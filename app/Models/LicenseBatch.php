<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class LicenseBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'product_id',
        'created_by',
        'label',
        'batch_code',
        'key_prefix',
        'quantity',
        'reseller_tag',
        'license_type',
        'edition',
        'max_activations',
        'expires_at',
        'duration_days',
        'total_generated',
        'total_used',
        'total_revoked',
        'status',
        'metadata',
        'notes',
    ];

    protected $casts = [
        'expires_at'      => 'datetime',
        'metadata'        => 'array',
        'quantity'        => 'integer',
        'total_generated' => 'integer',
        'total_used'      => 'integer',
        'total_revoked'   => 'integer',
        'max_activations' => 'integer',
        'duration_days'   => 'integer',
    ];

    // ──────────────────────────────────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class, 'batch_id');
    }

    public function exports(): HasMany
    {
        return $this->hasMany(BatchExport::class, 'batch_id');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Stats helpers
    // ──────────────────────────────────────────────────────────────────────────

    public function getUsagePercentAttribute(): float
    {
        if ($this->total_generated === 0) {
            return 0;
        }

        return round(($this->total_used / $this->total_generated) * 100, 1);
    }

    public function syncCounts(): void
    {
        $this->update([
            'total_generated' => $this->licenses()->count(),
            'total_used'      => $this->licenses()->whereNotNull('customer_id')->count(),
            'total_revoked'   => $this->licenses()->where('status', 'revoked')->count(),
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Boot
    // ──────────────────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (LicenseBatch $batch) {
            if (empty($batch->uuid)) {
                $batch->uuid = (string) Str::uuid();
            }
            if (empty($batch->batch_code)) {
                $batch->batch_code = self::generateBatchCode();
            }
        });
    }

    public static function generateBatchCode(): string
    {
        return 'BATCH-' . strtoupper(date('Ymd')) . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }
}
