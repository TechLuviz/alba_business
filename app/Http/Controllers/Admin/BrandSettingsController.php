<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroBanner;
use App\Support\BannerRules;
use App\Support\Uploads;
use Illuminate\Http\Request;

class BrandSettingsController extends Controller
{
    public function edit()
    {
        return view('admin.brand.edit', ['brand' => $this->brand()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'brand_logo' => BannerRules::image(false, 'logo'),
            'brand_favicon' => BannerRules::favicon(),
        ], BannerRules::messages(), BannerRules::attributes());

        $brand = $this->brand();
        unset($data['brand_logo'], $data['brand_favicon']);

        if ($request->hasFile('brand_favicon')) {
            Uploads::delete($brand->brand_favicon);
            $data['brand_favicon'] = Uploads::store($request->file('brand_favicon'), 'favicon');
        } elseif ($request->boolean('remove_favicon')) {
            Uploads::delete($brand->brand_favicon);
            $data['brand_favicon'] = null;
        }

        if ($request->hasFile('brand_logo')) {
            Uploads::delete($brand->brand_logo);
            $data['brand_logo'] = Uploads::store($request->file('brand_logo'), 'logo');
        } elseif ($request->boolean('remove_logo')) {
            Uploads::delete($brand->brand_logo);
            $data['brand_logo'] = null;
        }

        $brand->fill($data)->save();

        return back()->with('success', 'Brand settings saved.');
    }

    private function brand(): HeroBanner
    {
        return HeroBanner::latest('id')->first() ?? new HeroBanner(['is_active' => true]);
    }
}