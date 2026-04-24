<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = ['search_id', 'name', 'phone', 'email','website', 'address', 'main_area', 'pincode','maps_url', 'rating'];

public function search()
{
    return $this->belongsTo(Search::class);
}
}