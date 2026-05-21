<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Lead extends Model
{
    protected $casts = [
    'rating' => 'float',
    'ai_metadata' => 'array',
];
    protected $fillable = [

    'name',

    'phone',

    'email',

    'website',

    'address',

    'rating',

    'reviews',

    'category',

    'main_area',

    'slug',

    'ai_metadata',

    'search_id'

];

    /**
     * RELATIONSHIP: A lead belongs to a specific search.
     */
    public function search()
    {
        return $this->belongsTo(Search::class);
    }

    /**
     * SCOPE: Filter leads that have no website.
     * Usage: Lead::noWebsite()->get();
     */
    public function scopeNoWebsite(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('website')
              ->orWhere('website', '-')
              ->orWhere('website', '');
        });
    }

    /**
     * SCOPE: Filter leads that have a valid website.
     * Usage: Lead::hasWebsite()->get();
     */
    public function scopeHasWebsite(Builder $query): Builder
    {
        return $query->whereNotNull('website')
                     ->where('website', '!=', '-')
                     ->where('website', '!=', '');
    }

    /**
     * SCOPE: Filter leads by a minimum rating.
     * Usage: Lead::highlyRated(4)->get();
     */
    public function scopeHighlyRated(Builder $query, $rating): Builder
    {
        return $query->where('rating', '>=', $rating);
    }
    public function hasWebsite(): bool
{
    return !empty($this->website) && $this->website !== '-';
}

public function getLeadScoreAttribute(): int
{
    $score = 0;

    if ($this->website) $score += 30;
    if ($this->phone) $score += 25;
    if ($this->email) $score += 25;
    if ($this->rating >= 4) $score += 20;

    return $score;
}

public function scopeHighQuality($query)
{
    return $query->whereNotNull('website')
                 ->whereNotNull('phone')
                 ->where('rating', '>=', 4);
}
}