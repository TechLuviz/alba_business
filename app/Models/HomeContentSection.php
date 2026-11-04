<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeContentSection extends Model
{
    protected $fillable = ['page', 'eyebrow', 'heading', 'content', 'image', 'image_position', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    /** The content section of a page ('home' or 'about'). */
    public static function current(string $page = 'home'): self
    {
        return static::where('page', $page)->latest('id')->first() ?? new static(['page' => $page, 'image_position' => 'right', 'is_active' => false]);
    }

    public function isVisible(): bool
    {
        return $this->is_active && filled($this->content);
    }
}
