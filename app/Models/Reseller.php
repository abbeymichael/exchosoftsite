<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Reseller extends Model
{
    use hasUuids;
    protected $fillable = [
        'user_id', 'company_name', 'reseller_code', 'type', 'commission_rate', 'discount_rate',
        'status', 'total_earned', 'total_paid', 'balance',
        'payout_method', 'payout_details', 'currency', 'minimum_payout',
        'notes', 'approved_at', 'approved_by',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'discount_rate'   => 'decimal:2',
        'total_earned'   => 'decimal:2',
        'total_paid'     => 'decimal:2',
        'balance'        => 'decimal:2',
        'payout_details' => 'array',
        'approved_at'    => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commissions()
    {
        return $this->hasMany(ResellerCommission::class);
    }

    public function payouts()
    {
        return $this->hasMany(ResellerPayout::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    ## Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    public function scopeReferral($query)
    {
        return $query->whereIn('type', ['referral', 'both']);
    }
    public function scopeWholesale($query)
    {
        return $query->whereIn('type', ['wholesale', 'both']);
    }

    ## Accessors

    public function getIsApprovedAttribute()
    {
        return $this->status === 'active' && $this->approved_at !== null;
    }

}
