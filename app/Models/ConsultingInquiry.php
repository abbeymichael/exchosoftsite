<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultingInquiry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference', 'customer_user_id', 'name', 'email', 'phone', 'company',
        'inquiry_type', 'subject', 'description', 'budget_range', 'timeline',
        'services_interested', 'how_heard', 'status', 'admin_notes',
        'assigned_to', 'responded_at',
    ];

    protected $casts = [
        'services_interested' => 'array',
        'responded_at'        => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (ConsultingInquiry $inquiry) {
            if (!$inquiry->reference) {
                $inquiry->reference = 'INQ-' . strtoupper(substr(uniqid(), -6));
            }
        });
    }

    public function customerUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_user_id');
    }

    public function assignedAdmin(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public static function statusColors(): array
    {
        return [
            'new'       => 'cyan',
            'reviewing' => 'blue',
            'quoted'    => 'violet',
            'accepted'  => 'green',
            'declined'  => 'red',
            'completed' => 'emerald',
        ];
    }
}
