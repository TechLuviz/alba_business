<header class="site-header">
    <div class="container header-inner">
        <button type="button" class="nav-toggle" id="navToggle" aria-label="Open menu" aria-expanded="false" aria-controls="siteNav">
            <span></span><span></span><span></span>
        </button>
        <a href="{{ route('home') }}" class="logo">
            @if($brand->brand_logo)
                <img src="{{ asset($brand->brand_logo) }}" alt="{{ $brand->brand_name }}" class="logo-img">
            @else
                @php($parts = explode(' ', $brand->brand_name ?: 'ALBATECH HUB', 2))
                <span class="logo-name">{{ $parts[0] }} @if(isset($parts[1]))<span class="logo-light">{{ $parts[1] }}</span>@endif <sup>TM</sup></span>
                <span class="logo-sub">{{ strtoupper($brand->brand_tagline ?: 'School of Business') }}</span>
            @endif
        </a>
        <nav class="nav-pill" id="siteNav">
            <button type="button" class="nav-close" id="navClose" aria-label="Close menu">&times;</button>
            {{-- Static Home link (skipped if a "Home" item already exists in the menu) --}}
            @unless($navItems->contains(fn ($n) => strtolower(trim($n->label)) === 'home'))
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'is-current' : '' }}">Home</a>
            @endunless
            @foreach($navItems as $item)
<?php
                    $href = str_starts_with($item->url, '#') ? route('home') . $item->url : (str_starts_with($item->url, '/') ? url($item->url) : $item->url);
                    // Highlight the item whose page we are on (same path, or a sub-page such as /insights/{slug}).
                    $itemPath = trim((string) parse_url($href, PHP_URL_PATH), '/');
                    $itemHost = parse_url($href, PHP_URL_HOST);
                    if (str_starts_with($item->url, '#')) {
                        // Section-anchor items (e.g. #programs) belong to the page that section lives on.
                        $isCurrent = match (strtolower(ltrim($item->url, '#'))) {
                            'programs', 'program' => request()->routeIs('programs.show'),
                            'about' => request()->routeIs('about'),
                            'contact' => request()->routeIs('contact'),
                            'insight', 'insights' => request()->routeIs('insights', 'insights.show'),
                            default => false,
                        };
                    } else {
                        // Compare full URLs so it also works when the site lives in a sub-folder (e.g. localhost/Alba_business).
                        $currentUrl = rtrim(request()->url(), '/');
                        $targetUrl = rtrim((string) strtok($href, '?#'), '/');
                        $isCurrent = $targetUrl === rtrim(url('/'), '/')
                            ? $currentUrl === $targetUrl
                            : ($currentUrl === $targetUrl || str_starts_with($currentUrl, $targetUrl . '/'));
                    }
                ?>
                <a href="{{ $href }}" class="{{ $isCurrent ? 'is-current' : '' }}" @if($isCurrent) aria-current="page" @endif>{{ $item->label }}</a>
            @endforeach
        </nav>
    </div>
</header>
<div class="nav-backdrop" id="navBackdrop"></div>
<script>
    (function () {
        const nav = document.getElementById('siteNav'), toggle = document.getElementById('navToggle'),
              backdrop = document.getElementById('navBackdrop'), close = document.getElementById('navClose');
        function set(open) {
            nav.classList.toggle('open', open);
            backdrop.classList.toggle('show', open);
            toggle.setAttribute('aria-expanded', open);
            document.body.style.overflow = open ? 'hidden' : '';
        }
        toggle.addEventListener('click', () => set(true));
        close.addEventListener('click', () => set(false));
        backdrop.addEventListener('click', () => set(false));
        nav.querySelectorAll('a').forEach(a => a.addEventListener('click', () => set(false)));
        document.addEventListener('keydown', e => { if (e.key === 'Escape') set(false); });
        window.matchMedia('(min-width: 769px)').addEventListener('change', e => { if (e.matches) set(false); });
    })();
</script>
