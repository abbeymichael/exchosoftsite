<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'product_code', 'platform',
        'current_version', 'description', 'logo', 'is_active',
        // Licensing
        'app_identifier', 'secret_key', 'support_email', 'webhook_url',
        'max_devices', 'default_duration_days',
        'min_app_version', 'max_app_version',
        'offline_ttl_hours', 'grace_period_days',
        'metadata', 'archived_at',
        // Shop
        'tagline', 'full_description', 'category', 'product_type',
        'currency', 'cover_image', 'gallery', 'features', 'tech_stack',
        'demo_url', 'documentation_url', 'download_url',
        'is_published', 'is_featured', 'sort_order', 'sales_count',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'gallery' => 'array',
        'features' => 'array',
        'tech_stack' => 'array',
        'metadata' => 'array',
        'archived_at' => 'datetime',
        'max_devices' => 'integer',
        'default_duration_days' => 'integer',
        'offline_ttl_hours' => 'integer',
        'grace_period_days' => 'integer',
        'sort_order' => 'integer',
        'sales_count' => 'integer',
    ];

    // ── Boot ──────────────────────────────────────────────────────────────────

    public function mandatesCoreOps(): bool
    {
        return $this->form_factor === 'lan_orchestrated';
    }

    public function isIsolatedStandalone(): bool
    {
        return $this->form_factor === 'standalone';
    }

    public function isHybridCloudSync(): bool
    {
        return $this->form_factor === 'hybrid_cloud';
    }

    protected static function booted(): void
    {
        static::creating(function (Product $p) {
            $p->secret_key ??= bin2hex(random_bytes(32));
            $p->slug ??= Str::slug($p->name);
        });
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function plans(): HasMany
    {
        return $this->hasMany(ProductPlan::class)->orderBy('sort_order')->orderBy('price');
    }

    public function activePlans(): HasMany
    {
        return $this->hasMany(ProductPlan::class)->where('is_active', true)->orderBy('sort_order');
    }

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

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function demoBookings(): HasMany
    {
        return $this->hasMany(DemoBooking::class);
    }

    public function whitepapers(): HasMany
    {
        return $this->hasMany(WhitePaper::class);
    }

    public function caseStudies(): HasMany
    {
        return $this->hasMany(CaseStudy::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot(['quantity', 'unit_price', 'total'])
            ->withTimestamps();
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePublished($q)
    {
        return $q->where('is_published', true);
    }

    public function scopeFeatured($q)
    {
        return $q->where('is_featured', true);
    }

    public function scopeLicensable($q)
    {
        return $q->whereNotNull('app_identifier');
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->whereNull('archived_at');
    }

    public function scopeInCategory($q, string $category)
    {
        return $q->where('category', $category);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getIsOnSaleAttribute(): bool
    {
        return false;
    } // pricing moved to plans

    public function getIsLicensableAttribute(): bool
    {
        return $this->app_identifier !== null;
    }

    public function getIsArchivedAttribute(): bool
    {
        return $this->archived_at !== null;
    }

    /**
     * Get the cheapest active plan price for display purposes.
     */
    public function getStartingPriceAttribute(): ?float
    {
        $plan = $this->activePlans->sortBy('price')->first();

        return $plan ? (float) ($plan->sale_price ?? $plan->price) : null;
    }

    public function getStatsAttribute(): array
    {
        return [
            'total_licenses' => $this->licenses()->count(),
            'active_licenses' => $this->activeLicenses()->count(),
            'total_activations' => LicenseActivation::whereHas('license', fn ($q) => $q->where('product_id', $this->id))
                ->where('status', 'active')->count(),
            'total_batches' => $this->batches()->count(),
            'unused_licenses' => $this->licenses()->whereNull('customer_id')->count(),
            'total_orders' => $this->orderItems()->count(),
            'total_plans' => $this->plans()->count(),
        ];
    }
}
