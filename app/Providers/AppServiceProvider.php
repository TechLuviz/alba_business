<?php

namespace App\Providers;

use App\Models\HeroBanner;
use App\Models\NavItem;
use App\Models\ProgramCard;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('layout.footer', function ($view) {
            $view->with([
                'brand' => HeroBanner::current(),
                'navItems' => NavItem::where('is_active', true)->orderBy('sort_order')->get(),
                'privacyActive' => Schema::hasTable('privacy_policies') && \App\Models\PrivacyPolicy::where('type', 'privacy')->where('is_active', true)->exists(),
                'termsActive' => Schema::hasTable('privacy_policies') && Schema::hasColumn('privacy_policies', 'type') && \App\Models\PrivacyPolicy::where('type', 'terms')->where('is_active', true)->exists(),
                'footerPrograms' => ProgramCard::where('is_active', true)->orderBy('sort_order')->orderBy('id')->pluck('title', 'slug'),
            ]);
        });

        View::composer('layout.header', function ($view) {
            $view->with([
                'brand' => HeroBanner::current(),
                'navItems' => NavItem::where('is_active', true)->orderBy('sort_order')->get(),
            ]);
        });
    }
}