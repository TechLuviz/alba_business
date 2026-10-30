@if(isset($featuredSection) && $featuredSection->is_active && $featuredCards->isNotEmpty())
    <section class="featured-insights" id="featured-insights">
        @if($featuredSection->eyebrow)
            <p class="featured-eyebrow"><span></span>{{ $featuredSection->eyebrow }}<span></span></p>
        @endif

        <div class="featured-viewport" aria-label="{{ $featuredSection->eyebrow }}">
            <div class="featured-track" id="featuredTrack">
                @foreach($featuredCards as $card)
                    <article class="featured-card">
                        @if($card->image)<img src="{{ asset($card->image) }}" alt="{{ $card->title }}" loading="lazy">@endif
                        <div class="featured-info">
                            <p class="featured-meta">
                                <strong>{{ $card->category }}</strong><i></i>
                                <time datetime="{{ $card->published_on->toDateString() }}">{{ $card->published_on->format('d M Y') }}</time>
                            </p>
                            <h3>{{ $card->title }}</h3>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        (function () {
            const track = document.getElementById('featuredTrack');
            const viewport = track.parentElement;
            const originals = Array.from(track.children);
            const SPEED = 55; // pixels per second - the row never pauses

            function build() {
                // reset to one set of cards
                Array.from(track.children).forEach(c => { if (c.dataset.clone) c.remove(); });
                const gap = parseFloat(getComputedStyle(track).columnGap) || 0;
                const setWidth = originals.reduce((w, c) => w + c.getBoundingClientRect().width + gap, 0);
                if (!setWidth) return;
                // repeat the set until the row is at least a screen wider than the visible area, then one more set to loop into
                const copies = Math.max(1, Math.ceil(viewport.clientWidth / setWidth)) + 1;
                for (let n = 0; n < copies; n++) {
                    originals.forEach(c => {
                        const k = c.cloneNode(true);
                        k.dataset.clone = '1';
                        k.setAttribute('aria-hidden', 'true');
                        track.appendChild(k);
                    });
                }
                track.style.setProperty('--shift', setWidth + 'px');
                track.style.setProperty('--dur', (setWidth / SPEED) + 's');
            }

            build();
            let t = null;
            window.addEventListener('resize', () => { clearTimeout(t); t = setTimeout(build, 250); });
            window.addEventListener('load', build);
        })();
    </script>
    @endpush
@endif
