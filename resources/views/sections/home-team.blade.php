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
            // desktop: the second card starts enlarged; phones: the first card starts in colour
            const mobile = window.matchMedia('(max-width: 768px)').matches;
            open(mobile ? 0 : Math.min(1, cards.length - 1));
        })();
    </script>
    @endpush
@endif
