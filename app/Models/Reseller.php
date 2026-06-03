<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reseller extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_name',
        'reseller_code',
        'type',
        'commission_rate',
        'discount_rate',
        'status',
        'total_earned',
        'total_paid',
        'balance',
        'payout_method',
        'payout_details',
        'currency',
        'minimum_payout',
        'notes',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'discount_rate'   => 'decimal:2',
        'total_earned'    => 'decimal:2',
        'total_paid'      => 'decimal:2',
        'balance'         => 'decimal:2',
        'minimum_payout'  => 'decimal:2',
        'payout_details'  => 'array',
        'approved_at'     => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(LicenseBatch::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(ResellerCommission::class);
    }

    public function pendingCommissions(): HasMany
    {
        return $this->hasMany(ResellerCommission::class)->where('status', 'pending');
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(ResellerPayout::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($q)    { return $q->where('status', 'active'); }
    public function scopeReferral($q)  { return $q->whereIn('type', ['referral', 'both']); }
    public function scopeWholesale($q) { return $q->whereIn('type', ['wholesale', 'both']); }
    public function scopePending($q)   { return $q->where('status', 'pending'); }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getIsApprovedAttribute(): bool
    {
        return $this->status === 'active' && $this->approved_at !== null;
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->company_name ?? $this->user?->name ?? 'Unknown';
    }
}
