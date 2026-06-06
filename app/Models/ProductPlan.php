<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ProductPlan extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'product_id',
        'name', 'slug', 'description',
        'price', 'sale_price', 'currency', 'form_factor',
        'duration_days',
        'trial_days', 'is_trial_eligible',
        'is_renewable', 'is_active', 'sort_order',
        'max_activations', 'offline_ttl_hours', 'grace_period_days',
    ];

    protected $casts = [
        'price'             => 'decimal:2',
        'sale_price'        => 'decimal:2',
        'duration_days'     => 'integer',
        'trial_days'        => 'integer',
        'is_trial_eligible' => 'boolean',
        'is_renewable'      => 'boolean',
        'is_active'         => 'boolean',
        'sort_order'        => 'integer',
        'max_activations'   => 'integer',
        'offline_ttl_hours' => 'integer',
        'grace_period_days' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (ProductPlan $plan) {
            $plan->slug ??= Str::slug($plan->name);
        });
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class, 'plan_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'plan_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($q)        { return $q->where('is_active', true); }
    public function scopeTrialEligible($q) { return $q->where('is_trial_eligible', true); }
    public function scopeOrdered($q)       { return $q->orderBy('sort_order')->orderBy('price'); }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getEffectivePriceAttribute(): float
    {
        return (float) ($this->sale_price ?? $this->price);
    }

    public function getIsOnSaleAttribute(): bool
    {
        return $this->sale_price !== null && (float) $this->sale_price < (float) $this->price;
    }

    public function getIsLifetimeAttribute(): bool
    {
        return $this->duration_days === 0;
    }

    public function getIsMonthlyAttribute(): bool
    {
        return $this->duration_days > 0 && $this->duration_days <= 31;
    }

    public function getIsYearlyAttribute(): bool
    {
        return $this->duration_days >= 365 && $this->duration_days < 3650;
    }

    public function getHasTrialAttribute(): bool
    {
        return $this->trial_days > 0;
    }

    public function getBillingLabelAttribute(): string
    {
        if ($this->is_lifetime) return 'Lifetime';
        if ($this->duration_days <= 31) return 'Monthly';
        if ($this->duration_days <= 93) return 'Quarterly';
        if ($this->duration_days <= 366) return 'Yearly';
        return "{$this->duration_days} days";
    }

    /**
     * Calculate expiry date when issuing a new license on this plan.
     */
    public function getExpiresAtForNewLicenseAttribute(): ?\Carbon\Carbon
    {
        if ($this->is_lifetime) {
            return null;
        }

        return now()->addDays($this->duration_days);
    }

    /**
     * Calculate renewal expiry from an existing license.
     * Extends from the license's current expiry if still active, otherwise from now.
     */
    public function renewalExpiresAt(License $license): ?\Carbon\Carbon
    {
        if ($this->is_lifetime) {
            return null;
        }

        $base = ($license->expires_at && $license->expires_at->isFuture())
            ? $license->expires_at
            : now();

        return $base->copy()->addDays($this->duration_days);
    }
}
