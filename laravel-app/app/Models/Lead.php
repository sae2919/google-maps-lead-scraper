<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Lead extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'search_id', 
        'name', 
        'phone', 
        'email', 
        'website', 
        'address', 
        'main_area', 
        'pincode', 
        'maps_url', 
        'rating',
        'ai_metadata'
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
}