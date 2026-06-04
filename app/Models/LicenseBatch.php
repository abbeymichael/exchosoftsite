<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class LicenseBatch extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'product_id',
        'reseller_id',
        'created_by',
        'label',
        'batch_code',
        'key_prefix',
        'quantity',
        'reseller_tag',
        'wholesale_price',
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
        'wholesale_price'   => 'decimal:2',
        'max_activations'   => 'integer',
        'duration_days'     => 'integer',
        'total_generated'   => 'integer',
        'total_used'        => 'integer',
        'total_revoked'     => 'integer',
        'expires_at'        => 'datetime',
        'metadata'          => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (LicenseBatch $batch) {
            if (empty($batch->uuid)) {
                $batch->uuid = (string) Str::uuid();
            }
        });
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function reseller(): BelongsTo
    {
        return $this->belongsTo(Reseller::class);
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

    public function commissions(): HasMany
    {
        return $this->hasMany(ResellerCommission::class, 'batch_id');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($q)  { return $q->where('status', 'active'); }
    public function scopeArchived($q){ return $q->where('status', 'archived'); }
}
