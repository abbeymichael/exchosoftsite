<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DemoBooking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference', 'customer_user_id', 'name', 'email', 'phone', 'company', 'job_title',
        'shop_product_id', 'product_name', 'demo_type', 'preferred_date', 'preferred_time',
        'timezone', 'attendees', 'requirements', 'message',
        'status', 'confirmed_at', 'confirmed_date', 'confirmed_time',
        'meeting_link', 'admin_notes', 'assigned_to',
    ];

    protected $casts = [
        'preferred_date'  => 'date',
        'confirmed_date'  => 'date',
        'confirmed_at'    => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (DemoBooking $booking) {
            if (!$booking->reference) {
                $booking->reference = 'DEMO-' . strtoupper(substr(uniqid(), -6));
            }
        });
    }

    public function customerUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_user_id');
    }

    public function shopProduct(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ShopProduct::class);
    }

    public function assignedAdmin(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public static function statusColors(): array
    {
        return [
            'pending'     => 'amber',
            'confirmed'   => 'green',
            'rescheduled' => 'blue',
            'completed'   => 'emerald',
            'cancelled'   => 'red',
            'no_show'     => 'slate',
        ];
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeUpcoming($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed', 'rescheduled'])
            ->where('preferred_date', '>=', now()->toDateString());
    }
}
