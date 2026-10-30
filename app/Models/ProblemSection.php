<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProblemSection extends Model
{
    public const CARDS = [1, 2, 3, 4];

    protected $fillable = [
        'eyebrow', 'heading', 'heading_accent', 'description',
        'card1_title', 'card1_image', 'card2_title', 'card2_image',
        'card3_title', 'card3_image', 'card4_title', 'card4_image',
        'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public static function current(): self
    {
        return static::latest('id')->first() ?? new static(['is_active' => true]);
    }

    public function isVisible(): bool
    {
        return $this->is_active && (filled($this->heading) || filled($this->heading_accent) || filled($this->description));
    }
}
