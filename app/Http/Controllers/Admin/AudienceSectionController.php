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
    private const TEXT_PATTERN = '/^[A-Za-z .,\'&!?-]+$/D';

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

        $textMsg = 'The :attribute may only contain letters, spaces and . , \' - & ! ? (no numbers, emojis, unicode or special characters).';

        $data = $request->validate([
            'eyebrow' => ['nullable', 'string', "max:{$c['eyebrow_max']}", 'regex:' . self::TEXT_PATTERN],
            'heading' => ['required', 'string', "min:{$c['heading_min']}", "max:{$c['heading_max']}", 'regex:' . self::TEXT_PATTERN],
            'highlight_text' => ['nullable', 'string', "max:{$c['highlight_max']}", 'regex:' . self::TEXT_PATTERN],
            'description' => ['nullable', 'string', "min:{$c['description_min']}", "max:{$c['description_max']}", 'regex:' . self::TEXT_PATTERN],
            'autoplay_interval' => ['required', 'integer', "min:{$c['interval_min']}", "max:{$c['interval_max']}"],
        ], [
            'eyebrow.regex' => $textMsg,
            'heading.regex' => $textMsg,
            'highlight_text.regex' => $textMsg,
            'description.regex' => $textMsg,
        ], [
            'eyebrow' => 'eyebrow text',
            'heading' => 'main heading',
            'highlight_text' => 'highlighted heading text',
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
        $v = trim(preg_replace('/\s+/', ' ', $v));
        return $v === '' ? null : $v;
    }
}
