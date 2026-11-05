<?php

use App\Http\Controllers\Admin\AboutSectionController;
use App\Http\Controllers\Admin\AudienceCardController;
use App\Http\Controllers\Admin\AudienceSectionController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BannerSettingsController;
use App\Http\Controllers\Admin\BrandSettingsController;
use App\Http\Controllers\Admin\DynamicBannerController;
use App\Http\Controllers\Admin\HomeTextSectionController;
use App\Http\Controllers\Admin\InsightCtaCardController;
use App\Http\Controllers\Admin\ProblemSectionController;
use App\Http\Controllers\Admin\QuickContactController;
use App\Http\Controllers\Admin\SeoSettingsController;
use App\Http\Controllers\Admin\VideoBannerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InsightDetailController;
use App\Http\Controllers\PrivacyController;
use App\Http\Controllers\ProgramsPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('about', [HomeController::class, 'aboutPage'])->name('about');
Route::get('contact', [HomeController::class, 'contactPage'])->name('contact');
Route::get('programs', [ProgramsPageController::class, 'show'])->name('programs.index');
Route::get('programs/{card:slug}', [HomeController::class, 'programShow'])->name('programs.show');
Route::get('privacy-policy', [PrivacyController::class, 'show'])->name('privacy');
Route::get('terms-of-service', [PrivacyController::class, 'show'])->defaults('type', 'terms')->name('terms');
Route::get('sitemap', [\App\Http\Controllers\SitemapController::class, 'page'])->name('sitemap');
Route::get('sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'xml'])->name('sitemap.xml');
Route::get('insights',[HomeController::class, 'insightsPage'])->name('insights');
Route::get('insights/{article}', [InsightDetailController::class, 'show'])->name('insights.show');
Route::post('contact', [HomeController::class, 'contactSubmit'])->middleware('throttle:5,10')->name('contact.submit');
Route::post('newsletter', [HomeController::class, 'subscribe'])->middleware('throttle:5,1')->name('newsletter.subscribe');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.submit');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/', fn () => redirect()->route('admin.banners.index'))->name('dashboard');

        // Page Settings > Home Page > Banner Settings
        Route::prefix('banners')->name('banners.')->group(function () {
            Route::get('/', [BannerSettingsController::class, 'index'])->name('index');
            Route::post('interval', [BannerSettingsController::class, 'interval'])->name('interval');

            Route::patch('dynamic/toggle-all', [DynamicBannerController::class, 'toggleAll'])->name('dynamic.toggle-all');
            Route::post('dynamic', [DynamicBannerController::class, 'store'])->name('dynamic.store');
            Route::put('dynamic/{slide}', [DynamicBannerController::class, 'update'])->name('dynamic.update');
            Route::patch('dynamic/{slide}/toggle', [DynamicBannerController::class, 'toggle'])->name('dynamic.toggle');
            Route::delete('dynamic/{slide}', [DynamicBannerController::class, 'destroy'])->name('dynamic.destroy');

            Route::patch('video/toggle-all', [VideoBannerController::class, 'toggleAll'])->name('video.toggle-all');
            Route::post('video', [VideoBannerController::class, 'store'])->name('video.store');
            Route::put('video/{video}', [VideoBannerController::class, 'update'])->name('video.update');
            Route::patch('video/{video}/toggle', [VideoBannerController::class, 'toggle'])->name('video.toggle');
            Route::delete('video/{video}', [VideoBannerController::class, 'destroy'])->name('video.destroy');
        });

        // Page Settings > Home Page > Homepage Content
        Route::get('home-content', [HomeTextSectionController::class, 'edit'])->name('home-text.edit');
        Route::put('home-content', [HomeTextSectionController::class, 'update'])->name('home-text.update');

        // Page Settings > Home Page > Content Section (after Insights)
        Route::get('content-section', [\App\Http\Controllers\Admin\HomeContentController::class, 'edit'])->name('homecontent.edit');
        Route::put('content-section', [\App\Http\Controllers\Admin\HomeContentController::class, 'update'])->name('homecontent.update');

        // Page Settings > About Page > Content Section
        Route::get('about-page/content', [\App\Http\Controllers\Admin\HomeContentController::class, 'edit'])->defaults('page', 'about')->name('aboutpage.content.edit');
        Route::put('about-page/content', [\App\Http\Controllers\Admin\HomeContentController::class, 'update'])->defaults('page', 'about')->name('aboutpage.content.update');

        // Page Settings > Home Page > About Section
        Route::get('about-section', [AboutSectionController::class, 'edit'])->name('about.edit');
        Route::put('about-section', [AboutSectionController::class, 'update'])->name('about.update');

        // Page Settings > Home Page > Target Audience Section
        Route::prefix('audience-section')->name('audience.')->group(function () {
            Route::get('/', [AudienceSectionController::class, 'index'])->name('index');
            Route::put('settings', [AudienceSectionController::class, 'updateSettings'])->name('settings.update');

            Route::patch('cards/toggle-all', [AudienceCardController::class, 'toggleAll'])->name('cards.toggle-all');
            Route::post('cards', [AudienceCardController::class, 'store'])->name('cards.store');
            Route::put('cards/{card}', [AudienceCardController::class, 'update'])->name('cards.update');
            Route::patch('cards/{card}/toggle', [AudienceCardController::class, 'toggle'])->name('cards.toggle');
            Route::delete('cards/{card}', [AudienceCardController::class, 'destroy'])->name('cards.destroy');
        });

        // Page Settings > Home Page > Problem Section
        Route::get('problem-section', [ProblemSectionController::class, 'edit'])->name('problem.edit');
        Route::put('problem-section', [ProblemSectionController::class, 'update'])->name('problem.update');

        // Page Settings > Home Page > Programs Section (handled by HomeController)
        Route::prefix('programs-section')->name('program.')->group(function () {
            Route::get('/', [HomeController::class, 'programIndex'])->name('index');
            Route::put('settings', [HomeController::class, 'programSettings'])->name('settings.update');
            Route::put('contact-card', [HomeController::class, 'programHelpSettings'])->name('help.update');

            Route::patch('cards/toggle-all', [HomeController::class, 'programToggleAll'])->name('cards.toggle-all');
            Route::post('cards', [HomeController::class, 'programStore'])->name('cards.store');
            Route::put('cards/{card}', [HomeController::class, 'programUpdate'])->name('cards.update');
            Route::patch('cards/{card}/toggle', [HomeController::class, 'programToggle'])->name('cards.toggle');
            Route::delete('cards/{card}', [HomeController::class, 'programDestroy'])->name('cards.destroy');
        });

        // Page Settings > Home Page > Team Section (handled by HomeController)
        Route::prefix('team-section')->name('team.')->group(function () {
            Route::get('/', [HomeController::class, 'teamIndex'])->name('index');
            Route::put('settings', [HomeController::class, 'teamSettings'])->name('settings.update');

            Route::patch('members/toggle-all', [HomeController::class, 'teamToggleAll'])->name('members.toggle-all');
            Route::post('members', [HomeController::class, 'teamStore'])->name('members.store');
            Route::put('members/{member}', [HomeController::class, 'teamUpdate'])->name('members.update');
            Route::patch('members/{member}/toggle', [HomeController::class, 'teamToggle'])->name('members.toggle');
            Route::delete('members/{member}', [HomeController::class, 'teamDestroy'])->name('members.destroy');
        });

        // Page Settings > Home Page > Insights Section (handled by HomeController)
        Route::prefix('insights-section')->name('insight.')->group(function () {
            Route::get('/', [HomeController::class, 'insightIndex'])->name('index');
            Route::put('settings', [HomeController::class, 'insightSettings'])->name('settings.update');

            Route::patch('cards/toggle-all', [HomeController::class, 'insightToggleAll'])->name('cards.toggle-all');
            Route::post('cards', [HomeController::class, 'insightStore'])->name('cards.store');
            Route::put('cards/{card}', [HomeController::class, 'insightUpdate'])->name('cards.update');
            Route::patch('cards/{card}/toggle', [HomeController::class, 'insightToggle'])->name('cards.toggle');
            Route::delete('cards/{card}', [HomeController::class, 'insightDestroy'])->name('cards.destroy');
        });

        // Page Settings > Home Page > Video Section "Insights in Motion" (handled by HomeController)
        Route::prefix('video-section')->name('reel.')->group(function () {
            Route::get('/', [HomeController::class, 'reelIndex'])->name('index');
            Route::put('settings', [HomeController::class, 'reelSettings'])->name('settings.update');

            Route::patch('cards/toggle-all', [HomeController::class, 'reelToggleAll'])->name('cards.toggle-all');
            Route::post('cards', [HomeController::class, 'reelStore'])->name('cards.store');
            Route::put('cards/{card}', [HomeController::class, 'reelUpdate'])->name('cards.update');
            Route::patch('cards/{card}/toggle', [HomeController::class, 'reelToggle'])->name('cards.toggle');
            Route::delete('cards/{card}', [HomeController::class, 'reelDestroy'])->name('cards.destroy');
        });

        // About Page Settings > Banner Settings (handled by HomeController)
        Route::get('about-page/banner', [HomeController::class, 'aboutBannerEdit'])->name('aboutpage.banner.edit');
        Route::put('about-page/banner', [HomeController::class, 'aboutBannerUpdate'])->name('aboutpage.banner.update');

        Route::get('about-page/story', [HomeController::class, 'aboutStoryEdit'])->name('aboutpage.story.edit');
        Route::put('about-page/story', [HomeController::class, 'aboutStoryUpdate'])->name('aboutpage.story.update');

        Route::prefix('about-page/principles')->name('aboutpage.principles.')->group(function () {
            Route::get('/', [HomeController::class, 'principlesIndex'])->name('index');
            Route::put('settings', [HomeController::class, 'principlesSettings'])->name('settings.update');

            Route::put('items/{item}', [HomeController::class, 'principleUpdate'])->name('items.update');
        });

        Route::get('about-page/team', [HomeController::class, 'aboutTeamEdit'])->name('aboutpage.team.edit');
        Route::put('about-page/team', [HomeController::class, 'aboutTeamUpdate'])->name('aboutpage.team.update');

        Route::get('about-page/get-in-touch', [HomeController::class, 'aboutCtaEdit'])->name('aboutpage.cta.edit');
        Route::put('about-page/get-in-touch', [HomeController::class, 'aboutCtaUpdate'])->name('aboutpage.cta.update');

        // Contact Page Settings > Banner Settings (handled by HomeController)
        Route::get('contact-page/banner', [HomeController::class, 'contactBannerEdit'])->name('contactpage.banner.edit');
        Route::put('contact-page/banner', [HomeController::class, 'contactBannerUpdate'])->name('contactpage.banner.update');

        Route::get('contact-page/get-in-touch', [HomeController::class, 'contactInfoEdit'])->name('contactpage.info.edit');
        Route::put('contact-page/get-in-touch', [HomeController::class, 'contactInfoUpdate'])->name('contactpage.info.update');
        Route::get('contact-page/enquiries', [HomeController::class, 'contactMessagesIndex'])->name('contactpage.messages.index');
        Route::get('contact-page/enquiries/export', [HomeController::class, 'contactMessagesExport'])->name('contactpage.messages.export');
        Route::delete('contact-page/enquiries', [HomeController::class, 'contactMessagesBulkDestroy'])->name('contactpage.messages.bulk-destroy');
        Route::delete('contact-page/enquiries/{message}',[HomeController::class, 'contactMessageDestroy'])->name('contactpage.messages.destroy');

        // Insights Page Settings > Banner Settings (handled by HomeController)
        // Programs Page Settings > Banner + Highlight Points
        Route::get('programs-page/banner', [ProgramsPageController::class, 'bannerEdit'])->name('programpage.banner.edit');
        Route::put('programs-page/banner', [ProgramsPageController::class, 'bannerUpdate'])->name('programpage.banner.update');

        Route::prefix('programs-page/points')->name('programpage.points.')->group(function () {
            Route::get('/', [ProgramsPageController::class, 'pointsIndex'])->name('index');
            Route::patch('toggle-all', [ProgramsPageController::class, 'pointToggleAll'])->name('toggle-all');
            Route::post('/', [ProgramsPageController::class, 'pointStore'])->name('store');
            Route::put('{point}', [ProgramsPageController::class, 'pointUpdate'])->name('update');
            Route::patch('{point}/toggle', [ProgramsPageController::class, 'pointToggle'])->name('toggle');
            Route::delete('{point}', [ProgramsPageController::class, 'pointDestroy'])->name('destroy');
        });

        Route::get('insights-page/banner',[HomeController::class, 'insightBannerEdit'])->name('insightpage.banner.edit');
        Route::put('insights-page/banner', [HomeController::class, 'insightBannerUpdate'])->name('insightpage.banner.update');


        Route::get('insights-page/detail-card', [InsightCtaCardController::class, 'edit'])->name('insightpage.cta.edit');
        Route::put('insights-page/detail-card', [InsightCtaCardController::class, 'update'])->name('insightpage.cta.update');

        Route::prefix('insights-page/featured')->name('insightpage.featured.')->group(function () {
            Route::get('/', [HomeController::class, 'featuredIndex'])->name('index');
            Route::put('settings', [HomeController::class, 'featuredSettings'])->name('settings.update');

            Route::patch('cards/toggle-all', [HomeController::class, 'featuredToggleAll'])->name('cards.toggle-all');
            Route::post('cards', [HomeController::class, 'featuredStore'])->name('cards.store');
            Route::put('cards/{card}', [HomeController::class, 'featuredUpdate'])->name('cards.update');
            Route::patch('cards/{card}/toggle', [HomeController::class, 'featuredToggle'])->name('cards.toggle');
            Route::delete('cards/{card}', [HomeController::class, 'featuredDestroy'])->name('cards.destroy');
        });

        Route::prefix('insights-page/articles')->name('insightpage.articles.')->group(function () {
            Route::get('/', [HomeController::class, 'articlesIndex'])->name('index');
            Route::put('settings', [HomeController::class, 'articlesSettings'])->name('settings.update');

            Route::patch('items/toggle-all', [HomeController::class, 'articleToggleAll'])->name('items.toggle-all');
            Route::post('items', [HomeController::class, 'articleStore'])->name('items.store');
            Route::put('items/{article}', [HomeController::class, 'articleUpdate'])->name('items.update');
            Route::patch('items/{article}/toggle', [HomeController::class, 'articleToggle'])->name('items.toggle');
            Route::delete('items/{article}', [HomeController::class, 'articleDestroy'])->name('items.destroy');
        });

        // Site Settings > Newsletter subscribers (handled by HomeController)
        Route::get('settings/newsletter', [HomeController::class, 'newsletterIndex'])->name('newsletter.index');
        Route::get('settings/newsletter/export', [HomeController::class, 'newsletterExport'])->name('newsletter.export');
        Route::delete('settings/newsletter/{subscriber}', [HomeController::class, 'newsletterDestroy'])->name('newsletter.destroy');

        // Site Settings > Logo & Brand
        Route::get('settings/privacy-policy', [PrivacyController::class, 'edit'])->name('privacy.edit');
        Route::put('settings/privacy-policy', [PrivacyController::class, 'update'])->name('privacy.update');
        Route::get('settings/terms-of-service', [PrivacyController::class, 'edit'])->defaults('type', 'terms')->name('terms.edit');
        Route::put('settings/terms-of-service', [PrivacyController::class, 'update'])->defaults('type', 'terms')->name('terms.update');

        Route::get('settings/brand',[BrandSettingsController::class, 'edit'])->name('brand.edit');
        Route::put('settings/brand', [BrandSettingsController::class, 'update'])->name('brand.update');

        // Site Settings > Quick Contact
        Route::get('settings/quick-contact', [QuickContactController::class, 'edit'])->name('quick.edit');
        Route::put('settings/quick-contact', [QuickContactController::class, 'update'])->name('quick.update');

        // Site Settings > SEO Settings
        Route::get('settings/seo', [SeoSettingsController::class, 'edit'])->name('seo.edit');
        Route::put('settings/seo', [SeoSettingsController::class, 'update'])->name('seo.update');

        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    });
});