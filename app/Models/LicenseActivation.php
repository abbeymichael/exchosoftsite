<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class LicenseActivation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'license_id',
        'device_id',
        'device_name',
        'hardware_id',
        'platform',
        'app_type',
        'ip_address',
        'fingerprint',
        'os',
        'app_version',
        'country',
        'activation_source',
        'status',
        'is_suspicious',
        'suspicious_reason',
        'activated_at',
        'last_seen_at',
        'deactivated_at',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'is_suspicious'  => 'boolean',
        'activated_at'   => 'datetime',
        'last_seen_at'   => 'datetime',
        'deactivated_at' => 'datetime',
        'expires_at'     => 'datetime',
        'metadata'       => 'array',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($q)     { return $q->where('status', 'active'); }
    public function scopeSuspicious($q) { return $q->where('is_suspicious', true); }
}
