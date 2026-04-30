<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneratedSite extends Model
{
    protected $fillable = [
        'slug',
        'business_name',
        'category',
        'city',
        'phone',
        'address',
        'html_content',
        'pexels_images',
        'metadata',
        'view_count',
    ];

    protected $casts = [
        'pexels_images' => 'array',
        'metadata'      => 'array',
    ];

    /**
     * Increment view count when site is visited.
     */
    public function incrementViews(): void
    {
        $this->increment('view_count');
    }
}