{{-- Page loader: shown until the page has finished loading (all public pages). Hidden without JavaScript and after 6s at the latest. --}}
@php($loaderBrand = \App\Models\HeroBanner::current())
<noscript><style>#pageLoader { display: none !important; }</style></noscript>
<div class="page-loader" id="pageLoader" role="status" aria-live="polite" aria-label="Loading">
    <div class="page-loader-inner">
        <div class="page-loader-mark">
            <i class="page-loader-ring" aria-hidden="true"></i>
            @if($loaderBrand->brand_logo)
                <img src="{{ asset($loaderBrand->brand_logo) }}" alt="" class="page-loader-logo">
            @else
                <span class="page-loader-text">{{ $loaderBrand->brand_name ?: 'Albahub' }}</span>
            @endif
        </div>
        <span class="page-loader-bar" aria-hidden="true"></span>
    </div>
</div>
<script>
    (function () {
        var el = document.getElementById('pageLoader'), start = Date.now(), done = false;
        function hide() {
            if (done || !el) return; done = true;
            el.classList.add('is-done');
            setTimeout(function () { if (el.parentNode) el.parentNode.removeChild(el); }, 700);
        }
        // Reveal only once the page is final: window loaded, web fonts applied (no text restyling after reveal)
        // and the hero video's first frame decoded (no fade-in after reveal).
        function fontsReady() { return (document.fonts && document.fonts.ready) ? document.fonts.ready : Promise.resolve(); }
        function videoReady() {
            return new Promise(function (res) {
                var v = document.querySelector('.hero-video');
                if (!v || v.readyState >= 2) return res();
                v.addEventListener('loadeddata', res, { once: true });
                v.addEventListener('error', res, { once: true });
                setTimeout(res, 2500);
            });
        }
        function ready() {
            Promise.all([fontsReady(), videoReady()]).then(function () {
                requestAnimationFrame(function () { requestAnimationFrame(function () {
                    setTimeout(hide, Math.max(0, 350 - (Date.now() - start))); // never just a flash
                }); });
            });
        }
        if (document.readyState === 'complete') ready(); else window.addEventListener('load', ready);
        window.addEventListener('pageshow', function (e) { // back/forward cache (Firefox/Safari): drop any leaving overlay
            if (e.persisted) { hide(); var l = document.querySelector('.page-leave'); if (l) l.classList.remove('is-on'); }
        });
        // Leaving the page: fade to the dark loading colour first, so the browser never shows a white/blank flash.
        document.addEventListener('click', function (ev) {
            var a = ev.target.closest && ev.target.closest('a[href]');
            if (!a || ev.defaultPrevented || ev.button !== 0 || ev.metaKey || ev.ctrlKey || ev.shiftKey || ev.altKey) return;
            if ((a.target && a.target !== '_self') || a.hasAttribute('download')) return;
            var u; try { u = new URL(a.href, location.href); } catch (e) { return; }
            if (u.origin !== location.origin || !/^https?:$/.test(u.protocol)) return;
            if (u.pathname === location.pathname && u.search === location.search) return; // same-page / anchor links
            ev.preventDefault();
            var l = document.querySelector('.page-leave');
            if (!l) { l = document.createElement('div'); l.className = 'page-leave'; document.body.appendChild(l); }
            requestAnimationFrame(function () { l.classList.add('is-on'); });
            setTimeout(function () { location.href = a.href; }, 230);
        });
        setTimeout(hide, 6000); // safety: never block the site
    })();
</script>
