<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AudienceSection extends Model
{
    protected $fillable = [
        'eyebrow',
        'heading',
        'highlight_text',
        'description',
        'heading_align',
        'bg_color',
        'heading_color',
        'highlight_color',
        'description_color',
        'accent_line_color',
        'padding_top',
        'padding_bottom',
        'autoplay',
        'autoplay_interval',
        'is_active',
    ];

    protected $casts = [
        'autoplay' => 'boolean',
        'is_active' => 'boolean',
        'padding_top' => 'integer',
        'padding_bottom' => 'integer',
        'autoplay_interval' => 'integer',
    ];

    public static function current(): self
    {
        return static::latest('id')->first() ?? new static([
            'eyebrow' => 'OUR EXPECTED AUDIENCE',
            'heading' => 'Who Is Alba Business School For?',
            'highlight_text' => 'Alba Business School',
            'description' => 'Built for ambitious minds ready to learn, build, lead, and create meaningful impact.',
            'heading_align' => 'left',
            'bg_color' => '#FBF9F5',
            'heading_color' => '#0A1628',
            'highlight_color' => '#C8914A',
            'description_color' => '#525866',
            'accent_line_color' => '#C8914A',
            'padding_top' => 90,
            'padding_bottom' => 90,
            'autoplay' => true,
            'autoplay_interval' => 5,
            'is_active' => true,
        ]);
    }

    public function isVisible(?\Illuminate\Support\Collection $cards = null): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($cards !== null && $cards->isEmpty()) {
            return false;
        }

        return filled($this->heading) || filled($this->description) || filled($this->eyebrow);
    }

    /**
     * Return formatted heading HTML with highlight text wrapped in a styled span.
     */
    public function formattedHeadingHtml(): string
    {
        $heading = e($this->heading ?? '');

        if (filled($this->highlight_text)) {
            $needle = preg_quote(e(trim($this->highlight_text)), '/');
            $color = e($this->highlight_color ?: '#C8914A');

            // Case-insensitive; keeps the heading's original casing. First match only.
            $result = preg_replace_callback(
                '/' . $needle . '/iu',
                fn ($m) => '<span class="audience-highlight" style="color: ' . $color . '">' . $m[0] . '</span>',
                $heading,
                1
            );

            if ($result !== null && $result !== $heading) {
                return $result;
            }

            // Highlight text isn't part of the heading: append it as the italic ending.
            return trim($heading . ' <span class="audience-highlight" style="color: ' . $color . '">' . e(trim($this->highlight_text)) . '</span>');
        }

        return $heading;
    }
}
