<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'product_code',
        'platform',
        'current_version',
        'pricing_type',
        'description',
        'is_active',
        'logo',
        // Advanced licensing fields
        'app_identifier',
        'secret_key',
        'version',
        'support_email',
        'webhook_url',
        'max_devices',
        'default_duration_days',
        'metadata',
        'created_by',
        'archived_at',
        // Enterprise licensing fields (Phase 2)
        'min_app_version',
        'max_app_version',
        'offline_ttl_hours',
        'grace_period_days',
    ];

    protected $casts = [
        'is_active'            => 'boolean',
        'metadata'             => 'array',
        'archived_at'          => 'datetime',
        'max_devices'          => 'integer',
        'default_duration_days' => 'integer',
        'offline_ttl_hours'    => 'integer',
        'grace_period_days'    => 'integer',
    ];

    // ──────────────────────────────────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────────────────────────────────

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class);
    }

    public function activeLicenses(): HasMany
    {
        return $this->hasMany(License::class)->where('status', 'active');
    }

    public function batches(): HasMany
    {
        return $this->hasMany(LicenseBatch::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Statistics helpers
    // ──────────────────────────────────────────────────────────────────────────

    public function getStatsAttribute(): array
    {
        return [
            'total_licenses'      => $this->licenses()->count(),
            'active_licenses'     => $this->licenses()->where('status', 'active')->count(),
            'total_activations'   => LicenseActivation::whereHas(
                'license', fn ($q) => $q->where('product_id', $this->id)
            )->where('status', 'active')->count(),
            'total_batches'       => $this->batches()->count(),
            'unused_licenses'     => $this->licenses()->whereNull('customer_id')->count(),
        ];
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Attribute helpers
    // ──────────────────────────────────────────────────────────────────────────

    public function getPlatformBadgeColorAttribute(): string
    {
        return match ($this->platform) {
            'desktop'       => 'blue',
            'saas'          => 'green',
            'hybrid'        => 'purple',
            'offline-first' => 'orange',
            default         => 'gray',
        };
    }

    public function getIsArchivedAttribute(): bool
    {
        return $this->archived_at !== null;
    }

    /**
     * The effective offline TTL in hours (defaults to 168 = 7 days).
     */
    public function getOfflineTtlHoursEffectiveAttribute(): int
    {
        return $this->offline_ttl_hours ?? 168;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Security helpers
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Generate a cryptographically secure HMAC secret for this product.
     */
    public static function generateSecretKey(): string
    {
        return bin2hex(random_bytes(32)); // 64-char hex
    }

    /**
     * Boot: auto-generate uuid and secret_key on creation if not provided.
     */
    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->uuid)) {
                $product->uuid = (string) Str::uuid();
            }
            if (empty($product->secret_key)) {
                $product->secret_key = self::generateSecretKey();
            }
            if (empty($product->slug) && ! empty($product->name)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }
}
