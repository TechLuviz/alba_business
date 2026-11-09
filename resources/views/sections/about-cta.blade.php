@if(isset($aboutCta) && $aboutCta->is_active)
    <section class="about-cta" id="get-in-touch">
        <div class="container">
            <div class="about-cta-grid">
                <div class="about-cta-copy">
                    @if($aboutCta->eyebrow)
                        <p class="about-cta-eyebrow"><span></span>{{ $aboutCta->eyebrow }}</p>
                    @endif
                    <h2>
                        <span>{{ $aboutCta->heading_line_1 }}</span>
                        <span>{{ $aboutCta->heading_line_2 }}</span>
                        <em>{{ $aboutCta->heading_accent }}</em>
                    </h2>
                    @if($aboutCta->description)<p class="about-cta-desc">{{ $aboutCta->description }}</p>@endif
                    {{-- Static button: change the text / link here in the code --}}
                    <a href="{{ route('contact') }}" class="about-cta-btn"><span>Get in touch</span><i aria-hidden="true">&rarr;</i></a>
                </div>

                @if($aboutCta->image_main || $aboutCta->image_small)
                    <div class="about-cta-media">
                        @if($aboutCta->image_main)
                            <figure class="cta-photo is-main"><img src="{{ asset($aboutCta->image_main) }}" alt="Albatech campus" loading="lazy"></figure>
                        @endif
                        @if($aboutCta->image_small)
                            <figure class="cta-photo is-small">
                                <img src="{{ asset($aboutCta->image_small) }}" alt="{{ $aboutCta->image_caption ?: 'Albatech community' }}" loading="lazy">
                                @if($aboutCta->image_caption)<figcaption>{{ $aboutCta->image_caption }}</figcaption>@endif
                            </figure>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Static footer strip --}}
            <div class="about-cta-foot"><span>ALBAHUB &mdash; School of Business</span><i></i></div>
        </div>
    </section>
@endif
