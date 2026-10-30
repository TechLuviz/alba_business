<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroBanner extends Model
{
    protected $fillable = [
        'brand_name', 'brand_tagline', 'brand_logo',
        'heading_line_1', 'heading_line_2', 'heading_accent',
        'description', 'button_text', 'button_url',
        'autoplay_interval', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public static function current(): self
    {
        return static::where('is_active', true)->latest('id')->first() ?? new static();
    }
}