{{--
    Page Settings (dropdown) > one dropdown per website page > that page's sections.
    To add a page: copy one of the <details class="menu-sub"> blocks below.
--}}
@php($inHome = request()->routeIs('admin.banners.*', 'admin.home-text.*', 'admin.about.*', 'admin.audience.*', 'admin.problem.*', 'admin.program.*', 'admin.team.*', 'admin.insight.*', 'admin.reel.*', 'admin.homecontent.*'))
@php($inAbout = request()->routeIs('admin.aboutpage.*'))
@php($inContact = request()->routeIs('admin.contactpage.*'))
@php($inInsights = request()->routeIs('admin.insightpage.*'))
@php($inPrograms = request()->routeIs('admin.programpage.*'))
<details class="menu-group" @if($inHome || $inAbout || $inContact || $inInsights || $inPrograms) open @endif>
    <summary>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M9 20V9"/></svg>
        <span>Page Settings</span>
    </summary>
    <div class="submenu">
        {{-- Home Page --}}
        <details class="menu-sub" @if($inHome) open @endif>
            <summary>Home Page</summary>
            <a href="{{ route('admin.banners.index') }}" class="{{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">Banner Settings</a>
            <a href="{{ route('admin.home-text.edit') }}" class="{{ request()->routeIs('admin.home-text.*') ? 'active' : '' }}">Homepage Content</a>
            <a href="{{ route('admin.about.edit') }}" class="{{ request()->routeIs('admin.about.*') ? 'active' : '' }}">About Section</a>
            <a href="{{ route('admin.audience.index') }}" class="{{ request()->routeIs('admin.audience.*') ? 'active' : '' }}">Target Audience Section</a>
            <a href="{{ route('admin.problem.edit') }}" class="{{ request()->routeIs('admin.problem.*') ? 'active' : '' }}">Problem Section</a>
            <a href="{{ route('admin.program.index') }}" class="{{ request()->routeIs('admin.program.*') ? 'active' : '' }}">Programs Section</a>
            <a href="{{ route('admin.team.index') }}" class="{{ request()->routeIs('admin.team.*') ? 'active' : '' }}">Team Section</a>
            <a href="{{ route('admin.homecontent.edit') }}" class="{{ request()->routeIs('admin.homecontent.*') ? 'active' : '' }}">Content Section</a>
            {{-- Home Insights Section (hidden: the home page now shows the Insights-page articles)
            <a href="{{ route('admin.insight.index') }}" class="{{ request()->routeIs('admin.insight.*') ? 'active' : '' }}">Insights Section</a>
            --}}
            <a href="{{ route('admin.reel.index') }}" class="{{ request()->routeIs('admin.reel.*') ? 'active' : '' }}">Video Section</a>
        </details>

        {{-- Programs Page --}}
        <details class="menu-sub" @if($inPrograms) open @endif>
            <summary>Programs Page</summary>
            <a href="{{ route('admin.programpage.banner.edit') }}" class="{{ request()->routeIs('admin.programpage.banner.*') ? 'active' : '' }}">Banner Settings</a>
            <a href="{{ route('admin.programpage.points.index') }}" class="{{ request()->routeIs('admin.programpage.points.*') ? 'active' : '' }}">Highlight Points</a>
        </details>

        {{-- About Page --}}
        <details class="menu-sub" @if($inAbout) open @endif>
            <summary>About Page</summary>
            <a href="{{ route('admin.aboutpage.banner.edit') }}" class="{{ request()->routeIs('admin.aboutpage.banner.*') ? 'active' : '' }}">Banner Settings</a>
            <a href="{{ route('admin.aboutpage.story.edit') }}" class="{{ request()->routeIs('admin.aboutpage.story.*') ? 'active' : '' }}">Our Story Section</a>
            <a href="{{ route('admin.aboutpage.principles.index') }}" class="{{ request()->routeIs('admin.aboutpage.principles.*') ? 'active' : '' }}">Guiding Principles</a>
            <a href="{{ route('admin.aboutpage.team.edit') }}" class="{{ request()->routeIs('admin.aboutpage.team.*') ? 'active' : '' }}">Our Team Section</a>
            <a href="{{ route('admin.aboutpage.content.edit') }}" class="{{ request()->routeIs('admin.aboutpage.content.*') ? 'active' : '' }}">Content Section</a>
            <a href="{{ route('admin.aboutpage.cta.edit') }}" class="{{ request()->routeIs('admin.aboutpage.cta.*') ? 'active' : '' }}">Get In Touch Section</a>
        </details>

        {{-- Contact Page --}}
        <details class="menu-sub" @if($inContact) open @endif>
            <summary>Contact Page</summary>
            <a href="{{ route('admin.contactpage.banner.edit') }}" class="{{ request()->routeIs('admin.contactpage.banner.*') ? 'active' : '' }}">Banner Settings</a>
            <a href="{{ route('admin.contactpage.info.edit') }}" class="{{ request()->routeIs('admin.contactpage.info.*') ? 'active' : '' }}">Get In Touch Section</a>
            <a href="{{ route('admin.contactpage.messages.index') }}" class="{{ request()->routeIs('admin.contactpage.messages.*') ? 'active' : '' }}">Enquiries</a>
        </details>

        {{-- Insights Page --}}
        <details class="menu-sub" @if($inInsights) open @endif>
            <summary>Insights Page</summary>
            <a href="{{ route('admin.insightpage.banner.edit') }}" class="{{ request()->routeIs('admin.insightpage.banner.*') ? 'active' : '' }}">Banner Settings</a>
            <a href="{{ route('admin.insightpage.cta.edit') }}" class="{{ request()->routeIs('admin.insightpage.cta.*') ? 'active' : '' }}">Insight Detail Card</a>
            <a href="{{ route('admin.insightpage.featured.index') }}" class="{{ request()->routeIs('admin.insightpage.featured.*') ? 'active' : '' }}">Featured Insights</a>
            <a href="{{ route('admin.insightpage.articles.index') }}" class="{{ request()->routeIs('admin.insightpage.articles.*') ? 'active' : '' }}">Insight Articles</a>
        </details>
    </div>
</details>
