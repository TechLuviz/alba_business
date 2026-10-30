<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramCard extends Model
{
    protected $fillable = [
        'title', 'features', 'duration', 'mode',
        'image', 'sort_order', 'is_featured', 'is_active',
    ];

    protected $casts = ['is_featured' => 'boolean', 'is_active' => 'boolean'];

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
