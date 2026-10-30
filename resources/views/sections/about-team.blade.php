@if(isset($aboutTeam) && $aboutTeam->is_active && $teamMembers->isNotEmpty())
    <section class="about-team" id="our-team">
        <div class="container">
            @if($aboutTeam->eyebrow)
                <p class="about-team-eyebrow"><span></span>{{ $aboutTeam->eyebrow }}<span></span></p>
            @endif
            <h2><span>{{ $aboutTeam->heading }}</span><em>{{ $aboutTeam->heading_accent }}</em></h2>

            <div class="team-orbit" id="teamOrbit" role="region" aria-roledescription="carousel" aria-label="Our team">
                @foreach($teamMembers as $member)
                    <article class="orbit-card" tabindex="0" aria-label="{{ $member->name }}, {{ $member->designation }}">
                        <div class="orbit-photo">
                            @if($member->image)<img src="{{ asset($member->image) }}" alt="{{ $member->name }}" loading="lazy">@endif
                        </div>
                        <div class="orbit-info">
                            <i></i>
                            <strong>{{ $member->name }}</strong>
                            <span>{{ $member->designation }}</span>
                        </div>
                    </article>
                @endforeach
            </div>

            @if($teamMembers->count() > 1)
                <div class="orbit-nav">
                    <button type="button" class="orbit-btn" id="orbitPrev" aria-label="Previous member">&larr;</button>
                    <div class="orbit-dots" id="orbitDots">
                        @foreach($teamMembers as $i => $member)
                            <button type="button" aria-label="Show {{ $member->name }}"></button>
                        @endforeach
                    </div>
                    <button type="button" class="orbit-btn" id="orbitNext" aria-label="Next member">&rarr;</button>
                </div>
            @endif
        </div>
    </section>

    @push('scripts')
    <script>
        (function () {
            const stage = document.getElementById('teamOrbit');
            const cards = Array.from(stage.querySelectorAll('.orbit-card'));
            const dots = Array.from(document.querySelectorAll('#orbitDots button'));
            const n = cards.length;
            let active = 0, timer = null, hovered = false;

            function layout() {
                cards.forEach((c, i) => {
                    let d = (i - active + n) % n;
                    if (d > n / 2) d -= n;
                    c.style.setProperty('--slot', d);
                    c.style.setProperty('--abs', Math.min(Math.abs(d), 3));
                    c.classList.toggle('is-active', d === 0);
                    c.classList.toggle('is-away', Math.abs(d) > 2);
                    c.setAttribute('aria-current', d === 0 ? 'true' : 'false');
                });
                dots.forEach((b, i) => b.classList.toggle('is-on', i === active));
            }
            function go(i) { active = (i + n) % n; layout(); }
            function restart() {
                clearInterval(timer);
                if (n < 2) return;
                timer = setInterval(() => { if (!hovered && !document.hidden) go(active + 1); }, 4000);
            }

            document.getElementById('orbitPrev')?.addEventListener('click', () => { go(active - 1); restart(); });
            document.getElementById('orbitNext')?.addEventListener('click', () => { go(active + 1); restart(); });
            dots.forEach((b, i) => b.addEventListener('click', () => { go(i); restart(); }));
            cards.forEach((c, i) => {
                c.addEventListener('click', () => { if (i !== active) { go(i); restart(); } });
                c.addEventListener('keydown', e => {
                    if (e.key === 'ArrowRight') { e.preventDefault(); go(active + 1); restart(); }
                    else if (e.key === 'ArrowLeft') { e.preventDefault(); go(active - 1); restart(); }
                });
            });
            stage.addEventListener('mouseenter', () => { hovered = true; });
            stage.addEventListener('mouseleave', () => { hovered = false; });
            window.addEventListener('pagehide', () => clearInterval(timer));

            let x0 = null;
            stage.addEventListener('touchstart', e => { x0 = e.touches[0].clientX; }, { passive: true });
            stage.addEventListener('touchend', e => {
                if (x0 === null) return;
                const dx = e.changedTouches[0].clientX - x0; x0 = null;
                if (Math.abs(dx) > 40) { go(active + (dx < 0 ? 1 : -1)); restart(); }
            }, { passive: true });

            layout();
            restart();
        })();
    </script>
    @endpush
@endif
