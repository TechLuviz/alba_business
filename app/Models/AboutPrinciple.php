<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutPrinciple extends Model
{
    protected $fillable = ['icon', 'label', 'heading', 'heading_accent', 'description', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
