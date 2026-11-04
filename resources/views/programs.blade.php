@extends('layout.app')

@section('title', 'Programs | Alba Business')

@section('content')
    @if($banner->is_active)
        {{-- Same look as the About / Contact / Insights banners (shared .about-banner styles) --}}
        <section class="about-banner programs-banner" @if($banner->image) style="background-image: url('{{ asset($banner->image) }}')" @endif>
            <span class="pb-orb pb-orb-1" aria-hidden="true"></span>
            <span class="pb-orb pb-orb-2" aria-hidden="true"></span>
            <span class="pb-orb pb-orb-3" aria-hidden="true"></span>
            <div class="container about-banner-inner">
                <nav class="programs-crumb" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}">Home</a><span aria-hidden="true">&gt;</span><b>Programs</b>
                </nav>
                {{-- Words are split so each one can animate in turn; normal spaces stay between them so wrapping is unchanged --}}
                @if($banner->title)
                    <h1 class="pb-title" aria-label="{{ $banner->title }}"><span>@foreach(preg_split('/\s+/', $banner->title) as $w)<span class="pw" aria-hidden="true" style="--w: {{ $loop->index }}"><span>{{ $w }}</span></span>{{ $loop->last ? '' : ' ' }}@endforeach</span></h1>
                    <i class="pb-rule" aria-hidden="true"></i>
                @endif
                @if($banner->description)
                    <p class="pb-desc" aria-label="{{ $banner->description }}">@foreach(preg_split('/\s+/', $banner->description) as $w)<span class="pd-word" aria-hidden="true" style="--w: {{ $loop->index }}">{{ $w }}</span>{{ $loop->last ? '' : ' ' }}@endforeach</p>
                @endif
            </div>
        </section>
    @endif

    @if($points->isNotEmpty())
        <section class="programs-points" aria-label="Program highlights">
            <ul class="container programs-points-list">
                @foreach($points as $point)
                    <li class="pp-reveal" style="--d: {{ $loop->index * 0.12 }}s">
                        <i aria-hidden="true">
                            @if($point->icon)
                                <img src="{{ asset($point->icon) }}" alt="" loading="lazy">
                            @else
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8"/><path d="M8.5 12.5l2.5 2.5 4.5-5"/></svg>
                            @endif
                        </i>
                        <span>
                            <strong>{{ $point->title }}</strong>
                            @if($point->description)<small>{{ $point->description }}</small>@endif
                        </span>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <section class="home-programs programs-listing" id="programs">
        <div class="container">
            @if($programSection->eyebrow || $programSection->heading || $programSection->heading_accent || $programSection->description)
                <div class="programs-head programs-head-left">
                    @if($programSection->eyebrow)
                        <p class="programs-eyebrow"><span></span>{{ $programSection->eyebrow }}</p>
                    @endif
                    <h2>
                        @if($programSection->heading_accent)<em>{{ $programSection->heading_accent }}</em>@endif
                        @if($programSection->heading)<span>{{ $programSection->heading }}</span>@endif
                    </h2>
                    @if($programSection->description)<p class="programs-desc">{{ $programSection->description }}</p>@endif
                </div>
            @endif

            @if($programCards->isEmpty())
                <p class="programs-empty">Programs are coming soon.</p>
            @else
                <div class="programs-list-grid">
                    @foreach($programCards as $i => $card)
                        <article class="program-card in {{ $card->is_featured ? 'is-featured' : '' }}">
                            <p class="program-level"><span>Level {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span><i></i></p>
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
                                <a href="{{ route('programs.show', $card->slug) }}" class="program-btn">Enroll Now &rarr;</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
<script>
(function () {
    var items = document.querySelectorAll('.pp-reveal');
    if (!items.length) return;
    if (!('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        items.forEach(function (el) { el.classList.add('in'); });
        return;
    }
    document.documentElement.classList.add('pp-anim');
    var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
    }, { threshold: 0.2 });
    items.forEach(function (el) { io.observe(el); });
})();
</script>
@endpush
