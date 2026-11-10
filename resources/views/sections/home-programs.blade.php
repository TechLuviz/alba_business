@if(isset($programSection) && $programSection->is_active && $programCards->isNotEmpty())
    <section class="home-programs" id="programs">
        <div class="container">
            <div class="programs-head">
                @if($programSection->eyebrow)
                    <p class="programs-eyebrow"><span></span>{{ $programSection->eyebrow }}<span></span></p>
                @endif
                <h2>
                    @if($programSection->heading_accent)<em>{{ $programSection->heading_accent }}</em>@endif
                    @if($programSection->heading)<span>{{ $programSection->heading }}</span>@endif
                </h2>
                @if($programSection->description)<p class="programs-desc">{{ $programSection->description }}</p>@endif
            </div>

            {{-- always a single-row auto-sliding carousel, whatever the number of cards --}}
            @php($carousel = true)
            <div class="programs-wrap {{ $carousel ? 'is-carousel' : '' }}" id="programsWrap">
            <div class="programs-viewport">
            <div class="programs-grid" data-count="{{ $programCards->count() }}">
                @foreach($programCards as $i => $card)
                    <article class="program-card {{ $card->is_featured ? 'is-featured' : '' }}" style="--i: {{ $i }}">
                        <p class="program-level"><span>Programme {{ $i + 1 }}</span><i></i></p>
                        <div class="program-media">
                            @if($card->image)
                                <img src="{{ asset($card->image) }}" alt="{{ $card->title }}" loading="lazy">
                            @endif
                        </div>
                        <h3><a href="{{ route('programs.show', $card->slug) }}" class="program-link">{{ $card->title }}</a></h3>
                        <ul class="program-features">
                            @foreach($card->features() as $feature)
                                <li>{{ $feature }}</li>
                            @endforeach
                        </ul>
                        <div class="program-foot">
                            <div class="program-meta"><strong>{{ is_numeric($card->duration) ? $card->duration . ' ' . Str::plural('Month', (int) $card->duration) : $card->duration }}</strong><span>{{ $card->mode }}</span></div>
                            {{-- Static button: change the text / link here in the code --}}
                            <button type="button" class="program-btn" data-enroll="{{ $card->title }}">Enroll Now &rarr;</button>
                        </div>
                    </article>
                @endforeach
            </div>
            </div>
            @if($carousel)
                <div class="programs-nav">
                    <button type="button" class="programs-nav-btn" id="programsPrev" aria-label="Previous programs">&larr;</button>
                    <button type="button" class="programs-nav-btn is-next" id="programsNext" aria-label="Next programs">&rarr;</button>
                </div>
            @endif
            </div>
        </div>
    </section>

    @include('sections.enroll-modal')

    @push('scripts')
    <script>
        (function () {
            const cards = document.querySelectorAll('.program-card');

            // slide as a carousel (4 / 3 / 2 / 1 cards visible depending on screen width)
            const wrap = document.getElementById('programsWrap');
            // phones (<600px) stack the cards one under another instead of sliding
            if (wrap && wrap.classList.contains('is-carousel') && window.matchMedia('(min-width: 600px)').matches) {
                const track = wrap.querySelector('.programs-grid');
                const prev = document.getElementById('programsPrev'), next = document.getElementById('programsNext');
                const n = cards.length;
                let index = 0, timer = null, hovered = false, busy = false;

                // endless loop: append copies of the first cards so the last slide runs straight into the first
                // (4 copies, cycling through the cards when there are fewer than 4, so the row never shows a gap)
                Array.from({ length: 4 }, (_, k) => cards[k % n]).forEach(c => {
                    const copy = c.cloneNode(true);
                    copy.setAttribute('aria-hidden', 'true');
                    copy.classList.add('in');
                    track.appendChild(copy);
                });

                function setPos(i, animate) {
                    index = i;
                    track.style.transition = animate ? '' : 'none';
                    const step = cards[0].offsetWidth + parseFloat(getComputedStyle(track).columnGap || 0);
                    track.style.transform = 'translateX(' + (-index * step) + 'px)';
                    if (!animate) track.getBoundingClientRect(); // flush so the next change animates
                }
                function slide(dir) {
                    if (busy) return;
                    busy = true;
                    if (dir < 0 && index === 0) setPos(n, false);
                    setPos(index + dir, true);
                    setTimeout(() => { busy = false; }, 750);
                }
                track.addEventListener('transitionend', e => {
                    if (e.target === track && e.propertyName === 'transform' && index >= n) setPos(0, false);
                });
                function restart() {
                    clearInterval(timer);
                    timer = setInterval(() => { if (!hovered && !document.hidden) slide(1); }, 3500);
                }
                cards.forEach(c => c.classList.add('in')); // clipped cards never intersect the viewport, so reveal them now
                prev.addEventListener('click', () => { slide(-1); restart(); });
                next.addEventListener('click', () => { slide(1); restart(); });
                wrap.addEventListener('mouseenter', () => { hovered = true; });
                wrap.addEventListener('mouseleave', () => { hovered = false; });
                window.addEventListener('resize', () => setPos(index >= n ? 0 : index, false));
                window.addEventListener('pagehide', () => clearInterval(timer));
                // swipe on touch screens
                let x0 = null;
                track.addEventListener('touchstart', e => { x0 = e.touches[0].clientX; }, { passive: true });
                track.addEventListener('touchend', e => {
                    if (x0 === null) return;
                    const dx = e.changedTouches[0].clientX - x0; x0 = null;
                    if (Math.abs(dx) > 40) { slide(dx < 0 ? 1 : -1); restart(); }
                }, { passive: true });
                restart();
            }

            if (!('IntersectionObserver' in window)) { cards.forEach(c => c.classList.add('in')); return; }
            const io = new IntersectionObserver(entries => entries.forEach(e => {
                if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
            }), { threshold: .15 });
            cards.forEach(c => io.observe(c));

            // 3D hover: neighbours of the hovered card move outward (left cards left, right cards right)
            const grid = document.querySelector('.programs-grid');
            if (grid && window.matchMedia('(hover: hover) and (min-width: 901px)').matches) {
                const all = Array.from(grid.querySelectorAll('.program-card'));
                const reset = () => {
                    grid.classList.remove('is-hovering');
                    all.forEach(c => { c.classList.remove('is-hovered', 'is-pushed'); c.style.removeProperty('--dx'); c.style.removeProperty('--ry'); c.style.removeProperty('--sc'); });
                };
                all.forEach((card, k) => {
                    card.addEventListener('mouseenter', () => {
                        if (!card.classList.contains('in')) return;
                        grid.classList.add('is-hovering');
                        all.forEach((c, j) => {
                            c.classList.toggle('is-hovered', j === k);
                            c.classList.toggle('is-pushed', j !== k);
                            if (j === k) { c.style.setProperty('--dx', '0px'); c.style.setProperty('--ry', '0deg'); return; }
                            const dir = Math.sign(j - k), dist = Math.abs(j - k);
                            c.style.setProperty('--dx', (dir * (46 + 18 * (dist - 1))) + 'px');
                            c.style.setProperty('--ry', (-dir * 16) + 'deg');
                            c.style.setProperty('--sc', '.94');
                        });
                    });
                });
                grid.addEventListener('mouseleave', reset);
            }
        })();
    </script>
    @endpush
@endif
