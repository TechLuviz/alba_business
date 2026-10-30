@if($about->isVisible())
    @php($hasImages = $about->primary_image || $about->secondary_image)
    <section class="home-about {{ $about->image_position === 'right' ? 'is-reversed' : '' }} {{ $hasImages ? '' : 'no-media' }}" id="about">
        <div class="container about-grid">
            @if($hasImages)
                <div class="about-media">
                    @if($about->primary_image)
                        <img class="about-img about-img-1" src="{{ asset($about->primary_image) }}" alt="About Alba Business School" loading="lazy">
                    @endif
                    @if($about->secondary_image)
                        <img class="about-img about-img-2" src="{{ asset($about->secondary_image) }}" alt="Alba Business School campus life" loading="lazy">
                    @endif
                </div>
            @endif

            <div class="about-copy">
                @if($about->eyebrow)<p class="about-eyebrow">{{ $about->eyebrow }}</p>@endif
                @if($about->heading)<h2>{{ $about->heading }}</h2>@endif
                @if($about->description)<p class="about-text">{{ $about->description }}</p>@endif
                {{-- Static button: change the text / link here in the code --}}
                <a href="{{ route('about') }}" class="about-btn">
                    <span>Our Story</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
            </div>
        </div>
    </section>
@endif
