<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroBanner;
use App\Models\HeroSlide;
use App\Models\HeroVideo;
use Illuminate\Http\Request;

class BannerSettingsController extends Controller
{
    public function index()
    {
        return view('admin.banners.index', [
            'slides' => HeroSlide::orderBy('sort_order')->orderBy('id')->get(),
            'videos' => HeroVideo::orderBy('sort_order')->orderBy('id')->get(),
            'interval' => HeroBanner::current()->autoplay_interval ?: 6,
        ]);
    }

    public function interval(Request $request)
    {
        $data = $request->validate(['autoplay_interval' => ['required', 'integer', 'min:2', 'max:30']]);

        $hero = HeroBanner::latest('id')->first() ?? new HeroBanner(['is_active' => true, 'brand_name' => 'ALBATECH HUB']);
        $hero->fill($data)->save();

        return back()->with('success', 'Slide interval updated.');
    }
}