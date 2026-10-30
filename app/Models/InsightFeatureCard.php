<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsightFeatureCard extends Model
{
    protected $fillable = ['category', 'title', 'published_on', 'image', 'sort_order', 'is_active'];

    protected $casts = ['published_on' => 'date', 'is_active' => 'boolean'];
}
