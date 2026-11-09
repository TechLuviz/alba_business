@if(isset($audience) && isset($audienceCards) && $audience->isVisible($audienceCards) && $audienceCards->isNotEmpty())
    <section class="home-audience" id="target-audience"
             style="background-color: {{ $audience->bg_color ?: '#FBF9F5' }}; padding-top: clamp(48px, 6vw, {{ $audience->padding_top ?? 90 }}px); padding-bottom: clamp(48px, 6vw, {{ $audience->padding_bottom ?? 90 }}px);">
        <div class="container">
            <div class="audience-grid">

                {{-- Left Column: Intro & Heading --}}
                <div class="audience-intro" style="text-align: {{ $audience->heading_align ?? 'left' }};">
                    @if($audience->eyebrow)
                        <p class="audience-eyebrow" style="color: {{ $audience->accent_line_color ?: '#C8914A' }}; @if(($audience->heading_align ?? 'left') === 'center') justify-content: center; @elseif(($audience->heading_align ?? 'left') === 'right') justify-content: flex-end; @endif">
                            <span class="audience-eyebrow-line" style="background-color: {{ $audience->accent_line_color ?: '#C8914A' }};"></span>
                            <span>{{ $audience->eyebrow }}</span>
                        </p>
                    @endif

                    <h2 class="audience-heading" style="color: {{ $audience->heading_color ?: '#0A1628' }};">
                        {!! $audience->formattedHeadingHtml() !!}
                    </h2>

                    @if($audience->description)
                        <p class="audience-desc" style="color: {{ $audience->description_color ?: '#525866' }}; @if(($audience->heading_align ?? 'left') === 'center') margin-left: auto; margin-right: auto; @elseif(($audience->heading_align ?? 'left') === 'right') margin-left: auto; @endif">
                            {{ $audience->description }}
                        </p>
                    @endif
                </div>

                {{-- Right Column: Circular Rotating Carousel (No Cropped Background Cards) --}}
                <div class="audience-carousel-col"
                     id="audienceCarousel"
                     data-autoplay="{{ $audience->autoplay ? '1' : '0' }}"
                     data-interval="{{ max(2, min(30, (int)($audience->autoplay_interval ?? 5))) * 1000 }}">

                    <div class="audience-stage-wrap" id="audienceViewport" tabindex="0" aria-label="Target Audience Carousel" role="region">
                        <div class="audience-stage" id="audienceStage">
                            @foreach($audienceCards as $index => $card)
                                @php($formattedNum = str_pad($index + 1, 2, '0', STR_PAD_LEFT))
                                <article class="audience-card"
                                         data-index="{{ $index }}"
                                         tabindex="0"
                                         aria-label="{{ $card->title }}">
                                    <div class="audience-card-header">
                                        <span class="audience-card-num" style="color: {{ $audience->highlight_color ?: '#C8914A' }};">{{ $formattedNum }} &mdash;</span>
                                        <h3 class="audience-card-title">{{ $card->title }}</h3>
                                        @if($card->description)
                                            <p class="audience-card-desc">{{ $card->description }}</p>
                                        @endif
                                    </div>

                                    <div class="audience-card-media">
                                        @if($card->image)
                                            <img class="audience-card-img"
                                                 src="{{ asset($card->image) }}"
                                                 alt="{{ $card->image_alt ?: $card->title }}"
                                                 loading="lazy"
                                                 onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'600\' height=\'450\' viewBox=\'0 0 600 450\' fill=\'%23eae7e1\'><rect width=\'600\' height=\'450\'/><text x=\'50%25\' y=\'50%25\' font-size=\'22\' fill=\'%238a6d4b\' text-anchor=\'middle\' dominant-baseline=\'middle\' font-family=\'sans-serif\'>{{ addslashes($card->title) }}</text></svg>'">
                                        @else
                                            <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #eae7e1; color: #8a6d4b; font-weight: 500;">
                                                {{ $card->title }}
                                            </div>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>

                    {{-- Navigation Arrow Buttons --}}
                    @if($audienceCards->count() > 1)
                        <div class="audience-nav" aria-label="Carousel navigation">
                            <button type="button" class="audience-nav-btn btn-prev" id="audiencePrev" aria-label="Previous audience card"
                                    style="--nav-color: {{ $audience->highlight_color ?: '#C8914A' }};">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                                </svg>
                            </button>

                            <button type="button" class="audience-nav-btn btn-next" id="audienceNext" aria-label="Next audience card"
                                    style="--nav-color: {{ $audience->highlight_color ?: '#C8914A' }};">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                    @endif

                </div>

            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        (function () {
            const root = document.getElementById('audienceCarousel');
            if (!root) return;

            const stage = document.getElementById('audienceStage');
            const wrap = document.getElementById('audienceViewport');
            const cards = Array.from(stage.querySelectorAll('.audience-card'));
            const prevBtn = document.getElementById('audiencePrev');
            const nextBtn = document.getElementById('audienceNext');

            const total = cards.length;
            if (total === 0) return;

            const autoplayEnabled = root.dataset.autoplay === '1';
            const autoplayInterval = parseInt(root.dataset.interval, 10) || 5000;

            // Start with middle/second card active if 2+ cards exist, else first
            // (on mobile a single card is shown, so start from the first one)
            const isMobile = window.matchMedia('(max-width: 768px)').matches;
            let currentIndex = total > 1 && !isMobile ? 1 : 0;
            let timer = null;
            let isHovered = false;

            function applySlots() {
                cards.forEach(c => {
                    c.classList.remove('is-center', 'is-left', 'is-right', 'is-hidden');
                });

                if (total === 1) {
                    cards[0].classList.add('is-center');
                    return;
                }

                if (total === 2) {
                    cards[currentIndex].classList.add('is-center');
                    const other = (currentIndex + 1) % 2;
                    cards[other].classList.add('is-right');
                    return;
                }

                // For 3 or more cards: center, left, right, and hidden for the rest
                const centerIdx = currentIndex;
                const leftIdx = (currentIndex - 1 + total) % total;
                const rightIdx = (currentIndex + 1) % total;

                cards[centerIdx].classList.add('is-center');
                cards[leftIdx].classList.add('is-left');
                cards[rightIdx].classList.add('is-right');

                cards.forEach((card, i) => {
                    if (i !== centerIdx && i !== leftIdx && i !== rightIdx) {
                        card.classList.add('is-hidden');
                    }
                });
            }

            function next() {
                currentIndex = (currentIndex + 1) % total;
                applySlots();
            }

            function prev() {
                currentIndex = (currentIndex - 1 + total) % total;
                applySlots();
            }

            if (nextBtn) nextBtn.addEventListener('click', () => { next(); restartAutoplay(); });
            if (prevBtn) prevBtn.addEventListener('click', () => { prev(); restartAutoplay(); });

            // Keyboard navigation
            wrap.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    next();
                    restartAutoplay();
                } else if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    prev();
                    restartAutoplay();
                }
            });

            // Clicking any card brings it into the center
            cards.forEach((card, idx) => {
                card.addEventListener('click', () => {
                    if (idx !== currentIndex) {
                        currentIndex = idx;
                        applySlots();
                        restartAutoplay();
                    }
                });
            });

            // Touch / Pointer Drag
            let isPointerDown = false;
            let startX = 0;

            wrap.addEventListener('pointerdown', (e) => {
                isPointerDown = true;
                startX = e.clientX;
                wrap.classList.add('is-dragging');
                stopAutoplay();
            });

            window.addEventListener('pointerup', (e) => {
                if (!isPointerDown) return;
                isPointerDown = false;
                wrap.classList.remove('is-dragging');
                const diff = e.clientX - startX;
                if (diff < -40) {
                    next();
                } else if (diff > 40) {
                    prev();
                }
                restartAutoplay();
            });

            // On touch, scrolling the page fires pointercancel (not pointerup): resume autoplay then too.
            window.addEventListener('pointercancel', () => {
                if (!isPointerDown) return;
                isPointerDown = false;
                wrap.classList.remove('is-dragging');
                restartAutoplay();
            });

            // Autoplay management
            function startAutoplay() {
                if (!autoplayEnabled || total <= 1) return;
                stopAutoplay();
                timer = setInterval(() => {
                    if (!isHovered && !isPointerDown) {
                        next();
                    }
                }, autoplayInterval);
            }

            function stopAutoplay() {
                if (timer) {
                    clearInterval(timer);
                    timer = null;
                }
            }

            function restartAutoplay() {
                stopAutoplay();
                startAutoplay();
            }

            window.addEventListener('pagehide', stopAutoplay);
            document.addEventListener('visibilitychange', () => { document.hidden ? stopAutoplay() : restartAutoplay(); });

            // Pause on hover for real mice only. Touch screens fake mouseenter on tap and never send mouseleave,
            // which would leave autoplay paused forever on mobile.
            root.addEventListener('pointerenter', (e) => { if (e.pointerType === 'mouse') isHovered = true; });
            root.addEventListener('pointerleave', () => { isHovered = false; });

            // Initialize slot positions
            applySlots();
            startAutoplay();
        })();
    </script>
    @endpush
@endif
