<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProblemSection;
use App\Support\BannerRules;
use App\Support\Uploads;
use Illuminate\Http\Request;

class ProblemSectionController extends Controller
{
    public function edit()
    {
        return view('admin.problem.edit', ['problem' => ProblemSection::current()]);
    }

    public function update(Request $request)
    {
        $c = config('banner.problem');
        $i = $c['image'];
        $problem = ProblemSection::current();

        // Letters, spaces and . , ' - & ! ? only (no numbers); no leading or repeated spaces; no overlong words.
        $text = fn (int $wordMax) => [
            "regex:/^(?! )(?!.*  )[A-Za-z .,'&!?-]+$/",
            function (string $attribute, mixed $value, \Closure $fail) use ($wordMax) {
                foreach (preg_split('/\s+/', (string) $value) as $word) {
                    if (mb_strlen($word) > $wordMax) {
                        $fail("Words in the :attribute may not be longer than {$wordMax} characters.");

                        return;
                    }
                }
            },
        ];

        $rules = [
            'eyebrow' => ['required', 'string', "min:{$c['eyebrow_min']}", "max:{$c['eyebrow_max']}", ...$text($c['word_max'])],
            'heading' => ['required', 'string', "min:{$c['heading_min']}", "max:{$c['heading_max']}", ...$text($c['word_max'])],
            'heading_accent' => ['required', 'string', "min:{$c['accent_min']}", "max:{$c['accent_max']}", ...$text($c['word_max'])],
            'description' => ['required', 'string', "min:{$c['description_min']}", "max:{$c['description_max']}", ...$text($c['description_word_max'])],
        ];
        $attributes = [
            'eyebrow' => 'section label',
            'heading' => 'heading',
            'heading_accent' => 'highlighted heading line',
            'description' => 'description',
        ];
        $messages = BannerRules::messages();

        foreach (ProblemSection::CARDS as $n) {
            $rules["card{$n}_title"] = ['required', 'string', "min:{$c['caption_min']}", "max:{$c['caption_max']}", ...$text($c['word_max'])];
            $rules["card{$n}_image"] = BannerRules::image(! $problem->{"card{$n}_image"} || $request->input("remove_card{$n}_image") === '1', 'problem.image');
            $attributes["card{$n}_title"] = "card {$n} caption";
            $attributes["card{$n}_image"] = "card {$n} image";
            $messages["card{$n}_image.dimensions"] = "Card {$n} image must be between {$i['min_width']}x{$i['min_height']} and {$i['max_width']}x{$i['max_height']} pixels.";
            $messages["card{$n}_image.max"] = "Card {$n} image may not be larger than " . round($i['max_kb'] / 1024) . ' MB.';
        }
        foreach (['eyebrow', 'heading', 'heading_accent', 'description', 'card1_title', 'card2_title', 'card3_title', 'card4_title'] as $field) {
            $messages["{$field}.regex"] = "The {$attributes[$field]} may only contain letters, spaces and . , ' - & ! ? (no numbers, emojis, special characters, leading or repeated spaces).";
        }

        $data = $request->validate($rules, $messages, $attributes);
        $data['is_active'] = $request->boolean('is_active');

        // Every image is mandatory: keep the saved one unless a replacement is uploaded.
        foreach (ProblemSection::CARDS as $n) {
            $field = "card{$n}_image";
            unset($data[$field]);
            if ($request->hasFile($field)) {
                Uploads::delete($problem->{$field});
                $data[$field] = Uploads::store($request->file($field), 'problem');
            }
        }

        $problem->fill($data)->save();

        return back()->with('success', 'Problem section saved.');
    }
}
