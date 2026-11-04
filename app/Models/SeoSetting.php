<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoSetting extends Model
{
    /** Pages that have SEO settings: route name => label. */
    public const PAGES = [
        'home' => 'Home Page',
        'about' => 'About Page',
    ];

    protected $fillable = ['page', 'meta_title', 'meta_description', 'focus_keyword'];

    public static function forPage(string $page): self
    {
        return static::firstOrNew(['page' => $page]);
    }
}
