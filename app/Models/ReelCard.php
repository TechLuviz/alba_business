<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReelCard extends Model
{
    /** Social platforms a card may link to: host suffix => display name. */
    public const PLATFORMS = [
        'youtube.com' => 'YouTube', 'youtu.be' => 'YouTube',
        'instagram.com' => 'Instagram',
        'facebook.com' => 'Facebook', 'fb.watch' => 'Facebook',
        'linkedin.com' => 'LinkedIn',
        'x.com' => 'X', 'twitter.com' => 'X',
        'tiktok.com' => 'TikTok',
        'vimeo.com' => 'Vimeo',
    ];

    protected $fillable = ['tag', 'title', 'url', 'video', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    /** Platform name for a URL, or null when the host is not an allowed platform. */
    public static function platformFor(?string $url): ?string
    {
        $host = strtolower((string) parse_url((string) $url, PHP_URL_HOST));
        foreach (self::PLATFORMS as $suffix => $name) {
            if ($host === $suffix || str_ends_with($host, '.' . $suffix)) {
                return $name;
            }
        }

        return null;
    }

    public function platform(): ?string
    {
        return self::platformFor($this->url);
    }

    /** Embeddable video for the link: ['provider' => 'youtube'|'vimeo', 'id' => ..., 'embed' => url, 'thumb' => url|null], or null. */
    public function embed(): ?array
    {
        $url = (string) $this->url;
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = (string) parse_url($url, PHP_URL_PATH);
        parse_str((string) parse_url($url, PHP_URL_QUERY), $q);

        if (self::platformFor($url) === 'YouTube') {
            $id = null;
            if ($host === 'youtu.be' || str_ends_with($host, '.youtu.be')) {
                $id = trim($path, '/');
            } elseif (preg_match('#^/(?:shorts|embed|live|v)/([A-Za-z0-9_-]{11})#', $path, $m)) {
                $id = $m[1];
            } elseif (! empty($q['v'])) {
                $id = $q['v'];
            }
            if ($id && preg_match('/^[A-Za-z0-9_-]{11}$/', $id)) {
                return [
                    'provider' => 'youtube', 'id' => $id,
                    'embed' => "https://www.youtube-nocookie.com/embed/{$id}?autoplay=1&mute=1&controls=0&loop=1&playlist={$id}&playsinline=1&modestbranding=1&rel=0&iv_load_policy=3&disablekb=1",
                    'thumb' => "https://i.ytimg.com/vi/{$id}/hqdefault.jpg",
                ];
            }
        }

        if (self::platformFor($url) === 'Vimeo' && preg_match('#/(\d{5,})#', $path, $m)) {
            return [
                'provider' => 'vimeo', 'id' => $m[1],
                'embed' => "https://player.vimeo.com/video/{$m[1]}?autoplay=1&muted=1&loop=1&background=1&playsinline=1",
                'thumb' => null,
            ];
        }

        return null;
    }
}
