<?php

namespace App\Models;

use App\Support\RichText;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $status
 * @property array<int, array{type: string, data: array<string, string>}> $sections
 */
class Page extends Model
{
    protected $fillable = ['title', 'slug', 'status', 'sections'];

    /** @param array<int, array{type: string, data: array<string, string>}> $sections */
    public function setSectionsAttribute(array $sections): void
    {
        foreach ($sections as &$section) {
            if ($section['type'] === 'rich_text' && is_string($section['data']['body'] ?? null)) {
                $section['data']['body'] = RichText::clean($section['data']['body']);
            }
        }

        $this->attributes['sections'] = json_encode($sections, JSON_THROW_ON_ERROR);
    }

    protected function casts(): array
    {
        return ['sections' => 'array'];
    }
}
