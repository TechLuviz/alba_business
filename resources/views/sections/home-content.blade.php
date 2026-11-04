@if(isset($homeContent) && $homeContent->isVisible())
    @php($contentId = $contentId ?? 'homeContent')
    <section class="home-content" id="{{ $contentId }}">
        <div class="container home-content-grid">
            <div class="home-content-text hc-reveal">
                <div class="privacy-content">{!! $homeContent->content !!}</div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        (function () {
            var els = document.querySelectorAll('#{{ $contentId }} .hc-reveal');
            if (!('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            document.documentElement.classList.add('hc-anim');
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
            }, { threshold: 0.15 });
            els.forEach(function (el) { io.observe(el); });
        })();
    </script>
    @endpush
@endif
