<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AudienceCard;
use App\Support\BannerRules;
use App\Support\Uploads;
use Illuminate\Http\Request;

class AudienceCardController extends Controller
{
    /** Letters, digits, whitespace, and only standard safe punctuation: & , . ' " - ? ! ( ) : ; / */
    private const TEXT_PATTERN = '/^[A-Za-z0-9\s&,.\'"\-?!():;\/]+$/D';

    public function store(Request $request)
    {
        $data = $this->validated($request, true);

        if ($request->hasFile('image')) {
            $data['image'] = Uploads::store($request->file('image'), 'audience');
        }

        AudienceCard::create($data);

        return redirect()->route('admin.audience.index')->with('success', 'Audience card created successfully.');
    }

    public function update(Request $request, AudienceCard $card)
    {
        $data = $this->validated($request, false);

        if ($request->hasFile('image')) {
            Uploads::delete($card->image);
            $data['image'] = Uploads::store($request->file('image'), 'audience');
        } elseif ($request->boolean('remove_image')) {
            Uploads::delete($card->image);
            $data['image'] = null;
        }

        $card->update($data);

        return redirect()->route('admin.audience.index')->with('success', 'Audience card updated successfully.');
    }

    public function destroy(AudienceCard $card)
    {
        Uploads::delete($card->image);
        $card->delete();

        return redirect()->route('admin.audience.index')->with('success', 'Audience card deleted successfully.');
    }

    public function toggle(Request $request, AudienceCard $card)
    {
        $card->update(['is_active' => $request->boolean('is_active')]);

        return response()->json(['is_active' => $card->is_active]);
    }

    public function toggleAll(Request $request)
    {
        $active = $request->boolean('is_active');
        AudienceCard::query()->update(['is_active' => $active]);

        return response()->json(['is_active' => $active]);
    }

    private function validated(Request $request, bool $creating): array
    {
        $c = config('banner.audience_card');
        $img = $c['image'];

        $request->merge([
            'title' => $this->clean($request->input('title')),
            'description' => $this->clean($request->input('description')),
            'image_alt' => $this->clean($request->input('image_alt')),
        ]);

        $textMsg = 'The :attribute may only contain English letters, numbers, spaces and standard punctuation & , . \' " - ? ! ( ) : ; / (no emojis, unicode, or special characters).';

        $rules = [
            'title' => ['required', 'string', "min:{$c['title_min']}", "max:{$c['title_max']}", 'regex:' . self::TEXT_PATTERN],
            'description' => ['nullable', 'string', "max:{$c['description_max']}", 'regex:' . self::TEXT_PATTERN],
            'image_alt' => ['nullable', 'string', "max:{$c['alt_max']}", 'regex:' . self::TEXT_PATTERN],
            'sort_order' => ['nullable', 'integer', 'min:0', "max:{$c['order_max']}"],
            'image' => [
                $creating ? 'required' : 'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                "max:{$img['max_kb']}",
                "dimensions:min_width={$img['min_width']},min_height={$img['min_height']},max_width={$img['max_width']},max_height={$img['max_height']}",
            ],
        ];

        $messages = [
            'title.regex' => $textMsg,
            'description.regex' => $textMsg,
            'image_alt.regex' => $textMsg,
            'image.dimensions' => "The image must be between {$img['min_width']}x{$img['min_height']} and {$img['max_width']}x{$img['max_height']} pixels.",
            'image.max' => 'The image may not be larger than ' . round($img['max_kb'] / 1024) . ' MB.',
            'image.mimes' => 'The image must be a file of type: jpg, jpeg, png, webp.',
        ];

        $attributes = [
            'title' => 'card title',
            'description' => 'card description',
            'image' => 'card image',
            'image_alt' => 'image alt text',
            'sort_order' => 'display order',
        ];

        $data = $request->validate($rules, $messages, $attributes);

        unset($data['image']);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function clean(?string $v): ?string
    {
        if ($v === null) return null;
        $v = trim($v);
        return $v === '' ? null : $v;
    }
}
