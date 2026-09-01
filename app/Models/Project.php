<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'language',
        'framework',
        'integrations',
        'stack_config',
        'status',
        'project_data',
        'html',
        'css',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'integrations'  => 'array',
            'stack_config'  => 'array',
            'project_data'  => 'array',
            'published_at'  => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published' && $this->published_at !== null;
    }

    public function stackLabel(): string
    {
        $tech = config('technologies');

        $lang = collect($tech['languages'] ?? [])
            ->firstWhere('id', $this->language);

        $fw = null;
        if ($this->framework && $this->language) {
            $fw = collect($tech['frameworks'][$this->language] ?? [])
                ->firstWhere('id', $this->framework);
        }

        $parts = array_filter([
            $lang['label'] ?? null,
            $fw['label'] ?? null,
        ]);

        return implode(' + ', $parts) ?: 'No stack';
    }

    public function languageAbbr(): string
    {
        $lang = collect(config('technologies.languages', []))
            ->firstWhere('id', $this->language);

        return $lang['abbr'] ?? strtoupper(substr($this->language ?? '?', 0, 2));
    }

    public function languageColor(): string
    {
        $lang = collect(config('technologies.languages', []))
            ->firstWhere('id', $this->language);

        return $lang['color'] ?? '#71717a';
    }
}
