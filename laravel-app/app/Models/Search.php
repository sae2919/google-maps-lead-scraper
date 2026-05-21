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
    'started_at'   => 'datetime',
    'completed_at' => 'datetime',
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
    public function getCompletionPercentageAttribute(): float
{
    if ($this->total_places == 0) {
        return 0;
    }

    return round(($this->processed_count / $this->total_places) * 100, 2);
}

public function getStatusLabelAttribute(): string
{
    if ($this->is_stopped) {
        return 'Stopped';
    }

    if ($this->is_paused) {
        return 'Paused';
    }

    return 'Running';
}

public function scopeActive($query)
{
    return $query->where('is_stopped', false);
}

public function scopeCompleted($query)
{
    return $query->where('status', 'completed');
}
}