<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuickContact extends Model
{
    protected $fillable = ['email', 'phone', 'whatsapp', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public static function current(): self
    {
        return static::latest('id')->first() ?? new static(['is_active' => true]);
    }

    /** Digits only (with a leading +), for tel: and wa.me links. */
    public static function digits(?string $number): string
    {
        return preg_replace('/[^0-9+]/', '', (string) $number);
    }
}
