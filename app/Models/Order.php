<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'customer_user_id',
        'reseller_id',
        'reseller_code_used',
        'commission_rate_snapshot',
        'guest_name', 'guest_email', 'guest_phone', 'guest_company',
        'subtotal', 'discount', 'tax', 'total', 'currency',
        'status', 'payment_status', 'payment_method',
        'payment_reference', 'payment_meta', 'paid_at',
        'fulfillment_status', 'fulfilled_at',
        'coupon_code', 'coupon_discount',
        'customer_note', 'admin_note',
    ];

    protected $casts = [
        'payment_meta'             => 'array',
        'paid_at'                  => 'datetime',
        'fulfilled_at'             => 'datetime',
        'subtotal'                 => 'decimal:2',
        'discount'                 => 'decimal:2',
        'tax'                      => 'decimal:2',
        'total'                    => 'decimal:2',
        'coupon_discount'          => 'decimal:2',
        'commission_rate_snapshot' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (!$order->order_number) {
                $order->order_number = 'ORD-' . strtoupper(substr(uniqid(), -8));
            }
        });
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function customerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_user_id');
    }

    public function reseller(): BelongsTo
    {
        return $this->belongsTo(Reseller::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(ResellerCommission::class);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getCustomerNameAttribute(): string
    {
        return $this->customerUser?->name ?? $this->guest_name ?? 'Guest';
    }

    public function getCustomerEmailAttribute(): string
    {
        return $this->customerUser?->email ?? $this->guest_email ?? '';
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->payment_status === 'paid';
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePaid($q)     { return $q->where('payment_status', 'paid'); }
    public function scopePending($q)  { return $q->where('status', 'pending'); }
    public function scopeCompleted($q){ return $q->where('status', 'completed'); }

    public static function statusColors(): array
    {
        return [
            'pending'    => 'amber',
            'paid'       => 'green',
            'processing' => 'blue',
            'completed'  => 'emerald',
            'cancelled'  => 'red',
            'refunded'   => 'slate',
        ];
    }
}
