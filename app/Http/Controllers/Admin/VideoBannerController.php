<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroVideo;
use App\Support\BannerMode;
use App\Support\BannerRules;
use App\Support\Uploads;
use Illuminate\Http\Request;

class VideoBannerController extends Controller
{
    public function store(Request $request)
    {
        $data = $this->validated($request, true);
        $data['video'] = Uploads::store($request->file('video'), 'video');
        $video = HeroVideo::create($data);
        if ($video->is_active) {
            BannerMode::videoActivated($video);
        }
        BannerMode::ensureOneActive('videos');

        return redirect()->route('admin.banners.index')->with('success', 'Video banner created.');
    }

    public function update(Request $request, HeroVideo $video)
    {
        $data = $this->validated($request, false);

        if ($request->hasFile('video')) {
            Uploads::delete($video->video);
            $data['video'] = Uploads::store($request->file('video'), 'video');
        }

        $video->update($data);
        if ($video->is_active) {
            BannerMode::videoActivated($video);
        }
        $auto = BannerMode::ensureOneActive('videos');

        return redirect()->route('admin.banners.index')->with('success', 'Video banner updated.' . ($auto ? ' ' . BannerMode::autoMessage($auto) : ''));
    }

    public function destroy(HeroVideo $video)
    {
        Uploads::delete($video->video);
        $video->delete();
        $auto = BannerMode::ensureOneActive('videos');

        return redirect()->route('admin.banners.index')->with('success', 'Video banner deleted.' . ($auto ? ' ' . BannerMode::autoMessage($auto) : ''));
    }

    public function toggle(Request $request, HeroVideo $video)
    {
        $video->update(['is_active' => $request->boolean('is_active')]);
        $off = $video->is_active ? BannerMode::videoActivated($video) : ['videos' => 0, 'slides' => 0];
        $auto = $video->is_active ? null : BannerMode::ensureOneActive('videos');

        $message = BannerMode::autoMessage($auto);
        if ($off['videos'] && $off['slides']) {
            $message = 'Video banner activated. The other video and the image banners were switched off, because only one video and one banner type can be active at a time.';
        } elseif ($off['videos']) {
            $message = 'Video banner activated. The other video was switched off, because only one video can be active at a time.';
        } elseif ($off['slides']) {
            $message = 'Video banner activated. The image banners were switched off, because only one banner type can be active at a time.';
        }

        return response()->json(['is_active' => $video->fresh()->is_active, 'reload' => $message !== null, 'message' => $message]);
    }

    public function toggleAll(Request $request)
    {
        // Only one video may be active, so "on" activates just the first one.
        HeroVideo::query()->update(['is_active' => false]);
        if ($request->boolean('is_active') && $first = HeroVideo::orderBy('sort_order')->orderBy('id')->first()) {
            $first->update(['is_active' => true]);
            BannerMode::videoActivated($first);
        }

        $on = $request->boolean('is_active');
        $auto = $on ? null : BannerMode::ensureOneActive('videos');

        return response()->json([
            'reload' => true,
            'message' => $on
                ? 'Only one video can be active, so just the first video was activated and the image banners were switched off.'
                : (BannerMode::autoMessage($auto) ?? 'All video banners were switched off.'),
        ]);
    }

    private function validated(Request $request, bool $creating): array
    {
        $data = $request->validate(
            BannerRules::text(false) + ['video' => BannerRules::video($creating)],
            BannerRules::messages(),
            BannerRules::attributes()
        );

        unset($data['video']);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}