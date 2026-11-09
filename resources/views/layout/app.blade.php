<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php($seo = $seoOverride ?? \App\Models\SeoSetting::where('page', request()->route()?->getName())->first())
    <title>{{ $seo?->meta_title ?: trim($__env->yieldContent('title', config('app.name'))) }}</title>
    @if($seo?->meta_description)
        <meta name="description" content="{{ $seo->meta_description }}">
    @endif
    @if(data_get($seo, 'focus_keyword'))
        <meta name="keywords" content="{{ data_get($seo, 'focus_keyword') }}">
    @endif
    @include('layout.favicon')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v={{ filemtime(public_path('assets/css/style.css')) }}">
    @stack('styles')
</head>
<body>
    @include('layout.loader')

    @include('layout.header')

    <main>
        @yield('content')
    </main>

    @include('layout.footer')

    @include('layout.quick-contact')

    <script src="{{ asset('assets/js/cf-select.js') }}?v={{ @filemtime(public_path('assets/js/cf-select.js')) }}"></script>
    <script>
        // Links, buttons, images, icons and videos can't be dragged (no ghost box with the link name/URL following the cursor).
        document.addEventListener('dragstart', function (e) {
            if (e.target && e.target.closest && e.target.closest('a, img, svg, button, video, picture')) e.preventDefault();
        });
    </script>
    <script>
        // Header: hides while scrolling down, slides back in while scrolling up; always shown at the top of the page.
        (function () {
            var header = document.querySelector('.site-header'), toggle = document.getElementById('navToggle');
            if (!header) return;
            var last = window.pageYOffset, ticking = false;
            function update() {
                ticking = false;
                var y = Math.max(0, window.pageYOffset), d = y - last;
                if (y <= 10) { header.classList.remove('is-hidden', 'is-stuck'); last = y; return; }
                if (Math.abs(d) < 6) return;
                var navOpen = toggle && toggle.getAttribute('aria-expanded') === 'true';
                if (d > 0 && !navOpen) header.classList.add('is-hidden');
                else if (d < 0) { header.classList.remove('is-hidden'); header.classList.add('is-stuck'); }
                last = y;
            }
            window.addEventListener('scroll', function () { if (!ticking) { ticking = true; requestAnimationFrame(update); } }, { passive: true });
        })();
    </script>
    @stack('scripts')
</body>
</html>