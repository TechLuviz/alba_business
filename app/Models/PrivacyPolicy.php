<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivacyPolicy extends Model
{
    protected $fillable = ['type', 'title', 'content', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public const TITLES = ['privacy' => 'Privacy Policy', 'terms' => 'Terms of Service'];

    /** The legal page of a type ('privacy' or 'terms'). */
    public static function current(string $type = 'privacy'): self
    {
        return static::where('type', $type)->latest('id')->first() ?? new static(['type' => $type, 'title' => self::TITLES[$type] ?? 'Legal', 'is_active' => true]);
    }
}
