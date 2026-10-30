<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutStory extends Model
{
    protected $fillable = [
        'eyebrow', 'heading', 'heading_accent', 'paragraph_1', 'paragraph_2',
        'quote', 'person_name', 'person_role', 'image', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public static function current(): self
    {
        return static::latest('id')->first() ?? new static(['is_active' => true]);
    }
}
