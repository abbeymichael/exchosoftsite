<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ResellerPayout extends Model
{
    use hasUuids;

    protected $fillable = [
        'reseller_id', 'amount', 'currency',
        'period_from', 'period_to',
        'processed_by', 'paid_at', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'period_from' => 'date',
        'period_to' => 'date',
        'paid_at' => 'datetime',
    ];

    public function reseller()
    {
        return $this->belongsTo(Reseller::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
