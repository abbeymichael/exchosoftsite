<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseStudy extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'author_id', 'title', 'slug', 'client_name', 'client_industry',
        'client_logo', 'cover_image', 'challenge', 'solution', 'results',
        'metrics', 'content', 'tags', 'shop_product_id',
        'status', 'published_at', 'views', 'is_featured',
        'meta_title', 'meta_description',
    ];

    protected $casts = [
        'metrics'      => 'array',
        'tags'         => 'array',
        'is_featured'  => 'boolean',
        'published_at' => 'datetime',
    ];

    public function author(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function shopProduct(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ShopProduct::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
