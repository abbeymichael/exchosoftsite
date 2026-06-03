<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResellerCommission extends Model
{
    use HasUuids;

    protected $fillable = [
        'reseller_id',
        'order_id',
        'license_id',
        'batch_id',
        'payment_id',
        'payout_id',
        'type',
        'sale_amount',
        'commission_rate_snapshot',
        'commission_amount',
        'currency',
        'status',
        'approved_at',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'sale_amount'              => 'decimal:2',
        'commission_rate_snapshot' => 'decimal:2',
        'commission_amount'        => 'decimal:2',
        'approved_at'              => 'datetime',
        'paid_at'                  => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function reseller(): BelongsTo
    {
        return $this->belongsTo(Reseller::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(LicenseBatch::class, 'batch_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function payout(): BelongsTo
    {
        return $this->belongsTo(ResellerPayout::class, 'payout_id');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePending($q)  { return $q->where('status', 'pending'); }
    public function scopeApproved($q) { return $q->where('status', 'approved'); }
    public function scopePaid($q)     { return $q->where('status', 'paid'); }
}
