<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AudienceCard extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'image_alt',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
