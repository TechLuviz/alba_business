<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use App\Support\BannerMode;
use App\Support\BannerRules;
use App\Support\Uploads;
use Illuminate\Http\Request;

class DynamicBannerController extends Controller
{
    public function store(Request $request)
    {
        $data = $this->validated($request, true);
        $data['image'] = Uploads::store($request->file('image'), 'hero');
        HeroSlide::create($data);
        if ($data['is_active']) {
            BannerMode::slidesActivated();
        }

        return redirect()->route('admin.banners.index')->with('success', 'Dynamic banner created.');
    }

    public function update(Request $request, HeroSlide $slide)
    {
        $data = $this->validated($request, false);

        if ($request->hasFile('image')) {
            Uploads::delete($slide->image);
            $data['image'] = Uploads::store($request->file('image'), 'hero');
        }

        $slide->update($data);
        if ($data['is_active']) {
            BannerMode::slidesActivated();
        }

        return redirect()->route('admin.banners.index')->with('success', 'Dynamic banner updated.');
    }

    public function destroy(HeroSlide $slide)
    {
        Uploads::delete($slide->image);
        $slide->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Dynamic banner deleted.');
    }

    public function toggle(Request $request, HeroSlide $slide)
    {
        $slide->update(['is_active' => $request->boolean('is_active')]);
        $videosOff = $slide->is_active ? BannerMode::slidesActivated() : 0;

        return response()->json([
            'is_active' => $slide->is_active,
            'reload' => $videosOff > 0,
            'message' => $videosOff ? 'Image banner activated. The active video banner was switched off, because only one banner type can be active at a time.' : null,
        ]);
    }

    public function toggleAll(Request $request)
    {
        $on = $request->boolean('is_active');
        HeroSlide::query()->update(['is_active' => $on]);
        $videosOff = $on ? BannerMode::slidesActivated() : 0;

        return response()->json([
            'is_active' => $on,
            'reload' => $videosOff > 0,
            'message' => $videosOff ? 'All image banners activated. The active video banner was switched off, because only one banner type can be active at a time.' : null,
        ]);
    }

    private function validated(Request $request, bool $creating): array
    {
        $data = $request->validate(
            BannerRules::text(true) + ['image' => BannerRules::image($creating)],
            BannerRules::messages(),
            BannerRules::attributes()
        );

        unset($data['image']);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}