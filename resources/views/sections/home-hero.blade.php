@php($useVideo = isset($video) && $video)

<section class="home-hero" id="heroCarousel" data-interval="{{ ($hero->autoplay_interval ?: 6) * 1000 }}">
    @if($useVideo)
        <video class="hero-video" src="{{ asset($video->video) }}" autoplay muted loop playsinline></video>
        <div class="hero-slide active hero-overlay">
            @include('sections.hero-content', ['item' => $video])
        </div>
    @elseif($slides->isNotEmpty())
        @foreach($slides as $i => $slide)
            <div class="hero-slide hero-overlay {{ $i === 0 ? 'active' : '' }}" style="background-image: url('{{ asset($slide->image) }}')">
                @include('sections.hero-content', ['item' => $slide])
            </div>
        @endforeach

        @if($slides->count() > 1)
            <div class="hero-dots">
                @foreach($slides as $i => $slide)
                    <button type="button" class="{{ $i === 0 ? 'active' : '' }}" aria-label="Slide {{ $i + 1 }}"></button>
                @endforeach
            </div>
        @endif
    @else
        <div class="hero-slide active hero-overlay">
            @include('sections.hero-content', ['item' => $hero])
        </div>
    @endif
</section>

@if(! $useVideo && $slides->count() > 1)
    @push('scripts')
    <script>
        (function () {
            const root = document.getElementById('heroCarousel');
            const slides = root.querySelectorAll('.hero-slide');
            const dots = root.querySelectorAll('.hero-dots button');
            const delay = parseInt(root.dataset.interval, 10) || 6000;
            let current = 0, timer;

            function show(i) {
                slides[current].classList.remove('active');
                dots[current].classList.remove('active');
                current = (i + slides.length) % slides.length;
                slides[current].classList.add('active');
                dots[current].classList.add('active');
            }
            function start() { timer = setInterval(() => show(current + 1), delay); }

            dots.forEach((d, i) => d.addEventListener('click', () => { clearInterval(timer); show(i); start(); }));
            start();
        })();
    </script>
    @endpush
@endif