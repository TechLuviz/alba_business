<?php

namespace App\Http\Controllers;

use App\Models\PrivacyPolicy;
use App\Support\HtmlSanitizer;
use App\Support\TextRules;
use Illuminate\Http\Request;

/** Privacy Policy and Terms of Service pages (public) and their admin editors. */
class PrivacyController extends Controller
{
    public function show(string $type = 'privacy')
    {
        $policy = PrivacyPolicy::current($type);
        abort_unless($policy->exists && $policy->is_active, 404);

        return view('privacy', ['policy' => $policy]);
    }

    public function edit(string $type = 'privacy')
    {
        return view('admin.privacy.edit', ['policy' => PrivacyPolicy::current($type), 'type' => $type]);
    }

    public function update(Request $request, string $type = 'privacy')
    {
        $c = config('banner.privacy');

        $data = $request->validate([
            'title' => TextRules::line(true, $c['title_min'], $c['title_max'], $c['word_max']),
            'content' => [
                'required', 'string', 'max:' . ($c['content_max'] * 4),
                function (string $attribute, mixed $value, \Closure $fail) use ($c) {
                    $len = mb_strlen(HtmlSanitizer::text($value));
                    if ($len < $c['content_min']) {
                        $fail("The policy text must have at least {$c['content_min']} characters (currently {$len}).");
                    } elseif ($len > $c['content_max']) {
                        $fail("The policy text may not be longer than {$c['content_max']} characters (currently {$len}).");
                    }
                },
            ],
        ], TextRules::messages(['title']), ['title' => 'title', 'content' => 'policy text']);

        $data['content'] = HtmlSanitizer::clean($data['content']);
        $data['is_active'] = $request->boolean('is_active');

        PrivacyPolicy::current($type)->fill($data)->save();

        return back()->with('success', (PrivacyPolicy::TITLES[$type] ?? 'Page') . ' saved.');
    }
}
