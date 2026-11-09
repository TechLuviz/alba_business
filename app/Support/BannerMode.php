<?php

namespace App\Support;

use App\Models\HeroSlide;
use App\Models\HeroVideo;

/**
 * Home banner rules: only one banner type (video or image slides) is active at a time,
 * and only one video can be active. Turning something on switches the others off.
 */
class BannerMode
{
    /** A video was activated: switch off every other video and all image slides. Returns how many of each were switched off. */
    public static function videoActivated(HeroVideo $video): array
    {
        return [
            'videos' => HeroVideo::where('id', '!=', $video->id)->where('is_active', true)->update(['is_active' => false]),
            'slides' => HeroSlide::where('is_active', true)->update(['is_active' => false]),
        ];
    }

    /** Image slides were activated: switch off all videos. Returns how many videos were switched off. */
    public static function slidesActivated(): int
    {
        return HeroVideo::where('is_active', true)->update(['is_active' => false]);
    }

    /**
     * Guard: the home page must always have one active banner type.
     * If nothing is active after a change, switch on the other type automatically
     * (or, if the other type has no banners, turn the same type back on).
     *
     * @param  string  $lastOff  'slides' or 'videos' - the type the user just switched off
     * @return string|null  'slides' / 'videos' if something was auto-enabled, else null
     */
    public static function ensureOneActive(string $lastOff): ?string
    {
        if (HeroSlide::where('is_active', true)->exists() || HeroVideo::where('is_active', true)->exists()) {
            return null;
        }

        $order = $lastOff === 'videos' ? ['slides', 'videos'] : ['videos', 'slides'];

        foreach ($order as $type) {
            if ($type === 'videos' && $first = HeroVideo::orderBy('sort_order')->orderBy('id')->first()) {
                $first->update(['is_active' => true]);
                return 'videos';
            }
            if ($type === 'slides' && HeroSlide::exists()) {
                HeroSlide::query()->update(['is_active' => true]);
                return 'slides';
            }
        }

        return null;
    }

    /** Human-readable note for an auto-enable result. */
    public static function autoMessage(?string $enabled): ?string
    {
        return match ($enabled) {
            'videos' => 'At least one banner must stay active, so the first video banner was switched on automatically.',
            'slides' => 'At least one banner must stay active, so the image banners were switched on automatically.',
            default => null,
        };
    }
}
