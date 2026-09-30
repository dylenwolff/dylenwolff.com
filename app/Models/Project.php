<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'category', 'summary', 'url', 'accent', 'sort_order', 'is_published'])]
class Project extends Model
{
    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }
}
