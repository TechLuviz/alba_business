@if(isset($reelSection) && $reelSection->is_active && $reelCards->isNotEmpty())
    <section class="home-reels" id="reels">
        <div class="container">
            <div class="reels-head">
                @if($reelSection->eyebrow)
                    <p class="reels-eyebrow"><span></span>{{ $reelSection->eyebrow }}<span></span></p>
                @endif
                <h2>
                    <span>{{ $reelSection->heading }}</span>
                    <em>{{ $reelSection->heading_accent }}</em>
                </h2>
                @if($reelSection->description)<p class="reels-desc">{{ $reelSection->description }}</p>@endif
            </div>

            <div class="reels-stage" id="reelsStage" aria-roledescription="carousel" aria-label="Videos">
                @foreach($reelCards as $card)
                    @php($embed = $card->embed())
                    <a class="reel-card" href="{{ $card->url }}" target="_blank" rel="noopener noreferrer"
                       aria-label="{{ $card->title }} - watch on {{ $card->platform() ?? 'the web' }} (opens in a new tab)">
                        @if($embed)
                            @if($embed['thumb'])<img class="reel-thumb" src="{{ $embed['thumb'] }}" alt="" loading="lazy" referrerpolicy="no-referrer">@endif
                            <span class="reel-embed" data-src="{{ $embed['embed'] }}"></span>
                        @elseif($card->video)
                            <video src="{{ asset($card->video) }}#t=0.1" muted loop playsinline preload="metadata" tabindex="-1"></video>
                        @endif
                        <span class="reel-shade"></span>
                        <span class="reel-top">
                            <span class="reel-tag">{{ $card->tag }}</span>
                            <button type="button" class="reel-pause" aria-label="Pause">
                                <svg class="i-pause" width="14" height="14" viewBox="0 0 14 14" fill="currentColor"><rect x="2" y="1" width="3.5" height="12" rx="1"/><rect x="8.5" y="1" width="3.5" height="12" rx="1"/></svg>
                                <svg class="i-play" width="14" height="14" viewBox="0 0 14 14" fill="currentColor"><path d="M3 1l10 6-10 6z"/></svg>
                            </button>
                        </span>
                        <span class="reel-label">{{ $card->tag }}</span>
                        <span class="reel-caption">
                            <strong>{{ $card->title }}</strong>
                            <i class="reel-progress"><b></b></i>
                        </span>
                    </a>
                @endforeach
            </div>

            @if($reelCards->count() > 1)
                <div class="reels-dots" id="reelsDots" aria-hidden="true">@foreach($reelCards as $d)<i></i>@endforeach</div>
                <div class="reels-nav">
                    <button type="button" class="reels-nav-btn" id="reelsPrev" aria-label="Previous video">&lsaquo;</button>
                    <button type="button" class="reels-nav-btn is-next" id="reelsNext" aria-label="Next video">&rsaquo;</button>
                </div>
            @endif
        </div>
    </section>

    @push('scripts')
    <script>
        (function () {
            const stage = document.getElementById('reelsStage');
            const cards = Array.from(stage.querySelectorAll('.reel-card'));
            const n = cards.length, DELAY = 5000;
            let active = Math.min(2, n - 1), timer = null, paused = false, hovered = false;
            stage.style.setProperty('--delay', DELAY + 'ms');

            function layout() {
                cards.forEach((c, i) => {
                    let d = (i - active + n) % n;
                    if (d > n / 2) d -= n;
                    c.style.setProperty('--slot', d);
                    c.classList.toggle('is-active', d === 0);
                    c.classList.toggle('is-near', Math.abs(d) === 1);
                    c.classList.toggle('is-far', Math.abs(d) === 2);
                    c.classList.toggle('is-away', Math.abs(d) > 2);
                    document.querySelectorAll("#reelsDots i")[i]?.classList.toggle("is-on", d === 0);
                    const v = c.querySelector('video');
                    if (v) {
                        if (d === 0 && !paused) { const p = v.play(); if (p) p.catch(() => {}); } else { v.pause(); }
                    }
                    // YouTube / Vimeo: only the active card loads (and autoplays) its embedded player
                    const slot = c.querySelector('.reel-embed');
                    if (slot) {
                        const frame = slot.querySelector('iframe');
                        if (d === 0 && !paused) {
                            if (!frame) {
                                const f = document.createElement('iframe');
                                f.src = slot.dataset.src; f.title = c.getAttribute('aria-label');
                                f.allow = 'autoplay; encrypted-media'; f.setAttribute('tabindex', '-1'); f.setAttribute('loading', 'lazy');
                                f.referrerPolicy = 'strict-origin-when-cross-origin';
                                slot.appendChild(f);
                            }
                        } else if (frame) { frame.remove(); }
                    }
                });
                // restart the progress bar animation on the active card
                const bar = cards[active].querySelector('.reel-progress b');
                if (bar) { bar.style.animation = 'none'; bar.offsetWidth; bar.style.animation = ''; }
            }
            function go(i) { active = (i + n) % n; layout(); }
            function restart() {
                clearInterval(timer);
                if (n < 2 || paused) return;
                timer = setInterval(() => { if (!document.hidden) go(active + 1); }, DELAY);
            }
            function setPaused(p) {
                paused = p;
                stage.classList.toggle('is-paused', p);
                cards.forEach(c => c.querySelector('.reel-pause').setAttribute('aria-label', p ? 'Play' : 'Pause'));
                layout(); restart();
            }

            document.getElementById('reelsPrev')?.addEventListener('click', () => { go(active - 1); restart(); });
            document.getElementById('reelsNext')?.addEventListener('click', () => { go(active + 1); restart(); });
            cards.forEach(c => c.querySelector('.reel-pause').addEventListener('click', e => { e.preventDefault(); e.stopPropagation(); setPaused(!paused); }));
            document.addEventListener('visibilitychange', () => { if (document.hidden) clearInterval(timer); else restart(); });
            window.addEventListener('pagehide', () => clearInterval(timer));

            // swipe left / right on touch screens (a tap still opens the link)
            let x0 = null;
            stage.addEventListener('touchstart', e => { x0 = e.touches[0].clientX; }, { passive: true });
            stage.addEventListener('touchend', e => {
                if (x0 === null) return;
                const dx = e.changedTouches[0].clientX - x0; x0 = null;
                if (Math.abs(dx) > 50) { go(active + (dx < 0 ? 1 : -1)); restart(); stage.dataset.swiped = '1'; setTimeout(() => delete stage.dataset.swiped, 400); }
            }, { passive: true });
            stage.addEventListener('click', e => { if (stage.dataset.swiped) e.preventDefault(); }, true);

            layout();
            restart();
        })();
    </script>
    @endpush
@endif
