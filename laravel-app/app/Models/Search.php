<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Search extends Model
{
    protected $fillable = [
        'query',
        'user_id',
        'is_stopped',
        'is_paused'
    ];

    protected $casts = [
        'is_stopped' => 'boolean',
        'is_paused' => 'boolean',
    ];

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
}