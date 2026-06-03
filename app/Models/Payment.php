<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasUuids;

    protected $fillable = [
        'order_id',
        'user_id',
        'guest_email',
        'gateway',
        'gateway_transaction_id',
        'gateway_reference',
        'gateway_response',
        'amount',
        'fee',
        'net',
        'currency',
        'status',
        'refunded_amount',
        'refunded_at',
        'refund_reason',
        'paid_at',
        'metadata',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'fee'              => 'decimal:2',
        'net'              => 'decimal:2',
        'refunded_amount'  => 'decimal:2',
        'gateway_response' => 'array',
        'metadata'         => 'array',
        'paid_at'          => 'datetime',
        'refunded_at'      => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resellerCommissions(): HasMany
    {
        return $this->hasMany(ResellerCommission::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeCompleted($q)  { return $q->where('status', 'completed'); }
    public function scopePending($q)    { return $q->where('status', 'pending'); }
    public function scopeRefunded($q)   { return $q->where('status', 'refunded'); }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    public function getIsRefundedAttribute(): bool
    {
        return in_array($this->status, ['refunded', 'partially_refunded']);
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'completed'          => 'green',
            'pending'            => 'amber',
            'processing'         => 'blue',
            'failed'             => 'red',
            'refunded'           => 'slate',
            'partially_refunded' => 'orange',
            'disputed'           => 'violet',
            'cancelled'          => 'gray',
            default              => 'gray',
        };
    }
}
