<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class License extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'product_id',
        'shop_product_id',
        'shop_order_id',
        'buyer_email',
        'buyer_name',
        'customer_id',
        'batch_id',
        'license_key',
        'key_prefix',
        'edition',
        'type',
        'max_activations',
        'current_activations',
        'issued_at',
        'activated_at',
        'last_seen_at',
        'status',
        'expires_at',
        'notes',
        // Customer / order fields
        'order_id',
        'transaction_id',
        'reseller_id',
        'support_tier',
        'grace_period_days',
        'is_renewable',
        'metadata',
        'suspended_at',
        'revoked_at',
        'first_activated_at',
        // Enterprise licensing fields (Phase 2)
        'features',
        'revocation_checksum',
        'min_app_version',
        'max_app_version',
    ];

    protected $casts = [
        'issued_at'          => 'datetime',
        'activated_at'       => 'datetime',
        'last_seen_at'       => 'datetime',
        'expires_at'          => 'datetime',
        'suspended_at'        => 'datetime',
        'revoked_at'          => 'datetime',
        'first_activated_at'  => 'datetime',
        'metadata'            => 'array',
        'features'            => 'array',
        'is_renewable'        => 'boolean',
        'grace_period_days'   => 'integer',
        'max_activations'     => 'integer',
        'current_activations' => 'integer',
    ];

    // ──────────────────────────────────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function shopProduct(): BelongsTo
    {
        return $this->belongsTo(ShopProduct::class);
    }

    public function shopOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'shop_order_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(LicenseBatch::class);
    }

    public function activations(): HasMany
    {
        return $this->hasMany(LicenseActivation::class);
    }

    public function activeActivations(): HasMany
    {
        return $this->hasMany(LicenseActivation::class)->where('status', 'active');
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    public function validationLogs(): HasMany
    {
        return $this->hasMany(ValidationLog::class);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Status helpers
    // ──────────────────────────────────────────────────────────────────────────

    public function isExpired(): bool
    {
        if (! $this->expires_at) {
            return false; // lifetime
        }

        // Respect grace period
        $graceCutoff = $this->expires_at->copy()->addDays($this->grace_period_days ?? 0);

        return $graceCutoff->isPast();
    }

    public function isInGracePeriod(): bool
    {
        return $this->expires_at
            && $this->expires_at->isPast()
            && ! $this->isExpired(); // within grace
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expires_at
            && $this->expires_at->isFuture()
            && $this->expires_at->diffInDays(now()) <= $days;
    }

    public function isValid(): bool
    {
        return in_array($this->status, ['active', 'trial'])
            && ! $this->isExpired();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Version-gating helper
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Check whether a given app version falls within this license's allowed range.
     * Falls back to product-level constraints when the license has no overrides.
     */
    public function isAppVersionAllowed(?string $appVersion): bool
    {
        if (! $appVersion) {
            return true; // no version submitted – pass-through
        }

        $min = $this->min_app_version ?? $this->product?->min_app_version;
        $max = $this->max_app_version ?? $this->product?->max_app_version;

        if ($min && version_compare($appVersion, $min, '<')) {
            return false;
        }

        if ($max && version_compare($appVersion, $max, '>')) {
            return false;
        }

        return true;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Revocation checksum
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Re-compute and persist the revocation checksum.
     * Called whenever status, revoked_at, or suspended_at changes.
     */
    public function refreshRevocationChecksum(): void
    {
        $raw = implode('|', [
            $this->uuid,
            $this->status,
            $this->revoked_at?->toISOString() ?? 'null',
            $this->suspended_at?->toISOString() ?? 'null',
        ]);

        $this->revocation_checksum = hash('sha256', $raw);
        $this->saveQuietly();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Computed badge color
    // ──────────────────────────────────────────────────────────────────────────

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'active'    => 'green',
            'expired'   => 'red',
            'suspended' => 'yellow',
            'revoked'   => 'red',
            'trial'     => 'blue',
            default     => 'gray',
        };
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Key generation
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Generate a cryptographically secure license key.
     * Format: {PREFIX}-XXXX-XXXX-XXXX (all uppercase hex-like chars)
     */
    public static function generateKey(string $prefix = 'EXCL'): string
    {
        $prefix   = strtoupper(substr(preg_replace('/[^A-Z0-9]/', '', $prefix), 0, 8));
        $segments = [];

        for ($i = 0; $i < 3; $i++) {
            // 4 uppercase alphanumeric chars from random bytes
            $raw        = random_bytes(4);
            $segments[] = strtoupper(substr(base_convert(bin2hex($raw), 16, 36), 0, 4));
        }

        return $prefix . '-' . implode('-', $segments);
    }

    /**
     * Generate a unique key that doesn't collide with existing keys.
     */
    public static function generateUniqueKey(string $prefix = 'EXCL', int $maxAttempts = 10): string
    {
        for ($i = 0; $i < $maxAttempts; $i++) {
            $key = self::generateKey($prefix);
            if (! self::where('license_key', $key)->exists()) {
                return $key;
            }
        }

        throw new \RuntimeException('Unable to generate a unique license key after ' . $maxAttempts . ' attempts.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Lifecycle
    // ──────────────────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        // Auto-assign UUID on creation
        static::creating(function (License $license) {
            if (empty($license->uuid)) {
                $license->uuid = (string) Str::uuid();
            }

            if (empty($license->license_key)) {
                $prefix = $license->key_prefix ?? 'EXCL';
                $license->license_key = self::generateUniqueKey($prefix);
            } else {
                $license->license_key = strtoupper($license->license_key);
            }
        });

        // Compute initial revocation checksum after creation
        static::created(function (License $license) {
            $license->refreshRevocationChecksum();
        });

        // Refresh revocation checksum whenever status, revoked_at or suspended_at changes
        static::updated(function (License $license) {
            if ($license->wasChanged(['status', 'revoked_at', 'suspended_at'])) {
                $license->refreshRevocationChecksum();
            }
        });
    }
}
