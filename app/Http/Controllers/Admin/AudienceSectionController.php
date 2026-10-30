<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AudienceCard;
use App\Models\AudienceSection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AudienceSectionController extends Controller
{
    /** Letters, digits, whitespace, and only standard safe punctuation: & , . ' " - ? ! ( ) : ; / */
    private const TEXT_PATTERN = '/^[A-Za-z0-9\s&,.\'"\-?!():;\/]+$/D';

    public function index()
    {
        $audience = AudienceSection::current();
        $cards = AudienceCard::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.audience.index', compact('audience', 'cards'));
    }

    public function updateSettings(Request $request)
    {
        $c = config('banner.audience');

        // Trim and collapse excessive spaces before validating
        $request->merge([
            'eyebrow' => $this->clean($request->input('eyebrow')),
            'heading' => $this->clean($request->input('heading')),
            'highlight_text' => $this->clean($request->input('highlight_text')),
            'description' => $this->clean($request->input('description')),
        ]);

        $textMsg = 'The :attribute may only contain English letters, numbers, spaces and standard punctuation & , . \' " - ? ! ( ) : ; / (no emojis, unicode, or special characters).';

        $data = $request->validate([
            'eyebrow' => ['nullable', 'string', "max:{$c['eyebrow_max']}", 'regex:' . self::TEXT_PATTERN],
            'heading' => ['required', 'string', "min:{$c['heading_min']}", "max:{$c['heading_max']}", 'regex:' . self::TEXT_PATTERN],
            'highlight_text' => ['nullable', 'string', "max:{$c['highlight_max']}", 'regex:' . self::TEXT_PATTERN],
            'description' => ['nullable', 'string', "min:{$c['description_min']}", "max:{$c['description_max']}", 'regex:' . self::TEXT_PATTERN],
            'heading_align' => ['required', Rule::in(['left', 'center', 'right'])],
            'bg_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{3,8})$/'],
            'heading_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{3,8})$/'],
            'highlight_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{3,8})$/'],
            'description_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{3,8})$/'],
            'accent_line_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{3,8})$/'],
            'padding_top' => ['required', 'integer', "min:{$c['padding_min']}", "max:{$c['padding_max']}"],
            'padding_bottom' => ['required', 'integer', "min:{$c['padding_min']}", "max:{$c['padding_max']}"],
            'autoplay_interval' => ['required', 'integer', "min:{$c['interval_min']}", "max:{$c['interval_max']}"],
        ], [
            'eyebrow.regex' => $textMsg,
            'heading.regex' => $textMsg,
            'highlight_text.regex' => $textMsg,
            'description.regex' => $textMsg,
            'bg_color.regex' => 'Background color must be a valid HEX color code (e.g. #FBF9F5).',
            'heading_color.regex' => 'Heading color must be a valid HEX color code (e.g. #0A1628).',
            'highlight_color.regex' => 'Highlight color must be a valid HEX color code (e.g. #C8914A).',
            'description_color.regex' => 'Description color must be a valid HEX color code (e.g. #525866).',
            'accent_line_color.regex' => 'Accent line color must be a valid HEX color code (e.g. #C8914A).',
        ], [
            'eyebrow' => 'eyebrow text',
            'heading' => 'main heading',
            'highlight_text' => 'highlighted heading text',
            'heading_align' => 'heading alignment',
            'bg_color' => 'background color',
            'heading_color' => 'heading color',
            'highlight_color' => 'highlight text color',
            'description_color' => 'description color',
            'accent_line_color' => 'accent line color',
            'padding_top' => 'top padding',
            'padding_bottom' => 'bottom padding',
            'autoplay_interval' => 'autoplay interval',
        ]);

        $data['autoplay'] = $request->boolean('autoplay');
        $data['is_active'] = $request->boolean('is_active');

        $audience = AudienceSection::current();
        $audience->fill($data)->save();

        return redirect()->route('admin.audience.index')->with('success', 'Target audience section settings saved successfully.');
    }

    private function clean(?string $v): ?string
    {
        if ($v === null) return null;
        $v = trim($v);
        return $v === '' ? null : $v;
    }
}
