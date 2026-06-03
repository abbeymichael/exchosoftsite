<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class License extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'product_id',
        'customer_id',
        'plan_id',
        'reseller_id',
        'batch_id',
        'order_id',
        'issued_by',
        'license_key',
        'key_prefix',
        'edition',
        'type',
        'max_activations',
        'current_activations',
        'issued_at',
        'activated_at',
        'first_activated_at',
        'last_seen_at',
        'suspended_at',
        'revoked_at',
        'expires_at',
        'status',
        'features',
        'revocation_checksum',
        'min_app_version',
        'max_app_version',
        'grace_period_days',
        'is_renewable',
        'support_tier',
        'notes',
        'metadata',
        'uuid',
    ];

    protected $casts = [
        'issued_at'          => 'datetime',
        'activated_at'       => 'datetime',
        'first_activated_at' => 'datetime',
        'last_seen_at'       => 'datetime',
        'expires_at'         => 'datetime',
        'suspended_at'       => 'datetime',
        'revoked_at'         => 'datetime',
        'metadata'           => 'array',
        'features'           => 'array',
        'is_renewable'       => 'boolean',
        'grace_period_days'  => 'integer',
        'max_activations'    => 'integer',
        'current_activations'=> 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(ProductPlan::class, 'plan_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function reseller(): BelongsTo
    {
        return $this->belongsTo(Reseller::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(LicenseBatch::class, 'batch_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
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

    // ── Status helpers ────────────────────────────────────────────────────────

    public function isExpired(): bool
    {
        if (! $this->expires_at) {
            return false; // lifetime
        }

        $graceDays   = $this->grace_period_days
            ?? $this->plan?->grace_period_days
            ?? $this->product?->grace_period_days
            ?? 0;
        $graceCutoff = $this->expires_at->copy()->addDays($graceDays);

        return $graceCutoff->isPast();
    }

    public function isInGracePeriod(): bool
    {
        return $this->expires_at
            && $this->expires_at->isPast()
            && ! $this->isExpired();
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

    // ── Version-gating ────────────────────────────────────────────────────────

    public function isAppVersionAllowed(?string $appVersion): bool
    {
        if (! $appVersion) {
            return true;
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

    // ── Revocation checksum ───────────────────────────────────────────────────

    public function refreshRevocationChecksum(): void
    {
        $raw = implode('|', [
            $this->uuid ?? $this->id,
            $this->status,
            $this->revoked_at?->toISOString() ?? 'null',
            $this->suspended_at?->toISOString() ?? 'null',
        ]);

        $this->revocation_checksum = hash('sha256', $raw);
        $this->saveQuietly();
    }

    // ── Badge color ───────────────────────────────────────────────────────────

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

    // ── Key generation ────────────────────────────────────────────────────────

    public static function generateKey(string $prefix = 'EXCL'): string
    {
        $prefix   = strtoupper(substr(preg_replace('/[^A-Z0-9]/', '', $prefix), 0, 8));
        $segments = [];

        for ($i = 0; $i < 3; $i++) {
            $raw        = random_bytes(4);
            $segments[] = strtoupper(substr(base_convert(bin2hex($raw), 16, 36), 0, 4));
        }

        return $prefix . '-' . implode('-', $segments);
    }

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

    // ── Lifecycle ─────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
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

        static::created(function (License $license) {
            $license->refreshRevocationChecksum();
        });

        static::updated(function (License $license) {
            if ($license->wasChanged(['status', 'revoked_at', 'suspended_at'])) {
                $license->refreshRevocationChecksum();
            }
        });
    }
}
