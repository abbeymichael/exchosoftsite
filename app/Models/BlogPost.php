<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'author_id', 'title', 'slug', 'excerpt', 'content',
        'cover_image', 'category', 'tags', 'status',
        'published_at', 'views', 'read_time_minutes',
        'meta_title', 'meta_description', 'is_featured',
    ];

    protected $casts = [
        'tags'         => 'array',
        'published_at' => 'datetime',
        'is_featured'  => 'boolean',
    ];

    public function author(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
