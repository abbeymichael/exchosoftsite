<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    public $timestamps = false; // only created_at

    protected $fillable = [
        'user_id',
        'actor_type',
        'actor_label',
        'event',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'metadata',
        'ip_address',
        'user_agent',
        'request_id',
    ];

    protected $casts = [
        'old_values'  => 'array',
        'new_values'  => 'array',
        'metadata'    => 'array',
        'created_at'  => 'datetime',
    ];

    // ──────────────────────────────────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Factory helper
    // ──────────────────────────────────────────────────────────────────────────

    public static function record(
        string $event,
        ?Model $subject = null,
        array $newValues = [],
        array $oldValues = [],
        array $meta = []
    ): self {
        return self::create([
            'user_id'        => auth()->id(),
            'actor_type'     => auth()->check() ? 'user' : 'api_token',
            'actor_label'    => auth()->user()?->name ?? request()->bearerToken() ? 'API' : 'system',
            'event'          => $event,
            'auditable_type' => $subject ? get_class($subject) : null,
            'auditable_id'   => $subject?->getKey(),
            'old_values'     => $oldValues ?: null,
            'new_values'     => $newValues ?: null,
            'metadata'       => array_merge([
                'request_id' => request()->header('X-Request-Id'),
            ], $meta),
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
            'request_id'     => request()->header('X-Request-Id') ?? request()->header('X-Idempotency-Key'),
        ]);
    }
}
