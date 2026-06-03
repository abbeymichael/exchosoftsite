<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ResellerCommission extends Model
{
    use HasUuids;

    protected $fillable = [
        'reseller_id', 'order_id', 'license_id', 'batch_id', 'payment_id', 'payout_id',
        'type', 'amount', 'currency', 'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function reseller()
    {
        return $this->belongsTo(Reseller::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function license()
    {
        return $this->belongsTo(License::class);
    }

    public function batch()
    {
        return $this->belongsTo(LicenseBatch::class, 'batch_id');
    }

    public function payout()
    {
        return $this->belongsTo(ResellerPayout::class, 'payout_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

}
