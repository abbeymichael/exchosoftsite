<?php

namespace App\Models;

use App\Models\CaseStudy;
use App\Models\DemoBooking;
use App\Models\License;
use App\Models\LicenseActivation;
use App\Models\LicenseBatch;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\WhitePaper;
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
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [ 'name', 'slug', 'product_code', 'platform',
        'current_version', 'pricing_type', 'description', 'logo', 'is_active',
        // Licensing
        'app_identifier', 'secret_key', 'support_email', 'webhook_url',
        'max_devices', 'default_duration_days',
        'min_app_version', 'max_app_version', 'offline_ttl_hours', 'grace_period_days',
        'metadata', 'archived_at',
        // Shop
        'tagline', 'full_description', 'category', 'product_type',
        'price', 'sale_price', 'currency',
        'cover_image', 'gallery', 'features', 'tech_stack',
        'demo_url', 'documentation_url', 'download_url',
        'is_published', 'is_featured', 'sort_order', 'sales_count',
        'created_by',
    ];

    protected $casts = [
        'is_active'             => 'boolean',
        'is_published'          => 'boolean',
        'is_featured'           => 'boolean',
        'price'                 => 'decimal:2',
        'sale_price'            => 'decimal:2',
        'gallery'               => 'array',
        'features'              => 'array',
        'tech_stack'            => 'array',
        'metadata'              => 'array',
        'archived_at'           => 'datetime',
        'max_devices'           => 'integer',
        'default_duration_days' => 'integer',
        'offline_ttl_hours'     => 'integer',
        'grace_period_days'     => 'integer',
        'sort_order'            => 'integer',
        'sales_count'           => 'integer',
    ];

    // ── Boot ──────────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (Product $p) {
            $p->secret_key ??= bin2hex(random_bytes(32));
            $p->slug       ??= Str::slug($p->name);
        });
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function licenses(): HasMany         { return $this->hasMany(License::class); }
    public function activeLicenses(): HasMany   { return $this->hasMany(License::class)->where('status', 'active'); }
    public function batches(): HasMany          { return $this->hasMany(LicenseBatch::class); }
    public function orderItems(): HasMany       { return $this->hasMany(OrderItem::class); }
    public function demoBookings(): HasMany     { return $this->hasMany(DemoBooking::class); }
    public function whitepapers(): HasMany      { return $this->hasMany(WhitePaper::class); }
    public function caseStudies(): HasMany      { return $this->hasMany(CaseStudy::class); }
    public function createdBy(): BelongsTo      { return $this->belongsTo(User::class, 'created_by'); }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot(['quantity', 'unit_price', 'total'])
            ->withTimestamps();
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePublished($q)                     { return $q->where('is_published', true); }
    public function scopeFeatured($q)                      { return $q->where('is_featured', true); }
    public function scopeLicensable($q)                    { return $q->whereNotNull('app_identifier'); }
    public function scopeActive($q)                        { return $q->where('is_active', true)->whereNull('archived_at'); }
    public function scopeInCategory($q, string $category)  { return $q->where('category', $category); }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getEffectivePriceAttribute(): ?float { return $this->sale_price ?? $this->price; }
    public function getIsOnSaleAttribute(): bool         { return $this->sale_price !== null && $this->sale_price < $this->price; }
    public function getIsLicensableAttribute(): bool     { return $this->app_identifier !== null; }
    public function getIsArchivedAttribute(): bool       { return $this->archived_at !== null; }

    public function getDiscountPercentAttribute(): int
    {
        if (! $this->is_on_sale || ! $this->price) return 0;
        return (int) round((1 - $this->sale_price / $this->price) * 100);
    }

    public function getStatsAttribute(): array
    {
        return [
            'total_licenses'    => $this->licenses()->count(),
            'active_licenses'   => $this->activeLicenses()->count(),
            'total_activations' => LicenseActivation::whereHas('license', fn($q) => $q->where('product_id', $this->id))->where('status', 'active')->count(),
            'total_batches'     => $this->batches()->count(),
            'unused_licenses'   => $this->licenses()->whereNull('customer_id')->count(),
            'total_orders'      => $this->orderItems()->count(),
        ];
    }
}
