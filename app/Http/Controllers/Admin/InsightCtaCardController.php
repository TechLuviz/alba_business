<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InsightCtaCard;
use App\Support\TextRules;
use Illuminate\Http\Request;

class InsightCtaCardController extends Controller
{
    public function edit()
    {
        return view('admin.insights-page.cta-card', ['card' => InsightCtaCard::current()]);
    }

    public function update(Request $request)
    {
        $c = config('banner.insight_cta');
        $w = $c['word_max'];

        $data = $request->validate([
            'heading' => TextRules::line(true, $c['heading_min'], $c['heading_max'], $w),
            'heading_accent' => TextRules::line(true, $c['heading_min'], $c['heading_max'], $w),
            'description' => TextRules::line(true, $c['description_min'], $c['description_max'], $w),
        ], TextRules::messages(['heading', 'heading_accent', 'description']), [
            'heading' => 'heading',
            'heading_accent' => 'highlighted heading',
            'description' => 'description',
        ]);
        $data['is_active'] = $request->boolean('is_active');

        InsightCtaCard::current()->fill($data)->save();

        return back()->with('success', 'Insight card saved.');
    }
}
