<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class LicenseActivation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'license_id',
        'device_id',
        'device_name',
        'hardware_id',
        'platform',
        'app_type',
        'ip_address',
        'fingerprint',
        'os',
        'app_version',
        'country',
        'activation_source',
        'status',
        'is_suspicious',
        'suspicious_reason',
        'activated_at',
        'issued_at',
        'last_seen_at',
        'deactivated_at',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'is_suspicious'  => 'boolean',
        'activated_at'   => 'datetime',
        'issued_at'      => 'datetime',
        'last_seen_at'   => 'datetime',
        'deactivated_at' => 'datetime',
        'expires_at'     => 'datetime',
        'metadata'       => 'array',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($q)     { return $q->where('status', 'active'); }
    public function scopeSuspicious($q) { return $q->where('is_suspicious', true); }
    public function scopeDeactivated($q) { return $q->where('status', 'deactivated'); }
    public function scopeRevoked($q) { return $q->where('status', 'revoked'); }

    // ── Activation Management ─────────────────────────────────────────────────

    /**
     * Deactivate this device (reversible - can be reactivated)
     */
    public function deactivate(): bool
    {
        return $this->update([
            'status' => 'deactivated',
            'deactivated_at' => now(),
        ]);
    }

    /**
     * Reactivate a previously deactivated device
     */
    public function reactivate(): bool
    {
        // Check if the license still allows this activation
        $license = $this->license;

        if (!$license) {
            return false;
        }

        // Don't allow reactivation if license is expired, revoked, or suspended
        if ($license->isExpired() || $license->status === 'revoked' || $license->status === 'suspended') {
            return false;
        }

        // Check if we can fit this device back in
        $activeCount = static::where('license_id', $license->id)
            ->where('status', 'active')
            ->where('device_id', '!=', $this->device_id)
            ->count();

        if ($activeCount >= $license->max_activations) {
            return false;
        }

        return $this->update([
            'status' => 'active',
            'deactivated_at' => null,
            'last_seen_at' => now(),
        ]);
    }

    /**
     * Permanently revoke this device (cannot be reactivated)
     */
    public function revoke(): bool
    {
        return $this->update(['status' => 'revoked']);
    }

    /**
     * Check if this activation can be reactivated
     */
    public function canReactivate(): bool
    {
        // Must be deactivated
        if ($this->status !== 'deactivated') {
            return false;
        }

        $license = $this->license;

        // License must exist and be valid
        if (!$license || $license->isExpired() || $license->status === 'revoked' || $license->status === 'suspended') {
            return false;
        }

        // Must have room for this device
        $activeCount = static::where('license_id', $license->id)
            ->where('status', 'active')
            ->where('device_id', '!=', $this->device_id)
            ->count();

        return $activeCount < $license->max_activations;
    }

    /**
     * Get reactivation error reason if it cannot be reactivated
     */
    public function getReactivationBlockReason(): ?string
    {
        if ($this->status !== 'deactivated') {
            return 'Device is not deactivated';
        }

        $license = $this->license;

        if (!$license) {
            return 'License not found';
        }

        if ($license->isExpired()) {
            return 'License has expired';
        }

        if ($license->status === 'revoked') {
            return 'License has been revoked';
        }

        if ($license->status === 'suspended') {
            return 'License is suspended';
        }

        $activeCount = static::where('license_id', $license->id)
            ->where('status', 'active')
            ->where('device_id', '!=', $this->device_id)
            ->count();

        if ($activeCount >= $license->max_activations) {
            return "License activation limit reached ({$activeCount}/{$license->max_activations})";
        }

        return null;
    }

    /**
     * Get count of active activations for the license
     */
    public static function getActiveCount(int $licenseId): int
    {
        return static::where('license_id', $licenseId)
            ->where('status', 'active')
            ->count();
    }

    /**
     * Get count of deactivated activations for the license
     */
    public static function getDeactivatedCount(int $licenseId): int
    {
        return static::where('license_id', $licenseId)
            ->where('status', 'deactivated')
            ->count();
    }

    // ── Suspicious Activity Detection ──────────────────────────────────────────

    public static function detectSuspicious(License $license, array $context): ?string
    {
        $deviceId = $context['device_id'] ?? null;
        $ip       = $context['ip']        ?? null;

        // 1. Same IP is already used by a different device on this license.
        if ($ip) {
            $ipConflict = static::where('license_id', $license->id)
                ->where('ip_address', $ip)
                ->where('status', 'active')
                ->when($deviceId, fn($q) => $q->where('device_id', '!=', $deviceId))
                ->exists();

            if ($ipConflict) {
                return 'ip_used_by_another_device';
            }
        }

        // 2. Same device_id is already active on a different license (seat sharing / key resale).
        if ($deviceId) {
            $deviceOnOtherLicense = static::where('device_id', $deviceId)
                ->where('license_id', '!=', $license->id)
                ->where('status', 'active')
                ->exists();

            if ($deviceOnOtherLicense) {
                return 'device_active_on_another_license';
            }
        }

        // 3. License has exceeded its allowed activation seats.
        $maxActivations = $license->max_activations ?? null;

        if ($maxActivations !== null) {
            $currentCount = static::where('license_id', $license->id)
                ->where('status', 'active')
                ->when($deviceId, fn($q) => $q->where('device_id', '!=', $deviceId))
                ->count();

            if ($currentCount >= $maxActivations) {
                return 'activation_limit_exceeded';
            }
        }

        // 4. Unusually high activation attempts from the same IP within the last hour.
        if ($ip) {
            $recentAttempts = static::where('ip_address', $ip)
                ->where('activated_at', '>=', now()->subHour())
                ->count();

            if ($recentAttempts >= 5) {
                return 'too_many_activations_from_ip';
            }
        }

        return null;
    }

    public static function buildFingerprint(array $attributes): string
    {
        $normalized = array_map(
            fn($v) => strtolower(trim((string) $v)),
            $attributes
        );

        ksort($normalized); // key order must never affect the output

        return hash('sha256', json_encode($normalized));
    }
}
