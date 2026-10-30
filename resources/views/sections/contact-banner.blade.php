@if(isset($contactBanner) && $contactBanner->is_active)
    {{-- Same look as the About page banner (shared .about-banner styles) --}}
    <section class="about-banner contact-banner" @if($contactBanner->image) style="background-image: url('{{ asset($contactBanner->image) }}')" @endif>
        <div class="container about-banner-inner">
            <h1>
                @if($contactBanner->heading_line_1)<span>{{ $contactBanner->heading_line_1 }}</span>@endif
                @if($contactBanner->heading_line_2)<span>{{ $contactBanner->heading_line_2 }}</span>@endif
            </h1>
            @if($contactBanner->description)<p>{{ $contactBanner->description }}</p>@endif
        </div>
    </section>
@endif
