<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PortfolioItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'description', 'content',
        'cover_image', 'gallery', 'category', 'tech_stack',
        'client_name', 'client_industry', 'project_url', 'github_url',
        'completed_at', 'duration', 'highlights', 'status',
        'is_featured', 'sort_order',
    ];

    protected $casts = [
        'gallery'      => 'array',
        'tech_stack'   => 'array',
        'highlights'   => 'array',
        'completed_at' => 'date',
        'is_featured'  => 'boolean',
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
