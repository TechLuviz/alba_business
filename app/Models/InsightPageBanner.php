<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsightPageBanner extends Model
{
    protected $fillable = ['image', 'heading_line_1', 'heading_line_2', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public static function current(): self
    {
        return static::latest('id')->first() ?? new static(['is_active' => true]);
    }
}
