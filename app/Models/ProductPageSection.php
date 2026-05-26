<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPageSection extends Model
{
    protected $fillable = [
        'product_code', 'section_key', 'label', 'content', 'data',
        'type', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'data'      => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get a section by product_code + section_key.
     */
    public static function getSection(string $productCode, string $sectionKey): ?static
    {
        return static::where('product_code', $productCode)
            ->where('section_key', $sectionKey)
            ->first();
    }

    /**
     * Get all active sections for a product code.
     */
    public static function getForProduct(string $productCode): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('product_code', $productCode)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->keyBy('section_key');
    }

    /**
     * Upsert a section.
     */
    public static function upsertSection(string $productCode, string $sectionKey, array $data): static
    {
        return static::updateOrCreate(
            ['product_code' => $productCode, 'section_key' => $sectionKey],
            $data
        );
    }
}
