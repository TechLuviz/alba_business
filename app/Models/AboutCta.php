<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutCta extends Model
{
    protected $fillable = [
        'eyebrow', 'heading_line_1', 'heading_line_2', 'heading_accent', 'description',
        'image_main', 'image_small', 'image_caption', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public static function current(): self
    {
        return static::latest('id')->first() ?? new static(['is_active' => true]);
    }
}
