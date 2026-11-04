<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutSection;
use App\Support\BannerRules;
use App\Support\Uploads;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AboutSectionController extends Controller
{
    private const IMAGES = ['primary_image' => 'primary', 'secondary_image' => 'secondary'];

    public function edit()
    {
        return view('admin.about.edit', ['about' => AboutSection::current()]);
    }

    public function update(Request $request)
    {
        $c = config('banner.about');
        $about = AboutSection::current();

        // Letters, spaces and . , ' - & ! ? only: no numbers, symbols, emojis or control chars; no leading or repeated spaces.
        $line = "/^(?! )(?!.*  )[A-Za-z .,'&!?-]+$/";
        $paragraph = $line;

        $data = $request->validate([
            'eyebrow' => ['nullable', 'string', "max:{$c['eyebrow_max']}", "regex:$line"],
            'heading' => ['nullable', 'string', "min:{$c['heading_min']}", "max:{$c['heading_max']}", "regex:$line"],
            'description' => ['nullable', 'string', "min:{$c['description_min']}", "max:{$c['description_max']}", "regex:$paragraph"],
            'primary_image' => BannerRules::image(false, 'about_image'),
            'secondary_image' => BannerRules::image(false, 'about_image'),
            'image_position' => ['required', Rule::in(['left', 'right'])],
        ], BannerRules::messages() + [
            'eyebrow.regex' => BannerRules::TEXT_MESSAGE,
            'heading.regex' => BannerRules::TEXT_MESSAGE,
            'description.regex' => BannerRules::TEXT_MESSAGE,
            'primary_image.dimensions' => $this->dimensionMessage(),
            'secondary_image.dimensions' => $this->dimensionMessage(),
            'primary_image.max' => 'The primary image may not be larger than ' . round(config('banner.about_image.max_kb') / 1024) . ' MB.',
            'secondary_image.max' => 'The secondary image may not be larger than ' . round(config('banner.about_image.max_kb') / 1024) . ' MB.',
        ], [
            'eyebrow' => 'section label',
            'heading' => 'main heading',
            'primary_image' => 'primary image',
            'secondary_image' => 'secondary image',
            'image_position' => 'image position',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        // Keep the saved image unless a replacement is uploaded or removal is ticked.
        foreach (self::IMAGES as $field => $key) {
            unset($data[$field]);
            if ($request->hasFile($field)) {
                Uploads::delete($about->{$field});
                $data[$field] = Uploads::store($request->file($field), 'about');
            } elseif ($request->boolean("remove_{$key}")) {
                Uploads::delete($about->{$field});
                $data[$field] = null;
            }
        }

        $about->fill($data)->save();

        return back()->with('success', 'About section saved.');
    }

    private function dimensionMessage(): string
    {
        $i = config('banner.about_image');

        return "The image must be between {$i['min_width']}x{$i['min_height']} and {$i['max_width']}x{$i['max_height']} pixels.";
    }
}
