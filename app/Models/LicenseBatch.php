<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class LicenseBatch extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'product_id',
        'plan_id',
        'reseller_id',
        'created_by',
        'label',
        'batch_code',
        'key_prefix',
        'quantity',
        'reseller_tag',
        'wholesale_price',
        'license_type',
        'edition',
        'max_activations',
        'expires_at',
        'duration_days',
        'total_generated',
        'total_used',
        'total_revoked',
        'status',
        'metadata',
        'notes',
    ];

    protected $casts = [
        'wholesale_price'   => 'decimal:2',
        'max_activations'   => 'integer',
        'duration_days'     => 'integer',
        'total_generated'   => 'integer',
        'total_used'        => 'integer',
        'total_revoked'     => 'integer',
        'expires_at'        => 'datetime',
        'metadata'          => 'array',
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

    public function reseller(): BelongsTo
    {
        return $this->belongsTo(Reseller::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class, 'batch_id');
    }

    public function exports(): HasMany
    {
        return $this->hasMany(BatchExport::class, 'batch_id');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(ResellerCommission::class, 'batch_id');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($q)  { return $q->where('status', 'active'); }
    public function scopeArchived($q){ return $q->where('status', 'archived'); }

    // ── Accessors ─────────────────────────────────────────────────────────────

    /**
     * Usage percentage (keys assigned vs generated).
     */
    public function getUsagePercentAttribute(): int
    {
        if ($this->total_generated === 0) {
            return 0;
        }

        return (int) round(($this->total_used / $this->total_generated) * 100);
    }

    /**
     * Number of keys still available (not used or revoked).
     */
    public function getAvailableKeysAttribute(): int
    {
        return max(0, $this->total_generated - $this->total_used - $this->total_revoked);
    }

    /**
     * Derive a human-readable billing label from the plan or the license_type field.
     */
    public function getBillingLabelAttribute(): string
    {
        if ($this->plan) {
            return $this->plan->billing_label;
        }

        return match ($this->license_type) {
            'lifetime' => 'Lifetime',
            'monthly'  => 'Monthly',
            'annual',
            'yearly'   => 'Yearly',
            'trial'    => 'Trial',
            'custom'   => 'Custom',
            default    => ucfirst($this->license_type ?? 'Unknown'),
        };
    }
}
