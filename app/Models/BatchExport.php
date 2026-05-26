<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatchExport extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'exported_by',
        'filename',
        'format',
        'record_count',
        'storage_path',
        'expires_at',
    ];

    protected $casts = [
        'expires_at'   => 'datetime',
        'record_count' => 'integer',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(LicenseBatch::class, 'batch_id');
    }

    public function exportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'exported_by');
    }
}
