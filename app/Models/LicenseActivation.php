<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LicenseActivation extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'license_id',
        'device_id',
        'hardware_id',
        'device_name',
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
        'last_seen_at',
        'deactivated_at',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'activated_at'   => 'datetime',
        'last_seen_at'   => 'datetime',
        'deactivated_at' => 'datetime',
        'expires_at'     => 'datetime',
        'is_suspicious'  => 'boolean',
        'metadata'       => 'array',
    ];

    // ──────────────────────────────────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────────────────────────────────

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────────

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'active'      => 'green',
            'deactivated' => 'gray',
            'revoked'     => 'red',
            default       => 'gray',
        };
    }

    /**
     * Build a device fingerprint hash from available identifiers.
     */
    public static function buildFingerprint(array $data): string
    {
        $parts = array_filter([
            $data['device_id']   ?? null,
            $data['hardware_id'] ?? null,
            $data['platform']    ?? null,
            $data['os']          ?? null,
        ]);

        return hash('sha256', implode('|', $parts));
    }

    /**
     * Detect if this activation looks suspicious based on rapid IP changes, etc.
     */
    public static function detectSuspicious(License $license, array $requestData): ?string
    {
        // Rule 1: same device_id re-activating with different IP too rapidly
        $recentActivation = self::where('license_id', $license->id)
            ->where('device_id', $requestData['device_id'] ?? '')
            ->where('status', 'active')
            ->where('ip_address', '!=', $requestData['ip'] ?? '')
            ->where('last_seen_at', '>', now()->subMinutes(5))
            ->first();

        if ($recentActivation) {
            return 'IP change within 5 minutes for the same device';
        }

        // Rule 2: too many activation attempts from same IP in short window
        $recentIpAttempts = ValidationLog::where('ip_address', $requestData['ip'] ?? '')
            ->where('created_at', '>', now()->subMinutes(10))
            ->count();

        if ($recentIpAttempts > 20) {
            return 'Excessive activation attempts from IP';
        }

        return null;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Lifecycle
    // ──────────────────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (LicenseActivation $activation) {
            if (empty($activation->uuid)) {
                $activation->uuid = (string) Str::uuid();
            }
        });
    }
}
