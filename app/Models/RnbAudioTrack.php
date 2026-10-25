<?php

namespace App\\Models;

use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Support\\Facades\\Storage;

class RnbAudioTrack extends Model
{
    protected $fillable = [
        'title', 'artist', 'audio_path', 'cover_path', 'cover_alt',
        'sort_order', 'is_published',
    ];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function getAudioUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->audio_path);
    }

    public function getCoverUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->cover_path);
    }
}
