@if(isset($insightSection) && $insightSection->is_active && isset($insightArticles) && $insightArticles->isNotEmpty())
    <section class="home-insights" id="insights">
        <div class="container insights-grid">
            <div class="insights-copy">
                @if($insightSection->eyebrow)
                    <p class="insights-eyebrow"><span></span>{{ $insightSection->eyebrow }}</p>
                @endif
                <h2>
                    <span>{{ $insightSection->heading }}</span>
                    <em>{{ $insightSection->heading_accent }}</em>
                </h2>
                @if($insightSection->description)<p class="insights-desc">{{ $insightSection->description }}</p>@endif
                {{-- Static button: change the text / link here in the code --}}
                <a href="{{ route('insights') }}" class="insights-btn">Explore All Blogs <span aria-hidden="true">&rarr;</span></a>
            </div>

            <div class="insights-stack{{ isset($insightArticles) && $insightArticles->isNotEmpty() ? ' has-mobile-articles' : '' }}" id="insightsStack" aria-label="Blogs">
                @foreach($insightArticles as $i => $card)
                    <article class="insight-card" tabindex="0" role="button" aria-label="{{ $card->title }}">
                        <div class="insight-media">
                            @if($card->image)<img src="{{ asset($card->image) }}" alt="{{ $card->title }}" loading="lazy">@endif
                        </div>
                        <div class="insight-body">
                            <p class="insight-num"><span>{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span><i></i></p>
                            <h3>{{ $card->title }}</h3>
                            <p class="insight-text">{{ $card->description }}</p>
                        </div>
                        <a class="insight-arrow" href="{{ route('insights.show', $card) }}" aria-label="Read article: {{ $card->title }}">&rarr;</a>
                    </article>
                @endforeach
            </div>

            {{-- Phones: the articles created on the Insights page, each linking to its detail page --}}
            @if(isset($insightArticles) && $insightArticles->isNotEmpty())
                <div class="insights-articles" aria-label="Latest insights">
                    @foreach($insightArticles as $i => $article)
                        <a class="insight-link-card" href="{{ route('insights.show', $article) }}" aria-label="Read article: {{ $article->title }}">
                            <div class="insight-media">
                                @if($article->image)<img src="{{ asset($article->image) }}" alt="{{ $article->title }}" loading="lazy">@endif
                            </div>
                            <div class="insight-body">
                                <p class="insight-num"><span>{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span><i></i></p>
                                <h3>{{ $article->title }}</h3>
                                <p class="insight-text">{{ $article->description }}</p>
                            </div>
                            <span class="insight-go" aria-hidden="true">&rarr;</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @push('scripts')
    <script>
        (function () {
            const stack = document.getElementById('insightsStack');
            const cards = Array.from(stack.querySelectorAll('.insight-card'));
            const n = cards.length;
            let active = 0, timer = null, hovered = false;

            // Slots relative to the active card: -1 above, 0 centre, +1 below; all others wait hidden.
            function layout() {
                cards.forEach((c, i) => {
                    let d = (i - active + n) % n;
                    if (d > n / 2) d -= n;               // shortest circular distance
                    c.classList.remove('is-active', 'is-prev', 'is-next', 'is-away');
                    if (n === 1 || d === 0) c.classList.add('is-active');
                    else if (d === -1) c.classList.add('is-prev');
                    else if (d === 1) c.classList.add('is-next');
                    else c.classList.add('is-away');
                    c.style.setProperty('--slot', d);
                    c.setAttribute('aria-current', d === 0 ? 'true' : 'false');
                });
            }
            function go(i) { active = (i + n) % n; layout(); }
            function restart() {
                clearInterval(timer);
                if (n < 2) return;
                timer = setInterval(() => { if (!hovered && !document.hidden) go(active + 1); }, 4000);
            }

            cards.forEach((c, i) => {
                c.addEventListener('click', () => { if (i !== active) { go(i); restart(); } });
                c.querySelector('.insight-arrow').addEventListener('click', e => e.stopPropagation()); // the arrow just follows its link
                c.addEventListener('keydown', e => {
                    if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); go(i); restart(); }
                    else if (e.key === 'ArrowDown') { e.preventDefault(); go(active + 1); restart(); }
                    else if (e.key === 'ArrowUp') { e.preventDefault(); go(active - 1); restart(); }
                });
            });
            stack.addEventListener('mouseenter', () => { hovered = true; });
            stack.addEventListener('mouseleave', () => { hovered = false; });
            window.addEventListener('pagehide', () => clearInterval(timer));

            // swipe up / down on touch screens
            let y0 = null;
            stack.addEventListener('touchstart', e => { y0 = e.touches[0].clientY; }, { passive: true });
            stack.addEventListener('touchend', e => {
                if (y0 === null) return;
                const dy = e.changedTouches[0].clientY - y0; y0 = null;
                if (Math.abs(dy) > 40) { go(active + (dy < 0 ? 1 : -1)); restart(); }
            }, { passive: true });

            layout();
            restart();
        })();
    </script>
    @endpush
@endif
