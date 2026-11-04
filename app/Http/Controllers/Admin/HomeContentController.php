<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeContentSection;
use App\Support\HtmlSanitizer;
use Illuminate\Http\Request;

/** Page Settings > Home Page > Content Section (shown after the Insights section): text only. */
class HomeContentController extends Controller
{
    public function edit(string $page = 'home')
    {
        return view('admin.home-content.edit', ['section' => HomeContentSection::current($page), 'page' => $page]);
    }

    public function update(Request $request, string $page = 'home')
    {
        $c = config('banner.home_content');

        $data = $request->validate([
            'content' => [
                'required', 'string', 'max:60000',
                function (string $attribute, mixed $value, \Closure $fail) use ($c) {
                    $len = count(preg_split('/\s+/u', HtmlSanitizer::text($value), -1, PREG_SPLIT_NO_EMPTY));
                    if ($len < $c['content_min']) {
                        $fail("The content must have at least {$c['content_min']} words (currently {$len}).");
                    } elseif ($len > $c['content_max']) {
                        $fail("The content may not be longer than {$c['content_max']} words (currently {$len}).");
                    }
                },
            ],
        ], [], ['content' => 'content']);

        $data['content'] = HtmlSanitizer::clean($data['content']);
        $data['is_active'] = $request->boolean('is_active');

        HomeContentSection::current($page)->fill($data)->save();

        return back()->with('success', 'Content section saved.');
    }
}
