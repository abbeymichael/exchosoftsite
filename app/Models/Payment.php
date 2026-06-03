<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Payment extends Model
{
    use hasUuids;

    protected $fillable = [
        'order_id', 'user_id', 'guest_email',
        'gateway', 'transaction_id', 'amount', 'currency', 'status',
        'paid_at', 'notes',
    ];
    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resellerCommissions()
    {
        return $this->hasMany(ResellerCommission::class);
    }

    public function payout()
    {
        return $this->hasOneThrough(ResellerPayout::class, ResellerCommission::class, 'payment_id', 'id', 'id', 'payout_id');
    }

    public function licenses()
    {
        return $this->hasManyThrough(License::class, OrderItem::class, 'order_id', 'id', 'order_id', 'license_id');
    }

    public function products()
    {
        return $this->hasManyThrough(Product::class, OrderItem::class, 'order_id', 'id', 'order_id', 'product_id');
    }

    ## scopes

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'successful');
    }

    ## Accessors

    public function getIsSuccessfulAttribute()
    {
        return $this->status === 'successful';
    }

    public function getIsFailedAttribute()
    {
        return $this->status === 'failed';
    }
}
