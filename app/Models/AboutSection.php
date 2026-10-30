<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutSection extends Model
{
    protected $fillable = [
        'eyebrow', 'heading', 'description', 'button_text', 'button_url',
        'primary_image', 'primary_alt', 'secondary_image', 'secondary_alt',
        'image_position', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public static function current(): self
    {
        return static::latest('id')->first() ?? new static(['is_active' => true, 'image_position' => 'left']);
    }

    public function isVisible(): bool
    {
        return $this->is_active && (filled($this->heading) || filled($this->description) || filled($this->eyebrow));
    }
}
