<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShopProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'tagline', 'description', 'full_description',
        'category', 'product_type', 'price', 'sale_price', 'currency',
        'cover_image', 'gallery', 'features', 'tech_stack',
        'download_url', 'demo_url', 'documentation_url',
        'version', 'platform', 'is_published', 'is_featured',
        'requires_license', 'sort_order', 'sales_count', 'linked_product_code',
    ];

    protected $casts = [
        'gallery'       => 'array',
        'features'      => 'array',
        'tech_stack'    => 'array',
        'is_published'  => 'boolean',
        'is_featured'   => 'boolean',
        'requires_license' => 'boolean',
        'price'         => 'decimal:2',
        'sale_price'    => 'decimal:2',
    ];

    public function orders(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot(['quantity', 'unit_price', 'total'])
            ->withTimestamps();
    }

    public function orderItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function demoBookings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DemoBooking::class);
    }

    public function whitepapers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WhitePaper::class);
    }

    public function caseStudies(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CaseStudy::class);
    }

    public function getEffectivePriceAttribute(): float
    {
        return $this->sale_price ?? $this->price;
    }

    public function getIsOnSaleAttribute(): bool
    {
        return $this->sale_price !== null && $this->sale_price < $this->price;
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
