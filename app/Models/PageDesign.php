<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageDesign extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'status',
        'project_data',
        'html',
        'css',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'project_data' => 'array',
            'published_at' => 'datetime',
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
}
