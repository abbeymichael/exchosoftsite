<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WhitePaper extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'author_id', 'title', 'slug', 'summary', 'content',
        'cover_image', 'file_path', 'category', 'tags',
        'shop_product_id', 'status', 'is_gated',
        'published_at', 'downloads', 'views',
        'meta_title', 'meta_description',
    ];

    protected $casts = [
        'tags'         => 'array',
        'is_gated'     => 'boolean',
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
}
