<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResellerPayout extends Model
{
    use HasUuids;

    protected $fillable = [
        'reseller_id',
        'amount',
        'currency',
        'method',
        'reference',
        'status',
        'period_from',
        'period_to',
        'processed_by',
        'paid_at',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'period_from' => 'date',
        'period_to'   => 'date',
        'paid_at'     => 'datetime',
        'metadata'    => 'array',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function reseller(): BelongsTo
    {
        return $this->belongsTo(Reseller::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(ResellerCommission::class, 'payout_id');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePending($q)   { return $q->where('status', 'pending'); }
    public function scopeCompleted($q) { return $q->where('status', 'completed'); }
}
