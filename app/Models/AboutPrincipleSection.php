<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutPrincipleSection extends Model
{
    protected $fillable = ['eyebrow', 'image', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public static function current(): self
    {
        return static::latest('id')->first() ?? new static(['is_active' => true]);
    }
}
