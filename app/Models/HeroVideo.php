<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroVideo extends Model
{
    protected $fillable = [
        'video', 'heading_line_1', 'heading_line_2', 'heading_accent',
        'description', 'button_text', 'button_url', 'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];
}