@if($problem->isVisible())
    <section class="home-problem" id="problem">
        <div class="container problem-grid">
            <div class="problem-copy">
                @if($problem->eyebrow)
                    <p class="problem-eyebrow"><span class="problem-eyebrow-line"></span><span>{{ $problem->eyebrow }}</span></p>
                @endif
                <h2>
                    @if($problem->heading)<span>{{ $problem->heading }}</span>@endif
                    @if($problem->heading_accent)<em>{{ $problem->heading_accent }}</em>@endif
                </h2>
                @if($problem->description)<p class="problem-text">{{ $problem->description }}</p>@endif
                {{-- Static button: change the text / link here in the code --}}
                <a href="#programs" class="btn-gold">
                    <span>Explore Programs</span>
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 11L11 3M5 3h6v6"/></svg>
                </a>
            </div>

            <div class="problem-cards">
                {{-- two columns: the first card of each column is short or tall in turn --}}
                @foreach([[1, 3], [2, 4]] as $col => $pair)
                    <div class="problem-col">
                        @foreach($pair as $n)
                            @php($img = $problem->{"card{$n}_image"})
                            @php($title = $problem->{"card{$n}_title"})
                            @if($img || $title)
                                <figure class="problem-card">
                                    @if($img)<img src="{{ asset($img) }}" alt="{{ $title ?: 'Business challenge' }}" loading="lazy">@endif
                                    @if($title)<figcaption>{{ $title }}</figcaption>@endif
                                </figure>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
