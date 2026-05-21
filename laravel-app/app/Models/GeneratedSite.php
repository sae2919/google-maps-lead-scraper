<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    'ai_config',
    'generation_status',
    'generated_at',
    'view_count',
];

protected $casts = [
    'ai_config'      => 'array',
    'pexels_images'  => 'array',
    'metadata'       => 'array',
    'generated_at'   => 'datetime',
];

    // ── Relationships ──────────────────────────────────────
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    // ── Helpers ────────────────────────────────────────────
    public function isDone(): bool
    {
        return $this->generation_status === 'done';
    }

    public function isGenerating(): bool
    {
        return in_array($this->generation_status, ['pending', 'generating']);
    }

    public function isFailed(): bool
    {
        return $this->generation_status === 'failed';
    }

    public function getTheme(): array
    {
        return $this->ai_config['theme'] ?? [];
    }

    public function getContent(): array
    {
        return $this->ai_config['content'] ?? [];
    }

    public function getLayout(): array
    {
        return $this->ai_config['layout'] ?? [];
    }
}