@if($textSection->hasContent())
    <section class="home-text" id="homeText" aria-label="{{ trim($textSection->heading_line_1 . ' ' . $textSection->heading_line_2) }}">
        <div class="container">
            <h2 aria-hidden="true">
                @foreach([$textSection->heading_line_1, $textSection->heading_line_2] as $line)
                    @continue(blank($line))
                    <span class="ht-line">
                        @foreach(explode(' ', $line) as $word)
                            <span class="ht-word">@foreach(mb_str_split($word) as $ch)<span class="ht-char">{{ $ch }}</span>@endforeach</span>
                        @endforeach
                    </span>
                @endforeach
            </h2>
        </div>
    </section>

    @push('scripts')
    <script>
        (function () {
            const root = document.getElementById('homeText');
            const chars = root.querySelectorAll('.ht-char');
            const h2 = root.querySelector('h2'), lines = root.querySelectorAll('.ht-line');

            // Keep every line on one row: shrink the font only if a line is wider than the container.
            function fit() {
                h2.style.fontSize = '';
                let size = parseFloat(getComputedStyle(h2).fontSize);
                while (size > 16 && Array.from(lines).some(l => l.scrollWidth > h2.clientWidth)) {
                    size -= 2; h2.style.fontSize = size + 'px';
                }
            }
            fit();
            window.addEventListener('resize', fit);

            if (!chars.length || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

            root.classList.add('is-scrub'); // letters start gray; without JS they stay white
            let ticking = false;

            function update() {
                ticking = false;
                const r = root.getBoundingClientRect(), vh = window.innerHeight;
                // 0 when the section top enters the lower part of the screen, 1 once it has travelled ~45% of the viewport up
                const start = vh * 0.9, end = vh * 0.45;
                const p = Math.min(1, Math.max(0, (start - r.top) / (start - end)));
                const lit = Math.round(p * chars.length);
                chars.forEach((c, i) => c.classList.toggle('on', i < lit));
            }
            function onScroll() { if (!ticking) { ticking = true; requestAnimationFrame(update); } }

            window.addEventListener('scroll', onScroll, { passive: true });
            window.addEventListener('resize', onScroll);
            update();
        })();
    </script>
    @endpush
@endif
