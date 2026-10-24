<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubSettings extends Model
{
    protected $fillable = ['key', 'data'];

    protected function casts(): array
    {
        return ['data' => 'array'];
    }

    public static function current(): array
    {
        $defaults = [
            'booking_title' => '', 'booking_text' => '', 'contacts' => [],
            'location_title' => '', 'address' => '', 'phone' => '',
            'contact_url' => '', 'facebook_url' => '', 'instagram_url' => '',
        ];
        $record = static::query()->where('key', 'club')->first();

        return array_replace($defaults, $record?->getAttribute('data') ?? []);
    }
}
