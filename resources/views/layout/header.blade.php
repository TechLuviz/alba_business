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
                <a href="{{ str_starts_with($item->url, '#') ? route('home') . $item->url : (str_starts_with($item->url, '/') ? url($item->url) : $item->url) }}">{{ $item->label }}</a>
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
