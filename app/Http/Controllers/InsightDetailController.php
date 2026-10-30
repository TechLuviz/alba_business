<?php

namespace App\Http\Controllers;

use App\Models\InsightArticle;
use App\Models\InsightCtaCard;

class InsightDetailController extends Controller
{
    public function show(InsightArticle $article)
    {
        abort_unless($article->is_active, 404);

        return view('insight-detail', [
            // per-article SEO (falls back to the article title / description when left blank)
            'seoOverride' => (object) ['meta_title' => $article->seo_title ?: $article->title, 'meta_description' => $article->seo_description ?: $article->description],'article' => $article->load('sections'), 'cta' => InsightCtaCard::current(),
            'related' => InsightArticle::where('is_active', true)->where('show_related', true)->whereKeyNot($article->id)
                ->orderBy('sort_order')->orderByDesc('published_on')->take(3)->get()]);
    }
}
