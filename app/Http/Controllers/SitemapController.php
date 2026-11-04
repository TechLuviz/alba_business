<?php

namespace App\Http\Controllers;

use App\Models\InsightArticle;
use App\Models\NavItem;
use App\Models\PrivacyPolicy;
use App\Models\ProgramCard;

/** Sitemap: an HTML page for visitors (/sitemap) and an XML file for search engines (/sitemap.xml). Both build themselves from the database. */
class SitemapController extends Controller
{
    public function page()
    {
        return view('sitemap', $this->groups());
    }

    public function xml()
    {
        $g = $this->groups();
        $urls = collect($g['pages'])->concat($g['programs'])->concat($g['articles'])->concat($g['legal'])->pluck('url')->unique()->values();

        return response()->view('sitemap-xml', ['urls' => $urls])->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    private function groups(): array
    {
        $pages = NavItem::where('is_active', true)->orderBy('sort_order')->get()->map(function ($item) {
            $url = str_starts_with($item->url, '#') ? route('home') : (str_starts_with($item->url, '/') ? url($item->url) : $item->url);

            return ['label' => $item->label, 'url' => $url];
        })->filter(fn ($p) => str_starts_with($p['url'], url('/')))->values()->all();

        $programs = ProgramCard::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get()
            ->map(fn ($c) => ['label' => $c->title, 'url' => route('programs.show', $c->slug)])->all();

        $articles = InsightArticle::where('is_active', true)->orderBy('sort_order')->orderByDesc('published_on')->orderByDesc('id')->get()
            ->filter(fn ($a) => filled($a->slug))
            ->map(fn ($a) => ['label' => $a->title, 'url' => route('insights.show', $a->slug)])->values()->all();

        $legal = PrivacyPolicy::where('is_active', true)->orderBy('id')->get()
            ->map(fn ($p) => ['label' => $p->title, 'url' => route($p->type === 'terms' ? 'terms' : 'privacy')])->all();

        return compact('pages', 'programs', 'articles', 'legal');
    }
}
