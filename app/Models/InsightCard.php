<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsightCard extends Model
{
    protected $fillable = ['title', 'description', 'image', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
