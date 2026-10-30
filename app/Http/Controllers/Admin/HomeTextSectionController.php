<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeTextSection;
use Illuminate\Http\Request;

class HomeTextSectionController extends Controller
{
    /** Letters, digits, single spaces between words, and only & , . ' - as punctuation. No unicode, emoji or other symbols. */
    private const PATTERN = '/^[A-Za-z0-9&,.\'\-]+(?: [A-Za-z0-9&,.\'\-]+)*$/D';

    public function edit()
    {
        return view('admin.home-text.edit', ['section' => HomeTextSection::current()]);
    }

    public function update(Request $request)
    {
        $c = config('banner.home_text');

        // Trim and collapse repeated spaces before validating.
        $request->merge([
            'heading_line_1' => $this->clean($request->input('heading_line_1')),
            'heading_line_2' => $this->clean($request->input('heading_line_2')),
        ]);

        $rules = fn (string $first) => [$first, 'string', "min:{$c['min']}", "max:{$c['max']}", 'regex:' . self::PATTERN,
            function ($attr, $value, $fail) use ($c) {
                foreach (explode(' ', (string) $value) as $word) {
                    if (mb_strlen($word) > $c['word_max']) {
                        $fail("Each word in the {$attr} may not be longer than {$c['word_max']} characters.");
                        return;
                    }
                }
            }];

        $data = $request->validate([
            'heading_line_1' => $rules('required'),
            'heading_line_2' => $rules('nullable'),
        ], [
            'regex' => 'The :attribute may only contain English letters, numbers, spaces and & , . \' - (no emojis, unicode or other special characters).',
        ], [
            'heading_line_1' => 'heading line 1',
            'heading_line_2' => 'heading line 2',
        ]);

        HomeTextSection::current()->fill($data)->save();

        return back()->with('success', 'Homepage content saved.');
    }

    private function clean(?string $v): ?string
    {
        $v = preg_replace('/ {2,}/', ' ', trim((string) $v));

        return $v === '' ? null : $v;
    }
}
