<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArchiveYear extends Model
{
    use SoftDeletes;

    protected $fillable = ['year', 'description', 'bands', 'is_published'];

    protected function casts(): array
    {
        return ['year' => 'integer', 'bands' => 'array', 'is_published' => 'boolean'];
    }
}
