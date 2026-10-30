@if(isset($insightBanner) && $insightBanner->is_active)
    {{-- Same look as the About and Contact page banners (shared .about-banner styles) --}}
    <section class="about-banner insights-banner" @if($insightBanner->image) style="background-image: url('{{ asset($insightBanner->image) }}')" @endif>
        <div class="container about-banner-inner">
            <h1>
                @if($insightBanner->heading_line_1)<span>{{ $insightBanner->heading_line_1 }}</span>@endif
                @if($insightBanner->heading_line_2)<span>{{ $insightBanner->heading_line_2 }}</span>@endif
            </h1>
            @if($insightBanner->description)<p>{{ $insightBanner->description }}</p>@endif
        </div>
    </section>
@endif
