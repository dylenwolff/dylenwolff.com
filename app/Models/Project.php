<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title', 'category', 'summary', 'url', 'image_path', 'client', 'status',
    'challenge', 'solution', 'responsibilities', 'technologies', 'gallery', 'accent',
    'is_featured', 'sort_order', 'is_published',
])]
class Project extends Model
{
    protected function casts(): array
    {
        return [
            'technologies' => 'array',
            'gallery' => 'array',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }
}
