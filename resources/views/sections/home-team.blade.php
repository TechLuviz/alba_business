@if(isset($teamSection) && $teamSection->is_active && $teamMembers->isNotEmpty())
    <section class="home-team" id="team">
        <div class="container">
            <div class="team-head">
                <div class="team-head-copy">
                    @if($teamSection->eyebrow)
                        <p class="team-eyebrow"><span></span>{{ $teamSection->eyebrow }}</p>
                    @endif
                    <h2>{{ $teamSection->heading }} <em>{{ $teamSection->heading_accent }}</em></h2>
                    @if($teamSection->description)<p class="team-desc">{{ $teamSection->description }}</p>@endif
                </div>
                {{-- Static button: change the text / link here in the code --}}
                <a href="{{ route('about') }}#our-team" class="team-viewall">View All <span aria-hidden="true">&rarr;</span></a>
            </div>

            <div class="team-row" id="teamRow">
                @foreach($teamMembers as $i => $member)
                    <article class="team-card" tabindex="0" role="button" aria-expanded="false" aria-label="{{ $member->name }}, {{ $member->designation }}">
                        @if($member->image)
                            <img src="{{ asset($member->image) }}" alt="{{ $member->name }}" loading="lazy">
                        @endif
                        <div class="team-info">
                            <h3>{{ $member->name }}</h3>
                            <p>{{ $member->designation }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        (function () {
            const cards = Array.from(document.querySelectorAll('#teamRow .team-card'));
            if (!cards.length) return;
            function open(k) {
                // phones are scroll-driven (see setup below); taps/keys must not override it
                if (window.matchMedia('(max-width: 768px)').matches) return;
                cards.forEach((c, j) => { c.classList.toggle('is-open', j === k); c.setAttribute('aria-expanded', j === k); });
            }
            cards.forEach((c, k) => {
                c.addEventListener('click', () => open(k));
                c.addEventListener('keydown', e => {
                    if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); open(k); }
                    else if (e.key === 'ArrowRight' || e.key === 'ArrowDown') { e.preventDefault(); cards[(k + 1) % cards.length].focus(); open((k + 1) % cards.length); }
                    else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') { e.preventDefault(); const p = (k - 1 + cards.length) % cards.length; cards[p].focus(); open(p); }
                });
            });
            // desktop: the second card starts enlarged (click to switch).
            // phones: cards are stacked, so each one turns to colour on its own while it is in the
            // middle band of the screen and fades back to B&W when it scrolls out. No tap needed.
            const mq = window.matchMedia('(max-width: 768px)');
            let io = null;
            function setup() {
                if (io) { io.disconnect(); io = null; }
                if (!mq.matches) {
                    open(Math.min(1, cards.length - 1));
                    return;
                }
                cards.forEach(c => { c.classList.remove('is-open'); c.setAttribute('aria-expanded', 'false'); });
                if (!('IntersectionObserver' in window)) {
                    cards.forEach(c => c.classList.add('is-open'));
                    return;
                }
                // only the single card whose centre is closest to the middle of the screen is in colour
                let raf = 0;
                function pick() {
                    raf = 0;
                    const mid = window.innerHeight / 2;
                    let best = -1, bestD = Infinity;
                    cards.forEach((c, j) => {
                        const r = c.getBoundingClientRect();
                        if (r.bottom < 0 || r.top > window.innerHeight) return;
                        const d = Math.abs(r.top + r.height / 2 - mid);
                        if (d < bestD) { bestD = d; best = j; }
                    });
                    // too far from the centre (section only partly in view) -> none in colour
                    if (bestD > window.innerHeight * 0.35) best = -1;
                    cards.forEach((c, j) => { c.classList.toggle('is-open', j === best); c.setAttribute('aria-expanded', j === best); });
                }
                const onScroll = () => { if (!raf) raf = requestAnimationFrame(pick); };
                window.addEventListener('scroll', onScroll, { passive: true });
                window.addEventListener('resize', onScroll);
                io = { disconnect() { window.removeEventListener('scroll', onScroll); window.removeEventListener('resize', onScroll); } };
                pick();
            }
            mq.addEventListener ? mq.addEventListener('change', setup) : mq.addListener(setup);
            setup();
        })();
    </script>
    @endpush
@endif
