<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeTextSection extends Model
{
    protected $fillable = ['heading_line_1', 'heading_line_2'];

    public static function current(): self
    {
        return static::latest('id')->first() ?? new static();
    }

    public function hasContent(): bool
    {
        return filled($this->heading_line_1) || filled($this->heading_line_2);
    }
}
