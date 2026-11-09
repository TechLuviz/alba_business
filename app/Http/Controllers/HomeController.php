<?php

namespace App\Http\Controllers;

use App\Models\AboutBanner;
use App\Models\AboutPrinciple;
use App\Models\AboutPrincipleSection;
use App\Models\AboutCta;
use App\Models\AboutSection;
use App\Models\ContactBanner;
use App\Models\ContactMessage;
use App\Models\ContactSection;
use App\Models\AboutTeamSection;
use App\Models\AboutStory;
use App\Models\AudienceCard;
use App\Models\AudienceSection;
use App\Models\HeroBanner;
use App\Models\HeroSlide;
use App\Models\HeroVideo;
use App\Models\HomeTextSection;
use App\Models\InsightArticle;
use App\Models\InsightArticleContent;
use App\Models\InsightArticleSection;
use App\Models\InsightCard;
use App\Models\InsightFeatureCard;
use App\Models\InsightFeatureSection;
use App\Models\InsightPageBanner;
use App\Models\InsightSection;
use App\Models\ProblemSection;
use App\Models\ProgramCard;
use App\Models\ProgramSection;
use App\Models\NewsletterSubscriber;
use App\Models\ReelCard;
use App\Models\ReelSection;
use App\Models\TeamMember;
use App\Models\TeamSection;
use App\Support\BannerRules;
use App\Support\TextRules;
use App\Support\Uploads;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $hero = HeroBanner::current();
        $video = HeroVideo::where('is_active', true)->orderBy('sort_order')->orderBy('id')->first();
        $slides = HeroSlide::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();

        $textSection = HomeTextSection::current();

        $about = AboutSection::current();

        $audience = AudienceSection::current();
        $audienceCards = AudienceCard::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();

        $problem = ProblemSection::current();

        $programSection = ProgramSection::current();
        $programCards = ProgramCard::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();

        $teamSection = TeamSection::current();
        $teamMembers = TeamMember::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();

        $insightSection = InsightSection::current();
        $insightCards = InsightCard::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        // Phones show the real articles from the Insights page instead of the animated stack
        $insightArticles = InsightArticle::where('is_active', true)->orderBy('sort_order')->orderByDesc('published_on')->orderByDesc('id')->limit(6)->get();

        $homeContent = \App\Models\HomeContentSection::current();

        $reelSection = ReelSection::current();
        $reelCards = ReelCard::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();

        return view('home', compact(
            'homeContent',
            'problem', 'hero', 'video', 'slides', 'textSection', 'about', 'audience', 'audienceCards',
            'programSection', 'programCards', 'teamSection', 'teamMembers', 'insightSection', 'insightCards', 'insightArticles',
            'reelSection', 'reelCards'
        ));
    }

    /* ---------- Contact page (public) and its admin settings ---------- */

    public function contactPage()
    {
        return view('contact', [
            'contactBanner' => ContactBanner::current(),
            'contactInfo' => ContactSection::current(),
            'courses' => ProgramCard::where('is_active', true)->orderBy('sort_order')->orderBy('id')->pluck('title'),
        ]);
    }

    public function contactBannerEdit()
    {
        return view('admin.contact-page.banner', ['banner' => ContactBanner::current()]);
    }

    public function contactBannerUpdate(Request $request)
    {
        $c = config('banner.contact_banner');
        $w = $c['word_max'];
        $banner = ContactBanner::current();

        $data = $request->validate([
            'heading_line_1' => TextRules::line(true, $c['heading_min'], $c['heading_max'], $w),
            'heading_line_2' => TextRules::line(true, $c['heading_min'], $c['heading_max'], $w),
            'description' => TextRules::line(true, $c['description_min'], $c['description_max'], $w),
            'image' => BannerRules::image(! $banner->image, 'contact_banner'),
        ], BannerRules::messages() + TextRules::messages(['heading_line_1', 'heading_line_2', 'description']) + [
            'image.dimensions' => "The image must be between {$c['min_width']}x{$c['min_height']} and {$c['max_width']}x{$c['max_height']} pixels.",
            'image.max' => 'The image may not be larger than ' . round($c['max_kb'] / 1024) . ' MB.',
        ], [
            'heading_line_1' => 'heading line 1',
            'heading_line_2' => 'heading line 2',
            'description' => 'description',
            'image' => 'banner image',
        ]);

        unset($data['image']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            Uploads::delete($banner->image);
            $data['image'] = Uploads::store($request->file('image'), 'contact-banner');
        }

        $banner->fill($data)->save();

        return back()->with('success', 'Contact banner saved.');
    }

    /* ---------- Insights page (public) and its admin settings ---------- */

    public function insightsPage()
    {
        return view('insights', [
            'insightBanner' => InsightPageBanner::current(),
            'articleSection' => InsightArticleSection::current(),
            'articles' => InsightArticle::where('is_active', true)
                ->orderBy('sort_order')->orderByDesc('published_on')->orderByDesc('id')
                ->paginate(config('banner.articles.per_page'))->fragment('articles'),
            'featuredSection' => InsightFeatureSection::current(),
            'featuredCards' => InsightFeatureCard::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function insightBannerEdit()
    {
        return view('admin.insights-page.banner', ['banner' => InsightPageBanner::current()]);
    }

    public function insightBannerUpdate(Request $request)
    {
        $c = config('banner.insight_banner');
        $w = $c['word_max'];
        $banner = InsightPageBanner::current();

        $data = $request->validate([
            'heading_line_1' => TextRules::line(true, $c['heading_min'], $c['heading_max'], $w),
            'heading_line_2' => TextRules::line(true, $c['heading_min'], $c['heading_max'], $w),
            'description' => TextRules::line(true, $c['description_min'], $c['description_max'], $w),
            'image' => BannerRules::image(! $banner->image, 'insight_banner'),
        ], BannerRules::messages() + TextRules::messages(['heading_line_1', 'heading_line_2', 'description']) + [
            'image.dimensions' => "The image must be between {$c['min_width']}x{$c['min_height']} and {$c['max_width']}x{$c['max_height']} pixels.",
            'image.max' => 'The image may not be larger than ' . round($c['max_kb'] / 1024) . ' MB.',
        ], [
            'heading_line_1' => 'heading line 1',
            'heading_line_2' => 'heading line 2',
            'description' => 'description',
            'image' => 'banner image',
        ]);

        unset($data['image']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            Uploads::delete($banner->image);
            $data['image'] = Uploads::store($request->file('image'), 'insights-banner');
        }

        $banner->fill($data)->save();

        return back()->with('success', 'Insights banner saved.');
    }

    /* ---------- Admin: Insights Page > Insight Articles ---------- */

    public function articlesIndex()
    {
        return view('admin.insights-page.articles', [
            'section' => InsightArticleSection::current(),
            'articles' => InsightArticle::with('sections')->orderBy('sort_order')->orderByDesc('published_on')->orderByDesc('id')->get(),
        ]);
    }

    public function articlesSettings(Request $request)
    {
        $c = config('banner.articles');

        $data = $request->validate([
            'eyebrow' => TextRules::line(true, $c['eyebrow_min'], $c['eyebrow_max'], $c['word_max']),
            'heading' => TextRules::line(true, $c['heading_min'], $c['heading_max'], $c['word_max']),
        ], TextRules::messages(['eyebrow', 'heading']), ['eyebrow' => 'section label', 'heading' => 'heading']);
        $data['is_active'] = $request->boolean('is_active');

        InsightArticleSection::current()->fill($data)->save();

        return back()->with('success', 'Insight Articles section saved.');
    }

    public function articleStore(Request $request)
    {
        $data = $this->articleValidated($request, true);
        $data['image'] = Uploads::store($request->file('image'), 'articles');
        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = Uploads::store($request->file('banner_image'), 'articles-banner');
        }
        $data['sort_order'] = $data['sort_order'] ?: InsightArticle::count() + 1;
        $article = InsightArticle::create($data);
        $this->syncArticleSections($article, $request);
        InsightArticle::resequence($article->id);

        return redirect()->route('admin.insightpage.articles.index')->with('success', 'Article created.');
    }

    public function articleUpdate(Request $request, InsightArticle $article)
    {
        $data = $this->articleValidated($request, false);

        if ($request->hasFile('image')) {
            Uploads::delete($article->image);
            $data['image'] = Uploads::store($request->file('image'), 'articles');
        }
        if ($request->hasFile('banner_image')) {
            Uploads::delete($article->banner_image);
            $data['banner_image'] = Uploads::store($request->file('banner_image'), 'articles-banner');
        }

        $data['sort_order'] = $data['sort_order'] ?: $article->sort_order; // blank keeps the current position
        $movedDown = $data['sort_order'] > $article->sort_order;
        $article->update($data);
        $this->syncArticleSections($article, $request);
        InsightArticle::resequence($article->id, $movedDown);

        return redirect()->route('admin.insightpage.articles.index')->with('success', 'Article updated.');
    }

    public function articleDestroy(InsightArticle $article)
    {
        Uploads::delete($article->image);
        Uploads::delete($article->banner_image);
        $article->delete();
        InsightArticle::resequence();

        return redirect()->route('admin.insightpage.articles.index')->with('success', 'Article deleted.');
    }

    public function articleToggle(Request $request, InsightArticle $article)
    {
        $article->update(['is_active' => $request->boolean('is_active')]);

        return response()->json(['is_active' => $article->is_active]);
    }

    public function articleToggleAll(Request $request)
    {
        InsightArticle::query()->update(['is_active' => $request->boolean('is_active')]);

        return response()->json(['is_active' => $request->boolean('is_active')]);
    }

    private function articleValidated(Request $request, bool $creating): array
    {
        $c = config('banner.article');
        $i = $c['image'];
        $w = $c['word_max'];

        $rules = [
            'category' => TextRules::line(true, $c['category_min'], $c['category_max'], $w),
            'title' => TextRules::line(true, $c['title_min'], $c['title_max'], $w),
            'description' => TextRules::line(true, $c['description_min'], $c['description_max'], $c['long_word_max']),
            'published_on' => ['required', 'date_format:Y-m-d', 'after_or_equal:' . $c['date_min'], 'before_or_equal:' . now()->addYear()->toDateString()],
            'author' => ['nullable', 'string', 'max:60'],
            'seo_title' => ['nullable', 'string'],
            'seo_description' => ['nullable', 'string'],
            'sections' => ['nullable', 'array', 'max:20'],
            'sections.*.title' => TextRules::line(true, 3, 80, $w),
            'sections.*.body' => TextRules::line(true, 10, 600, $c['long_word_max']),
            'sections.*.read_minutes' => ['nullable', 'integer', 'min:1', 'max:60'],
            'sort_order' => ['nullable', 'integer', 'min:1', "max:{$c['order_max']}"],
            'image' => BannerRules::image($creating, 'article.image'),
            'banner_image' => BannerRules::image(false, 'insight_banner'),
        ];

        $b = config('banner.insight_banner');
        $data = $request->validate($rules, BannerRules::messages() + TextRules::messages(['category', 'title', 'description', 'sections.*.title', 'sections.*.body']) + [
            'banner_image.dimensions' => "The banner image must be between {$b['min_width']}x{$b['min_height']} and {$b['max_width']}x{$b['max_height']} pixels.",
            'banner_image.max' => 'The banner image may not be larger than ' . round($b['max_kb'] / 1024) . ' MB.',
            'published_on.date_format' => 'Enter a valid date.',
            'published_on.after_or_equal' => "The date cannot be before {$c['date_min']}.",
            'published_on.before_or_equal' => 'The date cannot be more than a year in the future.',
            'image.dimensions' => "The image must be between {$i['min_width']}x{$i['min_height']} and {$i['max_width']}x{$i['max_height']} pixels.",
            'image.max' => 'The image may not be larger than ' . round($i['max_kb'] / 1024) . ' MB.',
        ], [
            'category' => 'category',
            'title' => 'title',
            'description' => 'description',
            'published_on' => 'date',
            'sort_order' => 'display order',
            'image' => 'article image',
            'banner_image' => 'banner image',
            'author' => 'author',
            'seo_title' => 'SEO title',
            'seo_description' => 'SEO description',
            'sections.*.title' => 'content title',
            'sections.*.body' => 'content text',
            'sections.*.read_minutes' => 'read time',
        ]);

        unset($data['image'], $data['banner_image'], $data['sections']);
        $data['author'] = $data['author'] ?? null;
        $data['seo_title'] = $data['seo_title'] ?? null;
        $data['seo_description'] = $data['seo_description'] ?? null;
        $data['show_related'] = $request->boolean('show_related');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    /** Replace an article's table-of-contents sections with the ones submitted in the form, in the order given. */
    private function syncArticleSections(InsightArticle $article, Request $request): void
    {
        $article->sections()->delete();

        foreach (array_values($request->input('sections', [])) as $i => $row) {
            InsightArticleContent::create([
                'insight_article_id' => $article->id,
                'title' => trim($row['title']),
                'body' => trim($row['body']),
                'read_minutes' => $row['read_minutes'] ?: null,
                'sort_order' => $i + 1,
            ]);
        }
    }

    /* ---------- Admin: Insights Page > Featured Insights ---------- */

    public function featuredIndex()
    {
        return view('admin.insights-page.featured', [
            'section' => InsightFeatureSection::current(),
            'cards' => InsightFeatureCard::orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function featuredSettings(Request $request)
    {
        $c = config('banner.featured');

        $data = $request->validate([
            'eyebrow' => TextRules::line(true, $c['eyebrow_min'], $c['eyebrow_max'], $c['word_max']),
        ], TextRules::messages(['eyebrow']), ['eyebrow' => 'section label']);
        $data['is_active'] = $request->boolean('is_active');

        InsightFeatureSection::current()->fill($data)->save();

        return back()->with('success', 'Featured Insights section saved.');
    }

    public function featuredStore(Request $request)
    {
        $data = $this->featuredValidated($request, true);
        $data['image'] = Uploads::store($request->file('image'), 'featured');
        InsightFeatureCard::create($data);

        return redirect()->route('admin.insightpage.featured.index')->with('success', 'Insight card created.');
    }

    public function featuredUpdate(Request $request, InsightFeatureCard $card)
    {
        $data = $this->featuredValidated($request, false);

        if ($request->hasFile('image')) {
            Uploads::delete($card->image);
            $data['image'] = Uploads::store($request->file('image'), 'featured');
        }

        $card->update($data);

        return redirect()->route('admin.insightpage.featured.index')->with('success', 'Insight card updated.');
    }

    public function featuredDestroy(InsightFeatureCard $card)
    {
        Uploads::delete($card->image);
        $card->delete();

        return redirect()->route('admin.insightpage.featured.index')->with('success', 'Insight card deleted.');
    }

    public function featuredToggle(Request $request, InsightFeatureCard $card)
    {
        $card->update(['is_active' => $request->boolean('is_active')]);

        return response()->json(['is_active' => $card->is_active]);
    }

    public function featuredToggleAll(Request $request)
    {
        InsightFeatureCard::query()->update(['is_active' => $request->boolean('is_active')]);

        return response()->json(['is_active' => $request->boolean('is_active')]);
    }

    private function featuredValidated(Request $request, bool $creating): array
    {
        $c = config('banner.featured_card');
        $i = $c['image'];
        $w = $c['word_max'];

        $rules = [
            'category' => TextRules::line(true, $c['category_min'], $c['category_max'], $w),
            'title' => TextRules::line(true, $c['title_min'], $c['title_max'], $w),
            'published_on' => ['required', 'date_format:Y-m-d', 'after_or_equal:' . $c['date_min'], 'before_or_equal:' . now()->addYear()->toDateString()],
            'sort_order' => ['nullable', 'integer', 'min:0', "max:{$c['order_max']}"],
            'image' => BannerRules::image($creating, 'featured_card.image'),
        ];

        $data = $request->validate($rules, BannerRules::messages() + TextRules::messages(['category', 'title']) + [
            'published_on.date_format' => 'Enter a valid date.',
            'published_on.after_or_equal' => "The date cannot be before {$c['date_min']}.",
            'published_on.before_or_equal' => 'The date cannot be more than a year in the future.',
            'image.dimensions' => "The image must be between {$i['min_width']}x{$i['min_height']} and {$i['max_width']}x{$i['max_height']} pixels.",
            'image.max' => 'The image may not be larger than ' . round($i['max_kb'] / 1024) . ' MB.',
        ], [
            'category' => 'category',
            'title' => 'title',
            'published_on' => 'date',
            'sort_order' => 'display order',
            'image' => 'card image',
        ]);

        unset($data['image']);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    /* ---------- Contact page: public form + admin (contact details, enquiries) ---------- */

    public function contactSubmit(Request $request)
    {
        // Hidden honeypot: real visitors leave it empty, so bots get a silent "success".
        if (filled($request->input('website'))) {
            return response()->json(['message' => 'Thank you! We will be in touch soon.']);
        }

        $f = config('banner.contact_form');
        $courses = ProgramCard::where('is_active', true)->pluck('title')->all();
        $courses[] = $f['other_course'];

        $request->merge([
            'name' => trim(preg_replace('/\s+/u', ' ', (string) $request->input('name'))),
            'email' => mb_strtolower(trim((string) $request->input('email'))),
            'phone' => trim((string) $request->input('phone')),
            'message' => trim(str_replace("\r\n", "\n", (string) $request->input('message'))),
        ]);

        $plain = [];
        foreach (['name', 'email', 'phone', 'course', 'message'] as $field) {
            if (preg_match('/[^\x20-\x7E\n]/', (string) $request->input($field))) {
                $plain[$field] = 'Emojis are not allowed.';
            }
        }
        if ($plain) {
            throw \Illuminate\Validation\ValidationException::withMessages($plain);
        }

        $data = $request->validate([
            'name' => ['required', 'string', "min:{$f['name_min']}", "max:{$f['name_max']}", "regex:/^[A-Za-z][A-Za-z .'-]*$/"],
            'email' => ['required', 'string', "max:{$f['email_max']}", 'email:rfc', 'regex:/^[^\s@]+@[^\s@]+\.[A-Za-z]{2,}$/', 'not_regex:/\.(co|cm|con|cmo|ocm|vom|xom|cpm|comm|coom|nte|ogr|ne|or)$/i'],
            'phone' => ['required', 'string', "max:{$f['phone_max']}", 'regex:/^\+?[0-9][0-9 ()-]*[0-9]$/', function (string $attr, mixed $value, \Closure $fail) {
                $digits = strlen(preg_replace('/\D/', '', $value));
                if ($digits < 7 || $digits > 15) {
                    $fail('A phone number has 7 to 15 digits.');
                }
            }],
            'course' => ['required', 'string', \Illuminate\Validation\Rule::in($courses)],
            'message' => ['required', 'string', "min:{$f['message_min']}", "max:{$f['message_max']}", "regex:/^[A-Za-z0-9\\s.,'\"?!&():;\\/-]+$/", function (string $attr, mixed $value, \Closure $fail) {
                foreach (preg_split('/\s+/', $value) as $word) {
                    if (mb_strlen($word) > 40) {
                        $fail('A single word in the message is too long (40 characters maximum).');

                        return;
                    }
                }
            }],
        ], [
            'name.regex' => 'The name may contain only letters, spaces and . \' - (no numbers, emojis or symbols).',
            'email.email' => 'Enter a valid email address, like name@example.com.',
            'email.regex' => 'Enter a valid email address, like name@example.com.',
            'phone.regex' => 'Use digits only, with an optional leading + and spaces, dashes or brackets.',
            'course.in' => 'Please choose a course from the list.',
            'message.regex' => 'Symbols like # @ % $ * are not allowed. Use letters, numbers and . , \' " ? ! & ( ) : ; / - only.',
        ], [
            'name' => 'name',
            'email' => 'email',
            'phone' => 'phone number',
            'course' => 'course',
            'message' => 'message',
        ]);

        ContactMessage::create($data + ['ip' => $request->ip()]);

        return response()->json(['message' => 'Thank you! Your message has been sent and we will be in touch soon.']);
    }

    public function contactInfoEdit()
    {
        return view('admin.contact-page.info', ['info' => ContactSection::current()]);
    }

    public function contactInfoUpdate(Request $request)
    {
        $c = config('banner.contact_info');
        $w = $c['word_max'];

        $data = $request->validate([
            'heading' => TextRules::line(true, $c['heading_min'], $c['heading_max'], $w),
            'heading_accent' => TextRules::line(true, $c['accent_min'], $c['accent_max'], $w),
            'description' => TextRules::line(true, $c['description_min'], $c['description_max'], $c['long_word_max']),
            'email' => ['required', 'string', "max:{$c['email_max']}", 'email:rfc', 'regex:/^[^\s@]+@[^\s@]+\.[A-Za-z]{2,}$/', 'not_regex:/\.(co|cm|con|cmo|ocm|vom|xom|cpm|comm|coom|nte|ogr|ne|or)$/i'],
            'phone' => ['required', 'string', "max:{$c['phone_max']}", 'regex:/^\+?[0-9][0-9 ()-]*[0-9]$/', function (string $attr, mixed $value, \Closure $fail) {
                $digits = strlen(preg_replace('/\D/', '', $value));
                if ($digits < 7 || $digits > 15) {
                    $fail('A phone number has 7 to 15 digits.');
                }
            }],
            'address' => ['required', 'string', "min:{$c['address_min']}", "max:{$c['address_max']}", "regex:/^(?! )(?!.*  )[A-Za-z0-9 .,'&#\/-]+$/"],
        ], TextRules::messages(['heading', 'heading_accent', 'description']) + [
            'email.email' => 'Enter a valid email address, like name@example.com.',
            'email.regex' => 'Enter a valid email address, like name@example.com.',
            'phone.regex' => 'Use digits only, with an optional leading + and spaces, dashes or brackets.',
            'address.regex' => "The address may contain only letters, numbers, spaces and . , ' & # / - (no emojis, leading or repeated spaces).",
        ], [
            'heading' => 'heading',
            'heading_accent' => 'heading accent',
            'description' => 'description',
            'email' => 'email',
            'phone' => 'phone number',
            'address' => 'address',
        ]);
        $data['is_active'] = $request->boolean('is_active');

        ContactSection::current()->fill($data)->save();

        return back()->with('success', 'Contact details saved.');
    }

    public function contactMessagesIndex(Request $request)
    {
        $sorts = ['name' => 'name', 'email' => 'email', 'course' => 'course', 'received' => 'created_at'];
        $sort = array_key_exists($request->query('sort'), $sorts) ? $request->query('sort') : 'received';
        $dir = $request->query('dir') === 'asc' ? 'asc' : 'desc';
        $showAll = $request->query('per_page') === 'all';
        $perPage = $showAll ? 'all' : (in_array((int) $request->query('per_page'), [10, 25, 50, 100], true) ? (int) $request->query('per_page') : 10);
        $q = trim((string) $request->query('q', ''));

        $query = ContactMessage::query();
        if ($q !== '') {
            $like = '%' . addcslashes(mb_substr($q, 0, 100), '%_\\') . '%';
            $query->where(fn ($w) => $w->where('name', 'like', $like)->orWhere('email', 'like', $like)
                ->orWhere('phone', 'like', $like)->orWhere('course', 'like', $like)->orWhere('message', 'like', $like));
        }
        $query->orderBy($sorts[$sort], $dir)->orderBy('id', $dir);

        // "All" = a single page holding every matching row
        $messages = $query->paginate($showAll ? max(1, (clone $query)->count()) : $perPage)->withQueryString();
        // e.g. the last row of the last page was just deleted: step back to the new last page
        if ($messages->isEmpty() && $messages->currentPage() > 1) {
            return redirect()->route('admin.contactpage.messages.index', array_merge($request->query(), ['page' => $messages->lastPage()]));
        }

        return view('admin.contact-page.messages', compact('messages', 'sort', 'dir', 'perPage', 'q'));
    }

    public function contactMessageDestroy(ContactMessage $message)
    {
        $message->delete();

        return back()->with('success', 'Enquiry deleted.');
    }

    public function contactMessagesBulkDestroy(Request $request)
    {
        $data = $request->validate(['ids' => ['required', 'array', 'min:1', 'max:5000'], 'ids.*' => ['integer']], [
            'ids.required' => 'Select at least one enquiry to delete.',
        ]);
        $count = ContactMessage::whereIn('id', $data['ids'])->delete();

        return back()->with('success', $count . ' ' . ($count === 1 ? 'enquiry' : 'enquiries') . ' deleted.');
    }

    public function contactMessagesExport()
    {
        $rows = ContactMessage::orderBy('id')->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['name', 'email', 'phone', 'course', 'message', 'received_at']);
            foreach ($rows as $r) {
                // prefix risky leading characters so spreadsheets never treat a cell as a formula
                $safe = fn ($v) => preg_match('/^[=+\-@]/', (string) $v) ? "'" . $v : $v;
                fputcsv($out, [$safe($r->name), $safe($r->email), $safe($r->phone), $safe($r->course), $safe($r->message), $r->created_at]);
            }
            fclose($out);
        }, 'enquiries.csv', ['Content-Type' => 'text/csv']);
    }

    /* ---------- About page (public) and its admin settings ---------- */

    public function aboutPage()
    {
        return view('about', ['aboutBanner' => AboutBanner::current(), 'aboutStory' => AboutStory::current(),
            'aboutTeam' => AboutTeamSection::current(),
            'aboutCta' => AboutCta::current(),
            'aboutContent' => \App\Models\HomeContentSection::current('about'),
            'teamMembers' => TeamMember::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get(),
            'principleSection' => AboutPrincipleSection::current(),
            'principles' => AboutPrinciple::orderBy('sort_order')->orderBy('id')->take(2)->get(),
        ]);
    }

    public function aboutBannerEdit()
    {
        return view('admin.about-page.banner', ['banner' => AboutBanner::current()]);
    }

    public function aboutBannerUpdate(Request $request)
    {
        $c = config('banner.about_banner');
        $w = $c['word_max'];
        $banner = AboutBanner::current();

        $data = $request->validate([
            'heading_line_1' => TextRules::lineNoDigits(true, $c['heading_min'], $c['heading_max'], $w),
            'heading_line_2' => TextRules::lineNoDigits(true, $c['heading_min'], $c['heading_max'], $w),
            'description' => TextRules::lineNoDigits(true, $c['description_min'], $c['description_max'], $w),
            'image' => BannerRules::image(! $banner->image, 'about_banner'),
        ], BannerRules::messages() + TextRules::messagesNoDigits(['heading_line_1', 'heading_line_2', 'description']) + [
            'image.dimensions' => "The image must be between {$c['min_width']}x{$c['min_height']} and {$c['max_width']}x{$c['max_height']} pixels.",
            'image.max' => 'The image may not be larger than ' . round($c['max_kb'] / 1024) . ' MB.',
        ], [
            'heading_line_1' => 'heading line 1',
            'heading_line_2' => 'heading line 2',
            'description' => 'description',
            'image' => 'banner image',
        ]);

        unset($data['image']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            Uploads::delete($banner->image);
            $data['image'] = Uploads::store($request->file('image'), 'about-banner');
        }

        $banner->fill($data)->save();

        return back()->with('success', 'About banner saved.');
    }

    public function aboutStoryEdit()
    {
        return view('admin.about-page.story', ['story' => AboutStory::current()]);
    }

    public function aboutStoryUpdate(Request $request)
    {
        $c = config('banner.about_story');
        $w = $c['word_max'];
        $lw = $c['long_word_max'];
        $story = AboutStory::current();
        $i = $c['image'];

        $fields = ['eyebrow', 'heading', 'heading_accent', 'paragraph_1', 'paragraph_2', 'quote', 'person_name', 'person_role'];
        $data = $request->validate([
            'eyebrow' => TextRules::lineNoDigits(true, $c['eyebrow_min'], $c['eyebrow_max'], $w),
            'heading' => TextRules::lineNoDigits(true, $c['heading_min'], $c['heading_max'], $w),
            'heading_accent' => TextRules::lineNoDigits(true, $c['accent_min'], $c['accent_max'], $w),
            'paragraph_1' => TextRules::lineNoDigits(true, $c['para1_min'], $c['para1_max'], $lw),
            'paragraph_2' => TextRules::lineNoDigits(true, $c['para2_min'], $c['para2_max'], $lw),
            'quote' => TextRules::lineNoDigits(true, $c['quote_min'], $c['quote_max'], $lw),
            'person_name' => TextRules::lineNoDigits(true, $c['name_min'], $c['name_max'], $w),
            'person_role' => TextRules::lineNoDigits(true, $c['role_min'], $c['role_max'], $w),
            'image' => BannerRules::image(! $story->image, 'about_story.image'),
        ], BannerRules::messages() + TextRules::messagesNoDigits($fields) + [
            'image.dimensions' => "The photo must be between {$i['min_width']}x{$i['min_height']} and {$i['max_width']}x{$i['max_height']} pixels.",
            'image.max' => 'The photo may not be larger than ' . round($i['max_kb'] / 1024) . ' MB.',
        ], [
            'eyebrow' => 'section label',
            'heading' => 'heading',
            'heading_accent' => 'heading accent',
            'paragraph_1' => 'first paragraph',
            'paragraph_2' => 'second paragraph',
            'quote' => 'quote',
            'person_name' => 'person name',
            'person_role' => 'person role',
            'image' => 'photo',
        ]);

        unset($data['image']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            Uploads::delete($story->image);
            $data['image'] = Uploads::store($request->file('image'), 'about-story');
        }

        $story->fill($data)->save();

        return back()->with('success', 'Our Story section saved.');
    }

    /* ---------- Admin: About Page > Get In Touch (closing call-to-action) ---------- */

    public function aboutCtaEdit()
    {
        return view('admin.about-page.cta', ['cta' => AboutCta::current()]);
    }

    public function aboutCtaUpdate(Request $request)
    {
        $c = config('banner.about_cta');
        $w = $c['word_max'];
        $i = $c['image'];
        $cta = AboutCta::current();
        $max = round($i['max_kb'] / 1024);

        $rules = [
            'eyebrow' => TextRules::lineNoDigits(true, $c['eyebrow_min'], $c['eyebrow_max'], $w),
            'heading_line_1' => TextRules::lineNoDigits(true, $c['line_min'], $c['line_max'], $w),
            'heading_line_2' => TextRules::lineNoDigits(true, $c['line_min'], $c['line_max'], $w),
            'heading_accent' => TextRules::lineNoDigits(true, $c['accent_min'], $c['accent_max'], $w),
            'description' => TextRules::lineNoDigits(true, $c['description_min'], $c['description_max'], $c['long_word_max']),
            'image_caption' => TextRules::lineNoDigits(true, $c['caption_min'], $c['caption_max'], $w),
            'image_main' => BannerRules::image(! $cta->image_main, 'about_cta.image'),
            'image_small' => BannerRules::image(! $cta->image_small, 'about_cta.image'),
        ];

        $data = $request->validate($rules, BannerRules::messages() + TextRules::messagesNoDigits(['eyebrow', 'heading_line_1', 'heading_line_2', 'heading_accent', 'description', 'image_caption']) + [
            'image_main.dimensions' => "The large image must be between {$i['min_width']}x{$i['min_height']} and {$i['max_width']}x{$i['max_height']} pixels.",
            'image_small.dimensions' => "The small image must be between {$i['min_width']}x{$i['min_height']} and {$i['max_width']}x{$i['max_height']} pixels.",
            'image_main.max' => "The large image may not be larger than {$max} MB.",
            'image_small.max' => "The small image may not be larger than {$max} MB.",
        ], [
            'eyebrow' => 'section label',
            'heading_line_1' => 'heading line 1',
            'heading_line_2' => 'heading line 2',
            'heading_accent' => 'heading accent',
            'description' => 'description',
            'image_caption' => 'small image caption',
            'image_main' => 'large image',
            'image_small' => 'small image',
        ]);

        foreach (['image_main', 'image_small'] as $field) {
            unset($data[$field]);
            if ($request->hasFile($field)) {
                Uploads::delete($cta->{$field});
                $data[$field] = Uploads::store($request->file($field), 'about-cta');
            }
        }
        $data['is_active'] = $request->boolean('is_active');

        $cta->fill($data)->save();

        return back()->with('success', 'Get In Touch section saved.');
    }

    /* ---------- Admin: About Page > Our Team (heading only; the members are the home page Team Section members) ---------- */

    public function aboutTeamEdit()
    {
        return view('admin.about-page.team', [
            'team' => AboutTeamSection::current(),
            'memberCount' => TeamMember::where('is_active', true)->count(),
        ]);
    }

    public function aboutTeamUpdate(Request $request)
    {
        $c = config('banner.about_team');
        $w = $c['word_max'];

        $data = $request->validate([
            'eyebrow' => TextRules::lineNoDigits(true, $c['eyebrow_min'], $c['eyebrow_max'], $w),
            'heading' => TextRules::lineNoDigits(true, $c['heading_min'], $c['heading_max'], $w),
            'heading_accent' => TextRules::lineNoDigits(true, $c['accent_min'], $c['accent_max'], $w),
        ], TextRules::messagesNoDigits(['eyebrow', 'heading', 'heading_accent']), [
            'eyebrow' => 'section label',
            'heading' => 'heading',
            'heading_accent' => 'heading accent',
        ]);
        $data['is_active'] = $request->boolean('is_active');

        AboutTeamSection::current()->fill($data)->save();

        return back()->with('success', 'Our Team section saved.');
    }

    /* ---------- Admin: About Page > Guiding Principles ---------- */

    public function principlesIndex()
    {
        return view('admin.about-page.principles', [
            'section' => AboutPrincipleSection::current(),
            'items' => AboutPrinciple::orderBy('sort_order')->orderBy('id')->take(2)->get(),
        ]);
    }

    public function principlesSettings(Request $request)
    {
        $c = config('banner.principles');
        $i = $c['image'];
        $section = AboutPrincipleSection::current();

        $data = $request->validate([
            'eyebrow' => TextRules::lineNoDigits(true, $c['eyebrow_min'], $c['eyebrow_max'], $c['word_max']),
            'image' => BannerRules::image(! $section->image, 'principles.image'),
        ], BannerRules::messages() + TextRules::messagesNoDigits(['eyebrow']) + [
            'image.dimensions' => "The image must be between {$i['min_width']}x{$i['min_height']} and {$i['max_width']}x{$i['max_height']} pixels.",
            'image.max' => 'The image may not be larger than ' . round($i['max_kb'] / 1024) . ' MB.',
        ], ['eyebrow' => 'section label', 'image' => 'section image']);

        unset($data['image']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            Uploads::delete($section->image);
            $data['image'] = Uploads::store($request->file('image'), 'principles');
        }

        $section->fill($data)->save();

        return back()->with('success', 'Guiding Principles section saved.');
    }

    /** The section always has two fixed principles (Mission, Vision): they can be edited but not added, removed or switched off. */
    public function principleUpdate(Request $request, AboutPrinciple $item)
    {
        $c = config('banner.principle');
        $w = $c['word_max'];

        $data = $request->validate([
            'label' => TextRules::lineNoDigits(true, $c['label_min'], $c['label_max'], $w),
            'heading' => TextRules::lineNoDigits(true, $c['heading_min'], $c['heading_max'], $w),
            'heading_accent' => TextRules::lineNoDigits(true, $c['accent_min'], $c['accent_max'], $w),
            'description' => TextRules::lineNoDigits(true, $c['description_min'], $c['description_max'], $c['long_word_max']),
        ], TextRules::messagesNoDigits(['label', 'heading', 'heading_accent', 'description']), [
            'label' => 'label',
            'heading' => 'heading',
            'heading_accent' => 'heading accent',
            'description' => 'description',
        ]);

        $item->update($data);

        return redirect()->route('admin.aboutpage.principles.index')->with('success', 'Principle updated.');
    }

    /* ---------- Footer newsletter (public) and admin subscriber list ---------- */

    public function subscribe(Request $request)
    {
        // Hidden honeypot: real visitors leave it empty, so bots get a silent "success".
        if (filled($request->input('website'))) {
            return response()->json(['message' => 'Thank you for subscribing!']);
        }

        $request->merge(['email' => mb_strtolower(trim((string) $request->input('email')))]);

        $data = $request->validate([
            'email' => ['required', 'string', 'max:254', 'email:rfc', 'regex:/^[A-Za-z0-9.!#$%&\'*+\/=?^_`{|}~@-]+\.[A-Za-z]{2,}$/'],
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Enter a valid email address, like name@example.com.',
            'email.regex' => 'Enter a valid email address, like name@example.com (no spaces, emojis or special characters).',
            'email.max' => 'The email address is too long.',
        ]);

        if (NewsletterSubscriber::where('email', $data['email'])->exists()) {
            return response()->json(['message' => 'You are already subscribed. Thank you!']);
        }

        NewsletterSubscriber::create(['email' => $data['email'], 'ip' => $request->ip()]);

        return response()->json(['message' => 'Thank you for subscribing!']);
    }

    public function newsletterIndex(Request $request)
    {
        $sorts = ['email' => 'email', 'subscribed' => 'created_at'];
        $sort = array_key_exists($request->query('sort'), $sorts) ? $request->query('sort') : 'subscribed';
        $dir = $request->query('dir') === 'asc' ? 'asc' : 'desc';
        $showAll = $request->query('per_page') === 'all';
        $perPage = $showAll ? 'all' : (in_array((int) $request->query('per_page'), [10, 25, 50, 100], true) ? (int) $request->query('per_page') : 10);
        $q = trim((string) $request->query('q', ''));

        $query = NewsletterSubscriber::query();
        if ($q !== '') {
            $query->where('email', 'like', '%' . addcslashes(mb_substr($q, 0, 100), '%_\\') . '%');
        }
        $query->orderBy($sorts[$sort], $dir)->orderBy('id', $dir);

        $subscribers = $query->paginate($showAll ? max(1, (clone $query)->count()) : $perPage)->withQueryString();
        if ($subscribers->isEmpty() && $subscribers->currentPage() > 1) {
            return redirect()->route('admin.newsletter.index', array_merge($request->query(), ['page' => $subscribers->lastPage()]));
        }

        return view('admin.newsletter.index', compact('subscribers', 'sort', 'dir', 'perPage', 'q'));
    }

    public function newsletterBulkDestroy(Request $request)
    {
        $data = $request->validate(['ids' => ['required', 'array', 'min:1', 'max:5000'], 'ids.*' => ['integer']], [
            'ids.required' => 'Select at least one subscriber to remove.',
        ]);
        $count = NewsletterSubscriber::whereIn('id', $data['ids'])->delete();

        return back()->with('success', $count . ' ' . ($count === 1 ? 'subscriber' : 'subscribers') . ' removed.');
    }

    public function newsletterDestroy(NewsletterSubscriber $subscriber)
    {
        $subscriber->delete();

        return back()->with('success', 'Subscriber removed.');
    }

    public function newsletterExport()
    {
        $rows = NewsletterSubscriber::orderBy('id')->get(['email', 'created_at']);

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['email', 'subscribed_at']);
            foreach ($rows as $r) {
                // prefix risky leading characters so spreadsheets never treat a cell as a formula
                $email = preg_match('/^[=+\-@]/', $r->email) ? "'" . $r->email : $r->email;
                fputcsv($out, [$email, $r->created_at]);
            }
            fclose($out);
        }, 'newsletter-subscribers.csv', ['Content-Type' => 'text/csv']);
    }


    /* ---------- Admin: Page Settings > Home Page > Video Section (Insights in Motion) ---------- */

    public function reelIndex()
    {
        return view('admin.reel.index', [
            'reel' => ReelSection::current(),
            'cards' => ReelCard::orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function reelSettings(Request $request)
    {
        $c = config('banner.reel');
        $w = $c['word_max'];

        $data = $request->validate([
            'eyebrow' => TextRules::lineNoDigits(true, $c['eyebrow_min'], $c['eyebrow_max'], $w),
            'heading' => TextRules::lineNoDigits(true, $c['heading_min'], $c['heading_max'], $w),
            'heading_accent' => TextRules::lineNoDigits(true, $c['accent_min'], $c['accent_max'], $w),
            'description' => TextRules::lineNoDigits(true, $c['description_min'], $c['description_max'], $w),
        ], TextRules::messagesNoDigits(['eyebrow', 'heading', 'heading_accent', 'description']), [
            'eyebrow' => 'section label',
            'heading' => 'heading',
            'heading_accent' => 'heading accent',
            'description' => 'description',
        ]);
        $data['is_active'] = $request->boolean('is_active');

        ReelSection::current()->fill($data)->save();

        return back()->with('success', 'Video section saved.');
    }

    public function reelStore(Request $request)
    {
        $data = $this->reelValidated($request, true);
        ReelCard::create($data);

        return redirect()->route('admin.reel.index')->with('success', 'Video card created.');
    }

    public function reelUpdate(Request $request, ReelCard $card)
    {
        $data = $this->reelValidated($request, false);

        $card->update($data);

        return redirect()->route('admin.reel.index')->with('success', 'Video card updated.');
    }

    public function reelDestroy(ReelCard $card)
    {
        Uploads::delete($card->video); // removes a clip uploaded before clips were dropped
        $card->delete();

        return redirect()->route('admin.reel.index')->with('success', 'Video card deleted.');
    }

    public function reelToggle(Request $request, ReelCard $card)
    {
        $card->update(['is_active' => $request->boolean('is_active')]);

        return response()->json(['is_active' => $card->is_active]);
    }

    public function reelToggleAll(Request $request)
    {
        ReelCard::query()->update(['is_active' => $request->boolean('is_active')]);

        return response()->json(['is_active' => $request->boolean('is_active')]);
    }

    private function reelValidated(Request $request, bool $creating): array
    {
        $c = config('banner.reel_card');
        $w = $c['word_max'];
        $names = implode(', ', array_unique(array_values(ReelCard::PLATFORMS)));

        $rules = [
            'tag' => TextRules::lineNoDigits(true, $c['tag_min'], $c['tag_max'], $w),
            'title' => TextRules::lineNoDigits(true, $c['title_min'], $c['title_max'], $w),
            'url' => ['required', 'string', "max:{$c['url_max']}", 'regex:/^https:\/\/[^\s]+$/', function (string $attribute, mixed $value, \Closure $fail) use ($names) {
                if (ReelCard::platformFor($value) === null) {
                    $fail("The video link must be a https link from one of: {$names}.");
                }
            }],
            'sort_order' => ['nullable', 'integer', 'min:0', "max:{$c['order_max']}"],
        ];

        $messages = TextRules::messagesNoDigits(['tag', 'title']) + [
            'url.regex' => 'The video link must start with https:// and contain no spaces.',
        ];

        $data = $request->validate($rules, $messages, [
            'tag' => 'category label',
            'title' => 'card title',
            'url' => 'video link',
            'sort_order' => 'display order',
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    /* ---------- Admin: Page Settings > Home Page > Insights Section ---------- */

    public function insightIndex()
    {
        return view('admin.insight.index', [
            'insight' => InsightSection::current(),
            'cards' => InsightCard::orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function insightSettings(Request $request)
    {
        $c = config('banner.insight');
        $w = $c['word_max'];

        $data = $request->validate([
            'eyebrow' => TextRules::line(true, $c['eyebrow_min'], $c['eyebrow_max'], $w),
            'heading' => TextRules::line(true, $c['heading_min'], $c['heading_max'], $w),
            'heading_accent' => TextRules::line(true, $c['accent_min'], $c['accent_max'], $w),
            'description' => TextRules::line(true, $c['description_min'], $c['description_max'], $w),
        ], TextRules::messages(['eyebrow', 'heading', 'heading_accent', 'description']), [
            'eyebrow' => 'section label',
            'heading' => 'heading',
            'heading_accent' => 'heading accent',
            'description' => 'description',
        ]);
        $data['is_active'] = $request->boolean('is_active');

        InsightSection::current()->fill($data)->save();

        return back()->with('success', 'Insights section saved.');
    }

    public function insightStore(Request $request)
    {
        $data = $this->insightValidated($request, true);
        $data['image'] = Uploads::store($request->file('image'), 'insight');
        InsightCard::create($data);

        return redirect()->route('admin.insight.index')->with('success', 'Insight card created.');
    }

    public function insightUpdate(Request $request, InsightCard $card)
    {
        $data = $this->insightValidated($request, false);

        if ($request->hasFile('image')) {
            Uploads::delete($card->image);
            $data['image'] = Uploads::store($request->file('image'), 'insight');
        }

        $card->update($data);

        return redirect()->route('admin.insight.index')->with('success', 'Insight card updated.');
    }

    public function insightDestroy(InsightCard $card)
    {
        Uploads::delete($card->image);
        $card->delete();

        return redirect()->route('admin.insight.index')->with('success', 'Insight card deleted.');
    }

    public function insightToggle(Request $request, InsightCard $card)
    {
        $card->update(['is_active' => $request->boolean('is_active')]);

        return response()->json(['is_active' => $card->is_active]);
    }

    public function insightToggleAll(Request $request)
    {
        InsightCard::query()->update(['is_active' => $request->boolean('is_active')]);

        return response()->json(['is_active' => $request->boolean('is_active')]);
    }

    private function insightValidated(Request $request, bool $creating): array
    {
        $c = config('banner.insight_card');
        $i = $c['image'];
        $w = $c['word_max'];

        $rules = [
            'title' => TextRules::line(true, $c['title_min'], $c['title_max'], $w),
            'description' => TextRules::line(true, $c['description_min'], $c['description_max'], $w),
            'sort_order' => ['nullable', 'integer', 'min:0', "max:{$c['order_max']}"],
            'image' => BannerRules::image($creating, 'insight_card.image'),
        ];

        $messages = BannerRules::messages() + TextRules::messages(['title', 'description']) + [
            'image.dimensions' => "The image must be between {$i['min_width']}x{$i['min_height']} and {$i['max_width']}x{$i['max_height']} pixels.",
            'image.max' => 'The image may not be larger than ' . round($i['max_kb'] / 1024) . ' MB.',
        ];

        $data = $request->validate($rules, $messages, [
            'title' => 'card title',
            'description' => 'card description',
            'sort_order' => 'display order',
            'image' => 'card image',
        ]);

        unset($data['image']);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    /* ---------- Admin: Page Settings > Home Page > Team Section ---------- */

    public function teamIndex(Request $request)
    {
        $sorts = ['member' => 'name', 'order' => 'sort_order', 'status' => 'is_active'];
        $sort = array_key_exists($request->query('sort'), $sorts) ? $request->query('sort') : 'order';
        $dir = $request->query('dir') === 'desc' ? 'desc' : 'asc';
        $showAll = $request->query('per_page') === 'all';
        $perPage = $showAll ? 'all' : (in_array((int) $request->query('per_page'), [10, 25, 50, 100], true) ? (int) $request->query('per_page') : 10);
        $q = trim((string) $request->query('q', ''));

        $query = TeamMember::query();
        if ($q !== '') {
            $like = '%' . addcslashes(mb_substr($q, 0, 100), '%_\\') . '%';
            $query->where(fn ($w) => $w->where('name', 'like', $like)->orWhere('designation', 'like', $like));
        }
        $query->orderBy($sorts[$sort], $dir);
        if ($sort !== 'order') {
            $query->orderBy('sort_order');
        }
        $query->orderBy('id', $dir);

        $members = $query->paginate($showAll ? max(1, (clone $query)->count()) : $perPage)->withQueryString();
        if ($members->isEmpty() && $members->currentPage() > 1) {
            return redirect()->route('admin.team.index', array_merge($request->query(), ['page' => $members->lastPage()]));
        }

        return view('admin.team.index', [
            'team' => TeamSection::current(),
            'members' => $members,
            'sort' => $sort, 'dir' => $dir, 'perPage' => $perPage, 'q' => $q,
            // computed over every member, not just the visible page
            'totalMembers' => TeamMember::count(),
            'allActive' => TeamMember::count() > 0 && ! TeamMember::where('is_active', false)->exists(),
            'nextOrder' => (TeamMember::max('sort_order') ?? 0) + 1,
        ]);
    }

    public function teamSettings(Request $request)
    {
        $c = config('banner.team');
        $w = $c['word_max'];

        $data = $request->validate([
            'eyebrow' => TextRules::line(true, $c['eyebrow_min'], $c['eyebrow_max'], $w),
            'heading' => TextRules::line(true, $c['heading_min'], $c['heading_max'], $w),
            'heading_accent' => TextRules::line(true, $c['accent_min'], $c['accent_max'], $w),
            'description' => TextRules::line(true, $c['description_min'], $c['description_max'], $w),
        ], TextRules::messages(['eyebrow', 'heading', 'heading_accent', 'description']), [
            'eyebrow' => 'section label',
            'heading' => 'heading',
            'heading_accent' => 'highlighted heading word',
            'description' => 'description',
        ]);
        $data['is_active'] = $request->boolean('is_active');

        TeamSection::current()->fill($data)->save();

        return back()->with('success', 'Team section saved.');
    }

    public function teamStore(Request $request)
    {
        $data = $this->teamValidated($request, true);
        $data['image'] = Uploads::store($request->file('image'), 'team');
        TeamMember::create($data);

        return redirect()->route('admin.team.index')->with('success', 'Team member added.');
    }

    public function teamUpdate(Request $request, TeamMember $member)
    {
        $data = $this->teamValidated($request, false);

        if ($request->hasFile('image')) {
            Uploads::delete($member->image);
            $data['image'] = Uploads::store($request->file('image'), 'team');
        }

        $member->update($data);

        return redirect()->route('admin.team.index')->with('success', 'Team member updated.');
    }

    public function teamDestroy(TeamMember $member)
    {
        Uploads::delete($member->image);
        $member->delete();

        return redirect()->route('admin.team.index')->with('success', 'Team member deleted.');
    }

    public function teamToggle(Request $request, TeamMember $member)
    {
        $member->update(['is_active' => $request->boolean('is_active')]);

        return response()->json(['is_active' => $member->is_active]);
    }

    public function teamToggleAll(Request $request)
    {
        TeamMember::query()->update(['is_active' => $request->boolean('is_active')]);

        return response()->json(['is_active' => $request->boolean('is_active')]);
    }

    private function teamValidated(Request $request, bool $creating): array
    {
        $c = config('banner.team_member');
        $i = $c['image'];
        $w = $c['word_max'];

        $rules = [
            'name' => TextRules::lineNoDigits(true, $c['name_min'], $c['name_max'], $w),
            'designation' => TextRules::lineNoDigits(true, $c['designation_min'], $c['designation_max'], $w),
            'sort_order' => ['nullable', 'integer', 'min:0', "max:{$c['order_max']}"],
            'image' => BannerRules::image($creating, 'team_member.image'),
        ];

        $messages = BannerRules::messages() + TextRules::messagesNoDigits(['name', 'designation']) + [
            'image.dimensions' => "The image must be between {$i['min_width']}x{$i['min_height']} and {$i['max_width']}x{$i['max_height']} pixels.",
            'image.max' => 'The image may not be larger than ' . round($i['max_kb'] / 1024) . ' MB.',
        ];

        $data = $request->validate($rules, $messages, [
            'name' => 'member name',
            'designation' => 'designation',
            'sort_order' => 'display order',
            'image' => 'member photo',
        ]);

        unset($data['image']);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    /* ---------- Public: program detail page ---------- */

    public function programShow(ProgramCard $card)
    {
        abort_unless($card->is_active, 404);

        return view('program-detail', ['card' => $card, 'help' => ProgramSection::current()]);
    }

    /* ---------- Admin: Page Settings > Home Page > Programs Section ---------- */

    public function programIndex()
    {
        return view('admin.program.index', [
            'program' => ProgramSection::current(),
            'cards' => ProgramCard::orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function programSettings(Request $request)
    {
        $c = config('banner.program');
        $w = $c['word_max'];

        $data = $request->validate([
            'eyebrow' => TextRules::lineNoDigits(true, $c['eyebrow_min'], $c['eyebrow_max'], $w),
            'heading_accent' => TextRules::lineNoDigits(true, $c['accent_min'], $c['accent_max'], $w),
            'heading' => TextRules::lineNoDigits(true, $c['heading_min'], $c['heading_max'], $w),
            'description' => TextRules::lineNoDigits(true, $c['description_min'], $c['description_max'], $w),
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

    public function programHelpSettings(Request $request)
    {
        $h = config('banner.program_help');

        $data = $request->validate([
            'help_image' => BannerRules::image(false, 'program_help.image'),
            'help_heading' => TextRules::lineNoDigits(false, $h['heading_min'], $h['heading_max'], $h['word_max']),
            'help_description' => TextRules::lineNoDigits(false, $h['description_min'], $h['description_max'], $h['word_max']),
        ], TextRules::messagesNoDigits(['help_heading', 'help_description']) + [
            'help_image.dimensions' => "The card image must be between {$h['image']['min_width']}x{$h['image']['min_height']} and {$h['image']['max_width']}x{$h['image']['max_height']} pixels.",
            'help_image.max' => 'The card image may not be larger than ' . round($h['image']['max_kb'] / 1024) . ' MB.',
        ], [
            'help_image' => 'card image',
            'help_heading' => 'card heading',
            'help_description' => 'card description',
        ]);
        unset($data['help_image']);
        $data['help_active'] = $request->boolean('help_active');

        $section = ProgramSection::current();
        if ($request->boolean('remove_help_image') && ! $request->hasFile('help_image')) {
            Uploads::delete($section->help_image);
            $data['help_image'] = null;
        }
        if ($request->hasFile('help_image')) {
            Uploads::delete($section->help_image);
            $data['help_image'] = Uploads::store($request->file('help_image'), 'program-help');
        }
        $section->fill($data)->save();

        return back()->with('success', 'Contact card saved.');
    }

    public function programStore(Request $request)
    {
        $data = $this->programValidated($request, true);
        $data['image'] = Uploads::store($request->file('image'), 'program');
        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = Uploads::store($request->file('banner_image'), 'program-banner');
        }
        $this->programApplyIcons($request, $data, null);
        ProgramCard::create($data);

        return redirect()->route('admin.program.index')->with('success', 'Program card created.');
    }

    public function programUpdate(Request $request, ProgramCard $card)
    {
        $data = $this->programValidated($request, false);

        if ($request->hasFile('image')) {
            Uploads::delete($card->image);
            $data['image'] = Uploads::store($request->file('image'), 'program');
        }
        if ($request->hasFile('banner_image')) {
            Uploads::delete($card->banner_image);
            $data['banner_image'] = Uploads::store($request->file('banner_image'), 'program-banner');
        }

        $this->programApplyIcons($request, $data, $card);

        $card->update($data);

        return redirect()->route('admin.program.index')->with('success', 'Program card updated.');
    }

    public function programDestroy(ProgramCard $card)
    {
        Uploads::delete($card->image);
        Uploads::delete($card->banner_image);
        foreach ($this->programIconPaths($card) as $path) {
            Uploads::delete($path);
        }
        $card->delete();

        return redirect()->route('admin.program.index')->with('success', 'Program card deleted.');
    }

    public function programToggle(Request $request, ProgramCard $card)
    {
        $card->update(['is_active' => $request->boolean('is_active')]);

        return response()->json(['is_active' => $card->is_active]);
    }

    public function programToggleAll(Request $request)
    {
        ProgramCard::query()->update(['is_active' => $request->boolean('is_active')]);

        return response()->json(['is_active' => $request->boolean('is_active')]);
    }

    /** Every icon file currently stored on a card (tools + learning formats). */
    private function programIconPaths(?ProgramCard $card): array
    {
        return collect($card?->tool_items ?? [])->pluck('icon')
            ->merge(collect($card?->formats ?? [])->pluck('icon'))
            ->filter()->values()->all();
    }

    /** Turn the submitted tool / format rows into stored items with icons; clean up icon files that are no longer used. */
    private function programApplyIcons(Request $request, array &$data, ?ProgramCard $card): void
    {
        $existing = $this->programIconPaths($card);
        $icon = function (string $path) use ($request, $existing): ?string {
            if ($request->hasFile("{$path}.icon")) {
                return Uploads::store($request->file("{$path}.icon"), 'program-icons');
            }
            $old = (string) $request->input("{$path}.icon_old");

            return in_array($old, $existing, true) ? $old : null; // only keep paths that already belong to this card
        };

        $data['tool_items'] = collect($data['tool_items'] ?? [])
            ->map(fn ($row, $k) => ['name' => $row['name'], 'icon' => $icon("tool_items.{$k}")])
            ->values()->all();
        $data['formats'] = collect($data['formats'] ?? [])
            ->map(fn ($row, $k) => ['title' => $row['title'], 'description' => $row['description'] ?? '', 'icon' => $icon("formats.{$k}")])
            ->values()->all();

        $used = collect($data['tool_items'])->pluck('icon')->merge(collect($data['formats'])->pluck('icon'))->filter()->all();
        foreach (array_diff($existing, $used) as $path) {
            Uploads::delete($path);
        }
    }

    private function programValidated(Request $request, bool $creating): array
    {
        $lv = config('banner.program_levels');
        $ex = config('banner.program_extras');
        // drop tool / format rows left blank (keys are kept so uploaded icons stay matched to their row)
        $keepRow = fn (string $key, array $fields) => collect($request->input($key, []))
            ->filter(fn ($r, $k) => is_array($r)
                && (trim(implode('', array_map(fn ($f) => (string) ($r[$f] ?? ''), $fields))) !== ''
                    || $request->hasFile("{$key}.{$k}.icon") || ! empty($r['icon_old'])))
            ->all();
        $request->merge([
            'tool_items' => $keepRow('tool_items', ['name']),
            'formats' => $keepRow('formats', ['title', 'description']),
        ]);
        // drop level rows the admin left completely blank
        $request->merge(['levels' => collect($request->input('levels', []))
            ->filter(fn ($l) => is_array($l) && trim(($l['title'] ?? '') . ($l['period'] ?? '') . ($l['points'] ?? '')) !== '')
            ->values()->all()]);
        $c = config('banner.program_card');
        $i = $c['image'];
        $w = $c['word_max'];
        $b = config('banner.program_banner');

        $rules = [
            'title' => TextRules::lineNoDigits(true, $c['title_min'], $c['title_max'], $w),
            'features' => ['required', 'string', function (string $attribute, mixed $value, \Closure $fail) use ($c) {
                $lines = ProgramCard::featuresFromHtml($value);
                if ($lines === []) {
                    return $fail('Add at least one feature.');
                }
                if (count($lines) > $c['features_max_items']) {
                    return $fail("Add at most {$c['features_max_items']} features.");
                }
                foreach ($lines as $n => $line) {
                    $pos = $n + 1;
                    $len = mb_strlen($line);
                    if (! preg_match("/^[A-Za-z .,'&!?-]+$/", $line)) {
                        return $fail("Feature {$pos} may only contain letters, spaces and . , ' - & ! ? (no numbers, emojis or special characters).");
                    }
                    if (preg_match('/ {2,}/', $line)) {
                        return $fail("Feature {$pos} may not contain repeated spaces.");
                    }
                    if ($len < $c['feature_min'] || $len > $c['feature_max']) {
                        return $fail("Feature {$pos} must be between {$c['feature_min']} and {$c['feature_max']} characters.");
                    }
                    foreach (preg_split('/\s+/', $line) as $word) {
                        if (mb_strlen($word) > $c['word_max']) {
                            return $fail("Feature {$pos} has a word longer than {$c['word_max']} characters.");
                        }
                    }
                }
            }],
            'duration' => ['required', 'integer', 'min:1', 'max:99'],
            'mode' => TextRules::lineNoDigits(true, $c['mode_min'], $c['mode_max'], $w),
            'sort_order' => ['nullable', 'integer', 'min:0', "max:{$c['order_max']}"],
            'image' => BannerRules::image($creating, 'program_card.image'),
            'about_text' => array_merge(TextRules::lineNoDigits(false, config('banner.program_about.min'), config('banner.program_about.max'), config('banner.program_about.word_max')), [function (string $attribute, mixed $value, \Closure $fail) {
                $max = config('banner.program_about.max_words');
                $words = count(preg_split('/\s+/u', trim((string) $value), -1, PREG_SPLIT_NO_EMPTY));
                if ($words > $max) {
                    $fail("The about this program text may not be longer than {$max} words (currently {$words}).");
                }
            }]),
            // detail-page banner (all optional)
            'banner_image' => BannerRules::image(false, 'program_banner.image'),
            'banner_title' => TextRules::lineNoDigits(false, $b['title_min'], $b['title_max'], $b['word_max']),
            'banner_description' => TextRules::lineNoDigits(false, $b['description_min'], $b['description_max'], $b['word_max']),
            'banner_levels' => ['nullable', 'integer', 'min:1', 'max:99'],
            'banner_timeline' => TextRules::line(false, $b['timeline_min'], $b['timeline_max'], $b['word_max']),
            // tools & digital topics (optional): a name and an optional icon each
            'tool_items' => ['nullable', 'array', "max:{$ex['tools_max']}"],
            'tool_items.*.name' => TextRules::lineNoDigits(true, $ex['tool_min'], $ex['tool_max'], $ex['word_max']),
            'tool_items.*.icon' => BannerRules::image(false, 'program_extras.icon'),
            'formats.*.icon' => BannerRules::image(false, 'program_extras.icon'),
            // learning format cards (optional)
            'formats' => ['nullable', 'array', "max:{$ex['format_max']}"],
            'formats.*.title' => TextRules::lineNoDigits(true, $ex['format_title_min'], $ex['format_title_max'], $ex['word_max']),
            'formats.*.description' => TextRules::lineNoDigits(false, $ex['format_desc_min'], $ex['format_desc_max'], $ex['word_max']),
            // curriculum points for programs without levels (optional)
            'curriculum_points' => ['nullable', 'string', function (string $attribute, mixed $value, \Closure $fail) use ($lv) {
                $lines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $value)), 'strlen'));
                if (count($lines) > $lv['points_max']) {
                    return $fail("Add at most {$lv['points_max']} curriculum points.");
                }
                foreach ($lines as $k => $line) {
                    $p = $k + 1;
                    $len = mb_strlen($line);
                    if (! preg_match("/^[A-Za-z .,'&!?-]+$/", $line)) {
                        return $fail("Curriculum point {$p}: only letters, spaces and . , ' - & ! ? are allowed.");
                    }
                    if (preg_match('/ {2,}/', $line)) {
                        return $fail("Curriculum point {$p}: repeated spaces are not allowed.");
                    }
                    if ($len < $lv['point_min'] || $len > $lv['point_max']) {
                        return $fail("Curriculum point {$p}: must be {$lv['point_min']}-{$lv['point_max']} characters (currently {$len}).");
                    }
                    foreach (preg_split('/\s+/', $line) as $word) {
                        if (mb_strlen($word) > $lv['word_max']) {
                            return $fail("Curriculum point {$p}: a word is longer than {$lv['word_max']} characters.");
                        }
                    }
                }
            }],
            // curriculum levels (all optional)
            'levels' => ['nullable', 'array', "max:{$lv['max_levels']}"],
            'levels.*.title' => TextRules::lineNoDigits(false, $lv['title_min'], $lv['title_max'], $lv['word_max']),
            'levels.*.period' => ['nullable', 'integer', 'min:1', 'max:99'],
            'levels.*.points' => ['required', 'string', function (string $attribute, mixed $value, \Closure $fail) use ($lv) {
                $lines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $value)), 'strlen'));
                $n = (int) explode('.', $attribute)[1] + 1;
                if ($lines === []) {
                    return $fail("Level {$n}: add at least one point.");
                }
                if (count($lines) > $lv['points_max']) {
                    return $fail("Level {$n}: add at most {$lv['points_max']} points.");
                }
                foreach ($lines as $k => $line) {
                    $p = $k + 1;
                    $len = mb_strlen($line);
                    if (! preg_match("/^[A-Za-z .,'&!?-]+$/", $line)) {
                        return $fail("Level {$n}, point {$p}: only letters, spaces and . , ' - & ! ? are allowed.");
                    }
                    if (preg_match('/ {2,}/', $line)) {
                        return $fail("Level {$n}, point {$p}: repeated spaces are not allowed.");
                    }
                    if ($len < $lv['point_min'] || $len > $lv['point_max']) {
                        return $fail("Level {$n}, point {$p}: must be {$lv['point_min']}-{$lv['point_max']} characters (currently {$len}).");
                    }
                    foreach (preg_split('/\s+/', $line) as $word) {
                        if (mb_strlen($word) > $lv['word_max']) {
                            return $fail("Level {$n}, point {$p}: a word is longer than {$lv['word_max']} characters.");
                        }
                    }
                }
            }],
        ];

        $messages = BannerRules::messages()
            + TextRules::messages(['duration', 'banner_timeline']) + TextRules::messagesNoDigits(['title', 'mode', 'about_text', 'banner_title', 'banner_description', 'levels.*.title', 'tool_items.*.name', 'formats.*.title', 'formats.*.description'])
            + [
                'banner_image.dimensions' => "The banner image must be between {$b['image']['min_width']}x{$b['image']['min_height']} and {$b['image']['max_width']}x{$b['image']['max_height']} pixels.",
                'banner_image.max' => 'The banner image may not be larger than ' . round($b['image']['max_kb'] / 1024) . ' MB.',
                'image.dimensions' => "The image must be between {$i['min_width']}x{$i['min_height']} and {$i['max_width']}x{$i['max_height']} pixels.",
                'image.max' => 'The image may not be larger than ' . round($i['max_kb'] / 1024) . ' MB.',
            ];

        $data = $request->validate($rules, $messages, [
            'title' => 'program title',
            'features' => 'features',
            'duration' => 'duration',
            'mode' => 'study mode',
            'sort_order' => 'display order',
            'image' => 'program image',
            'banner_image' => 'banner image',
            'about_text' => 'about this program text',
            'banner_title' => 'banner title',
            'banner_description' => 'banner description',
            'banner_levels' => 'banner levels text',
            'banner_timeline' => 'banner timeline text',
            'curriculum_points' => 'curriculum points',
            'tool_items.*.name' => 'tool name',
            'tool_items.*.icon' => 'tool icon',
            'formats.*.icon' => 'format icon',
            'formats.*.title' => 'format title',
            'formats.*.description' => 'format description',
            'levels.*.title' => 'level title',
            'levels.*.period' => 'level period',
            'levels.*.points' => 'level points',
        ]);

        unset($data['image'], $data['banner_image']);
        $data['curriculum_points'] = implode("\n", array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) ($data['curriculum_points'] ?? ''))), 'strlen')));
        $data['levels'] = collect($data['levels'] ?? [])->map(fn ($l) => [
            'title' => $l['title'] ?? '',
            'period' => isset($l['period']) ? (string) (int) $l['period'] : '',
            'points' => array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $l['points'])), 'strlen')),
        ])->values()->all();
        $data['features'] = implode("\n", ProgramCard::featuresFromHtml($data['features']));
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
