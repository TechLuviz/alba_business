<?php

namespace App\Support;

/** Shared validation rules for hero banners (dynamic + video) and brand settings. */
class BannerRules
{
    public static function text(bool $headingRequired): array
    {
        $t = config('banner.text');

        // Input is trimmed by Laravel's TrimStrings middleware (whitespace-only becomes empty).
        // Printable ASCII only: no emojis, unicode or control characters.
        $line = '/^[\x20-\x7E]+$/';
        $paragraph = '/^[\x20-\x7E\r\n]+$/';

        return [
            'heading_line_1' => [$headingRequired ? 'required' : 'nullable', 'string', "min:{$t['heading_min']}", "max:{$t['heading_max']}", "regex:$line"],
            'heading_line_2' => ['nullable', 'string', "min:{$t['heading_min']}", "max:{$t['heading_max']}", "regex:$line"],
            'heading_accent' => ['nullable', 'string', "min:{$t['accent_min']}", "max:{$t['accent_max']}", "regex:$line"],
            'description' => ['nullable', 'string', "min:{$t['description_min']}", "max:{$t['description_max']}", "regex:$paragraph"],
            'sort_order' => ['nullable', 'integer', 'min:0', "max:{$t['order_max']}"],
        ];
    }

    public static function image(bool $required, string $key = 'image'): array
    {
        $i = config("banner.{$key}");

        return [
            $required ? 'required' : 'nullable',
            'image',
            'mimes:jpg,jpeg,png,webp',
            "max:{$i['max_kb']}",
            "dimensions:min_width={$i['min_width']},min_height={$i['min_height']},max_width={$i['max_width']},max_height={$i['max_height']}",
        ];
    }

    public static function favicon(): array
    {
        return [
            'nullable',
            'file',
            'mimes:png,ico,jpg,jpeg,webp',
            'max:' . config('banner.favicon.max_kb'),
        ];
    }

    public static function video(bool $required): array
    {
        return [
            $required ? 'required' : 'nullable',
            'file',
            'mimes:mp4,webm,ogg,ogv',
            'mimetypes:video/mp4,video/webm,video/ogg,application/ogg',
            'min:1',
            'max:' . config('banner.video.max_kb'),
            // Content check (magic bytes) so renamed or corrupted files are rejected.
            function (string $attribute, mixed $value, \Closure $fail) {
                $head = is_object($value) && $value->isValid() ? (string) @file_get_contents($value->getRealPath(), false, null, 0, 12) : '';
                $ok = substr($head, 4, 4) === 'ftyp'
                    || str_starts_with($head, "\x1A\x45\xDF\xA3")
                    || str_starts_with($head, 'OggS');
                if (! $ok) {
                    $fail('The video file is empty, corrupted or not a real MP4, WebM or OGG file.');
                }
            },
        ];
    }

    public static function attributes(): array
    {
        return [
            'heading_line_1' => 'heading line 1',
            'heading_line_2' => 'heading line 2',
            'heading_accent' => 'heading accent',
            'sort_order' => 'display order',
            'brand_name' => 'brand name',
            'brand_tagline' => 'brand tagline',
            'brand_logo' => 'logo',
            'brand_favicon' => 'favicon',
        ];
    }

    public static function messages(): array
    {
        return [
            'heading_line_1.regex' => 'Heading line 1 may only contain standard keyboard characters (no emojis or special unicode).',
            'heading_line_2.regex' => 'Heading line 2 may only contain standard keyboard characters (no emojis or special unicode).',
            'heading_accent.regex' => 'The heading accent may only contain standard keyboard characters (no emojis or special unicode).',
            'description.regex' => 'The description may only contain standard keyboard characters (no emojis or special unicode).',
            'image.dimensions' => 'The image must be between ' . config('banner.image.min_width') . 'x' . config('banner.image.min_height')
                . ' and ' . config('banner.image.max_width') . 'x' . config('banner.image.max_height') . ' pixels.',
            'brand_logo.dimensions' => 'The logo must be between ' . config('banner.logo.min_width') . 'x' . config('banner.logo.min_height')
                . ' and ' . config('banner.logo.max_width') . 'x' . config('banner.logo.max_height') . ' pixels.',
            'image.max' => 'The image may not be larger than ' . round(config('banner.image.max_kb') / 1024) . ' MB.',
            'video.max' => 'The video may not be larger than ' . round(config('banner.video.max_kb') / 1024) . ' MB.',
            'video.mimetypes' => 'The video must be an MP4, WebM or OGG file.',
        ];
    }
}