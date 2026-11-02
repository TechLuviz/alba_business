<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramCard extends Model
{
    protected $fillable = [
        'title', 'features', 'duration', 'mode',
        'image', 'sort_order', 'is_featured', 'is_active',
        'banner_image', 'banner_title', 'banner_description', 'banner_levels', 'banner_timeline',
        'about_text', 'slug', 'levels', 'curriculum_points', 'tool_items', 'formats',
    ];

    protected $casts = ['is_featured' => 'boolean', 'is_active' => 'boolean', 'levels' => 'array', 'formats' => 'array', 'tool_items' => 'array'];

    /** Slug is created once from the title (kept stable afterwards so shared links keep working). */
    protected static function booted(): void
    {
        static::creating(function (self $card) {
            $base = \Illuminate\Support\Str::slug($card->title) ?: 'program';
            $slug = $base;
            for ($n = 2; static::where('slug', $slug)->exists(); $n++) {
                $slug = "{$base}-{$n}";
            }
            $card->slug = $slug;
        });
    }

    /** Curriculum points (used when a program has no levels), one per line. */
    public function curriculumPoints(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $this->curriculum_points)), 'strlen'));
    }

    /** One feature per line. */
    public function features(): array
    {
        return array_values(array_filter(array_map('trim', explode("\n", (string) $this->features)), 'strlen'));
    }

    /** Turn the Summernote HTML (a bullet list) into plain feature lines. */
    public static function featuresFromHtml(?string $html): array
    {
        $text = preg_replace('#</(li|p|div|h[1-6])>|<br\s*/?>#i', "\n", (string) $html);
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace("\xC2\xA0", ' ', $text); // non-breaking spaces from the editor

        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $text)), 'strlen'));
    }
}
