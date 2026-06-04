<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Customer extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'email',
        'company',
        'phone',
        'type',
        'notes',
        'is_active',
        // Extended CRM fields
        'country',
        'reseller_id',
        'external_id',
        'metadata',
        'archived_at',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'metadata'    => 'array',
        'archived_at' => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class);
    }

    public function activeLicenses(): HasMany
    {
        return $this->hasMany(License::class)->where('status', 'active');
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getDisplayNameAttribute(): string
    {
        return $this->company ? "{$this->name} ({$this->company})" : $this->name;
    }

    public function getIsArchivedAttribute(): bool
    {
        return $this->archived_at !== null;
    }




}
