<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoSetting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SeoSettingsController extends Controller
{
    public function edit(Request $request)
    {
        $page = $request->query('page');
        $page = array_key_exists($page, SeoSetting::PAGES) ? $page : array_key_first(SeoSetting::PAGES);

        return view('admin.seo.edit', ['pages' => SeoSetting::PAGES, 'page' => $page, 'seo' => SeoSetting::forPage($page)]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'page' => ['required', Rule::in(array_keys(SeoSetting::PAGES))],
            'meta_title' => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string'],
        ], [], ['meta_title' => 'title', 'meta_description' => 'description']);

        SeoSetting::updateOrCreate(['page' => $data['page']], $data);

        return redirect()->route('admin.seo.edit', ['page' => $data['page']])->with('success', 'SEO settings saved.');
    }
}
