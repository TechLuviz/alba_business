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
        function ready() { setTimeout(hide, Math.max(0, 450 - (Date.now() - start))); } // never just a flash
        if (document.readyState === 'complete') ready(); else window.addEventListener('load', ready);
        window.addEventListener('pageshow', function (e) { if (e.persisted) hide(); }); // back/forward cache
        setTimeout(hide, 6000); // safety: never block the site
    })();
</script>
