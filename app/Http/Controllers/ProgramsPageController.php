<?php

namespace App\Http\Controllers;

use App\Models\ProgramCard;
use App\Models\ProgramPageBanner;
use App\Models\ProgramPoint;
use App\Models\ProgramSection;
use App\Support\BannerRules;
use App\Support\TextRules;
use App\Support\Uploads;
use Illuminate\Http\Request;

/** Programs listing page (public) and its admin settings (banner + highlight points). */
class ProgramsPageController extends Controller
{
    public function show()
    {
        return view('programs', [
            'banner' => ProgramPageBanner::current(),
            'points' => ProgramPoint::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get(),
            'programSection' => ProgramSection::current(),
            'programCards' => ProgramCard::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    /* ---------- Admin: Programs Page > Banner Settings ---------- */

    public function bannerEdit()
    {
        return view('admin.programs-page.banner', ['banner' => ProgramPageBanner::current()]);
    }

    public function bannerUpdate(Request $request)
    {
        $c = config('banner.program_page_banner');
        $banner = ProgramPageBanner::current();

        $data = $request->validate([
            'title' => TextRules::line(true, $c['title_min'], $c['title_max'], $c['word_max']),
            'description' => TextRules::line(true, $c['description_min'], $c['description_max'], $c['word_max']),
            'image' => BannerRules::image(! $banner->image, 'program_page_banner'),
        ], BannerRules::messages() + TextRules::messages(['title', 'description']) + [
            'image.dimensions' => "The image must be between {$c['min_width']}x{$c['min_height']} and {$c['max_width']}x{$c['max_height']} pixels.",
            'image.max' => 'The image may not be larger than ' . round($c['max_kb'] / 1024) . ' MB.',
        ], ['title' => 'title', 'description' => 'description', 'image' => 'banner image']);

        unset($data['image']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            Uploads::delete($banner->image);
            $data['image'] = Uploads::store($request->file('image'), 'program-page-banner');
        }

        $banner->fill($data)->save();

        return back()->with('success', 'Programs page banner saved.');
    }

    /* ---------- Admin: Programs Page > Highlight Points ---------- */

    public function pointsIndex()
    {
        return view('admin.programs-page.points', [
            'points' => ProgramPoint::orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function pointStore(Request $request)
    {
        $data = $this->pointValidated($request);
        if ($request->hasFile('icon')) {
            $data['icon'] = Uploads::store($request->file('icon'), 'program-point');
        }
        ProgramPoint::create($data);

        return redirect()->route('admin.programpage.points.index')->with('success', 'Point created.');
    }

    public function pointUpdate(Request $request, ProgramPoint $point)
    {
        $data = $this->pointValidated($request);

        if ($request->hasFile('icon')) {
            Uploads::delete($point->icon);
            $data['icon'] = Uploads::store($request->file('icon'), 'program-point');
        } elseif ($request->boolean('remove_icon')) {
            Uploads::delete($point->icon);
            $data['icon'] = null;
        }

        $point->update($data);

        return redirect()->route('admin.programpage.points.index')->with('success', 'Point updated.');
    }

    public function pointDestroy(ProgramPoint $point)
    {
        Uploads::delete($point->icon);
        $point->delete();

        return redirect()->route('admin.programpage.points.index')->with('success', 'Point deleted.');
    }

    public function pointToggle(Request $request, ProgramPoint $point)
    {
        $point->update(['is_active' => $request->boolean('is_active')]);

        return response()->json(['is_active' => $point->is_active]);
    }

    public function pointToggleAll(Request $request)
    {
        ProgramPoint::query()->update(['is_active' => $request->boolean('is_active')]);

        return response()->json(['is_active' => $request->boolean('is_active')]);
    }

    private function pointValidated(Request $request): array
    {
        $c = config('banner.program_point');

        $data = $request->validate([
            'title' => TextRules::line(true, $c['title_min'], $c['title_max'], $c['word_max']),
            'description' => TextRules::line(false, $c['description_min'], $c['description_max'], $c['word_max']),
            'sort_order' => ['nullable', 'integer', 'min:0', "max:{$c['order_max']}"],
            'icon' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg', 'max:' . $c['icon_max_kb']],
        ], TextRules::messages(['title', 'description']) + [
            'icon.mimes' => 'The icon must be a PNG, SVG, WEBP or JPG file.',
            'icon.max' => 'The icon may not be larger than ' . $c['icon_max_kb'] . ' KB.',
        ], ['title' => 'title', 'description' => 'description', 'sort_order' => 'display order', 'icon' => 'icon']);

        unset($data['icon']);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
