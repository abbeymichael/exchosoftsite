<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ValidationLog extends Model
{
    public $timestamps = false; // only created_at column

    protected $fillable = [
        'license_key',
        'license_id',
        'product_slug',
        'action',
        'success',
        'failure_reason',
        'device_id',
        'ip_address',
        'app_version',
        'platform',
        'country',
        // Client-side replay-attack prevention
        'request_nonce',
        'request_timestamp',
        // Server-side replay-attack prevention & offline caching
        'response_nonce',
        'validation_source',
        'offline_valid_until',
    ];

    protected $casts = [
        'success'             => 'boolean',
        'created_at'          => 'datetime',
        'request_timestamp'   => 'datetime',
        'offline_valid_until' => 'datetime',
    ];

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    /**
     * Quick factory for logging validation attempts from the API controller.
     */
    public static function capture(array $data): self
    {
        return self::create([
            'license_key'         => strtoupper($data['license_key'] ?? ''),
            'license_id'          => $data['license_id'] ?? null,
            'product_slug'        => $data['product_slug'] ?? null,
            'action'              => $data['action'] ?? 'validate',
            'success'             => $data['success'] ?? false,
            'failure_reason'      => $data['failure_reason'] ?? null,
            'device_id'           => $data['device_id'] ?? null,
            'ip_address'          => request()->ip(),
            'app_version'         => $data['app_version'] ?? null,
            'platform'            => $data['platform'] ?? null,
            'country'             => $data['country'] ?? null,
            'request_nonce'       => $data['nonce'] ?? null,
            'request_timestamp'   => $data['timestamp'] ?? null,
            'response_nonce'      => $data['response_nonce'] ?? null,
            'validation_source'   => $data['validation_source'] ?? 'online',
            'offline_valid_until' => $data['offline_valid_until'] ?? null,
        ]);
    }
}
