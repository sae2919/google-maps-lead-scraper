<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Search extends Model
{
    protected $fillable = [
        'query',
        'user_id',
        'is_stopped',
        'is_paused',
        'total_places',
    ];

    protected $casts = [
        'is_stopped'   => 'boolean',
        'is_paused'    => 'boolean',
        'total_places' => 'integer',
    ];

    // ── RELATIONSHIPS ────────────────────────────────────────────────────────

    public function leads()
    {
        return $this->hasMany(Lead::class, 'search_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── SCOPES ───────────────────────────────────────────────────────────────

    public function scopeRunning($query)
    {
        return $query->where('is_stopped', false)->where('is_paused', false);
    }

    public function scopePaused($query)
    {
        return $query->where('is_paused', true);
    }

    public function scopeStopped($query)
    {
        return $query->where('is_stopped', true);
    }
}