<?php

namespace App\Models;

use App\Models\PageVersion;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    use HasUuids;
    protected $fillable = [
        'key', 'title',
        'banner_heading', 'banner_subheading', 'banner_image',
        'banner_cta_text', 'banner_cta_url',
        'meta_title', 'meta_description', 'meta_keywords', 'canonical_url',
        'og_title', 'og_description', 'og_image', 'og_type',
        'twitter_card', 'twitter_title', 'twitter_description', 'twitter_image',
        'schema_markup', 'extra', 'is_active',
    ];

    protected $casts = [
        'schema_markup' => 'array',
        'extra'         => 'array',
        'is_active'     => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function versions(): HasMany
    {
        return $this->hasMany(PageVersion::class)->latest();
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public static function forKey(string $key): self
    {
        return static::where('key', $key)->firstOrFail();
    }

    /**
     * Save current state as a version snapshot before updating.
     */
    public function snapshot(string $changedBy = null, string $note = null): void
    {
        $this->versions()->create([
            'snapshot'   => $this->toArray(),
            'changed_by' => $changedBy,
            'note'       => $note,
        ]);
    }

    /**
     * Restore a previous version.
     */
    public function restoreVersion(PageVersion $version): void
    {
        $data = collect($version->snapshot)
            ->except(['id', 'created_at', 'updated_at'])
            ->toArray();

        $this->snapshot(auth()->user()->name ?? 'system', 'Before restore to version #' . $version->id);
        $this->update($data);
    }

    // ── SEO fallback accessors ────────────────────────────────────────────────

    public function getResolvedOgTitleAttribute(): string
    {
        return $this->og_title ?? $this->meta_title ?? $this->title;
    }

    public function getResolvedOgDescriptionAttribute(): string
    {
        return $this->og_description ?? $this->meta_description ?? '';
    }

    public function getResolvedTwitterTitleAttribute(): string
    {
        return $this->twitter_title ?? $this->resolved_og_title;
    }

    public function getResolvedTwitterDescriptionAttribute(): string
    {
        return $this->twitter_description ?? $this->resolved_og_description;
    }
}
