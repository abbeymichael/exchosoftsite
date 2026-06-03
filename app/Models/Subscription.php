<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_id',
        'plan_id',
        'billing_cycle',
        'amount',
        'currency',
        'next_billing_date',
        'provider',
        'provider_reference',
        'status',
        'cancelled_at',
    ];

    protected $casts = [
        'amount'            => 'decimal:2',
        'next_billing_date' => 'datetime',
        'cancelled_at'      => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(ProductPlan::class, 'plan_id');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($q)    { return $q->where('status', 'active'); }
    public function scopeCancelled($q) { return $q->where('status', 'cancelled'); }
    public function scopePastDue($q)   { return $q->where('status', 'past_due'); }
}
