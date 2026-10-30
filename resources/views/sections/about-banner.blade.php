@if(isset($aboutBanner) && $aboutBanner->is_active)
    <section class="about-banner" @if($aboutBanner->image) style="background-image: url('{{ asset($aboutBanner->image) }}')" @endif>
        <div class="container about-banner-inner">
            <h1>
                @if($aboutBanner->heading_line_1)<span>{{ $aboutBanner->heading_line_1 }}</span>@endif
                @if($aboutBanner->heading_line_2)<span>{{ $aboutBanner->heading_line_2 }}</span>@endif
            </h1>
            @if($aboutBanner->description)<p>{{ $aboutBanner->description }}</p>@endif
        </div>
    </section>
@endif
