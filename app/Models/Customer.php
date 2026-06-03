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

    // ── Note: customers table uses id() (bigint) + uuid column ─────────────
    // HasUuids is used only if id is uuid. Since our customers table uses $table->id()
    // plus a separate uuid column, we override the trait behaviour below.
    public function uniqueIds(): array
    {
        return []; // uuid is populated via booted(), id() remains auto-increment
    }

    protected static function booted(): void
    {
        static::creating(function (Customer $customer) {
            if (empty($customer->uuid)) {
                $customer->uuid = (string) Str::uuid();
            }
        });
    }
}
