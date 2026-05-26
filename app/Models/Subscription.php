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

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    public function isExpiringSoon(): bool
    {
        return $this->next_billing_date
            && $this->next_billing_date->isFuture()
            && $this->next_billing_date->diffInDays(now()) <= 7;
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'active'    => 'green',
            'cancelled' => 'red',
            'past_due'  => 'yellow',
            'trialing'  => 'blue',
            'paused'    => 'gray',
            default     => 'gray',
        };
    }
}
