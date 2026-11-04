<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramCard;
use App\Models\ProgramSection;
use App\Support\TextRules;
use Illuminate\Http\Request;

class ProgramSectionController extends Controller
{
    public function index()
    {
        return view('admin.program.index', [
            'program' => ProgramSection::current(),
            'cards' => ProgramCard::orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $c = config('banner.program');

        $data = $request->validate([
            'eyebrow' => TextRules::lineNoDigits(true, $c['eyebrow_min'], $c['eyebrow_max'], $c['word_max']),
            'heading_accent' => TextRules::lineNoDigits(true, $c['accent_min'], $c['accent_max'], $c['word_max']),
            'heading' => TextRules::lineNoDigits(true, $c['heading_min'], $c['heading_max'], $c['word_max']),
            'description' => TextRules::lineNoDigits(true, $c['description_min'], $c['description_max'], $c['word_max']),
        ], TextRules::messagesNoDigits(['eyebrow', 'heading_accent', 'heading', 'description']), [
            'eyebrow' => 'section label',
            'heading_accent' => 'highlighted heading line',
            'heading' => 'heading',
            'description' => 'description',
        ]);
        $data['is_active'] = $request->boolean('is_active');

        ProgramSection::current()->fill($data)->save();

        return back()->with('success', 'Programs section saved.');
    }
}
