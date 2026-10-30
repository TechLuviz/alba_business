@if(isset($principleSection) && $principleSection->is_active && $principles->isNotEmpty())
    <section class="about-principles" id="principles">
        <div class="principles-media">
            @if($principleSection->image)
                <img src="{{ asset($principleSection->image) }}" alt="Our guiding principles" loading="lazy">
            @endif
        </div>

        <div class="principles-body">
            @if($principleSection->eyebrow)
                <p class="principles-eyebrow"><span></span>{{ $principleSection->eyebrow }}</p>
            @endif

            @foreach($principles as $i => $item)
                <article class="principle" style="--i: {{ $i }}">
                    <div class="principle-icon">@include('sections._principle-icon', ['icon' => ['target', 'eye'][$i] ?? 'target']){{-- static icons: target for the first, eye for the second --}}</div>
                    <div class="principle-text">
                        <p class="principle-label">{{ $item->label }}</p>
                        <h3><span>{{ $item->heading }}</span><em>{{ $item->heading_accent }}</em></h3>
                        <p class="principle-desc">{{ $item->description }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    @push('scripts')
    <script>
        (function () {
            const items = document.querySelectorAll('.principle');
            if (!('IntersectionObserver' in window)) { items.forEach(i => i.classList.add('in')); return; }
            const io = new IntersectionObserver(es => es.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } }), { threshold: .2 });
            items.forEach(i => io.observe(i));
        })();
    </script>
    @endpush
@endif
