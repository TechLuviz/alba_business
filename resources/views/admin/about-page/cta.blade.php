@extends('admin.layout')

@section('title', 'Get In Touch Section')
@section('page_title', 'About Page - Get In Touch')
@section('breadcrumbs')<a href="{{ route('admin.banners.index') }}">Home</a> <span>&rsaquo;</span> About Page Settings <span>&rsaquo;</span> Get In Touch Section @endsection

@section('content')
    @php($c = config('banner.about_cta'))
    @php($img = $c['image'])
    @php($hint = "Letters, numbers, spaces and . , ' - & ! ? only")
    <form method="POST" action="{{ route('admin.aboutpage.cta.update') }}" enctype="multipart/form-data" class="card form-card" data-live-validate novalidate>
        @csrf
        @method('PUT')

        <h3>Text</h3>
        <label>Section label (eyebrow) *
            <input type="text" name="eyebrow" value="{{ old('eyebrow', $cta->eyebrow) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                   minlength="{{ $c['eyebrow_min'] }}" maxlength="{{ $c['eyebrow_max'] }}" placeholder="{{ $hint }}">
        </label>
        <div class="grid2">
            <label>Heading line 1 *
                <input type="text" name="heading_line_1" value="{{ old('heading_line_1', $cta->heading_line_1) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                       minlength="{{ $c['line_min'] }}" maxlength="{{ $c['line_max'] }}" placeholder="{{ $hint }}">
            </label>
            <label>Heading line 2 *
                <input type="text" name="heading_line_2" value="{{ old('heading_line_2', $cta->heading_line_2) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                       minlength="{{ $c['line_min'] }}" maxlength="{{ $c['line_max'] }}" placeholder="{{ $hint }}">
            </label>
        </div>
        <label>Heading accent (gold italic line) *
            <input type="text" name="heading_accent" value="{{ old('heading_accent', $cta->heading_accent) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                   minlength="{{ $c['accent_min'] }}" maxlength="{{ $c['accent_max'] }}" placeholder="{{ $hint }}">
        </label>
        <label>Description *
            <textarea name="description" rows="3" data-live="line" required data-word-max="{{ $c['long_word_max'] }}"
                      minlength="{{ $c['description_min'] }}" maxlength="{{ $c['description_max'] }}" placeholder="{{ $hint }}">{{ old('description', $cta->description) }}</textarea>
        </label>
        <p class="hint">The "Get in touch" button is static and is changed in the code.</p>

        <h3>Images</h3>
        <div class="spec">
            <strong>Image requirements</strong>
            <ul>
                <li>Recommended: {{ $img['recommended'] }}</li>
                <li>Min {{ $img['min_width'] }} x {{ $img['min_height'] }} px &middot; max {{ $img['max_width'] }} x {{ $img['max_height'] }} px &middot; JPG, PNG or WEBP &middot; max {{ round($img['max_kb'] / 1024) }} MB</li>
                <li>On the website the small image overlaps the large one, and both pop up when hovered.</li>
            </ul>
        </div>
        <div class="grid2">
            @foreach(['image_main' => ['Large image', 'ctaMainPreview'], 'image_small' => ['Small image (overlapping)', 'ctaSmallPreview']] as $field => [$label, $previewId])
                <div class="img-field">
                    <label>{{ $label }} *
                        <input type="file" name="{{ $field }}" data-preview="{{ $previewId }}" accept="image/png,image/jpeg,image/webp" @if(!$cta->{$field}) required @endif
                               data-max-kb="{{ $img['max_kb'] }}" data-min-w="{{ $img['min_width'] }}" data-min-h="{{ $img['min_height'] }}"
                               data-max-w="{{ $img['max_width'] }}" data-max-h="{{ $img['max_height'] }}">
                    </label>
                    <div class="preview">
                        <img id="{{ $previewId }}" src="{{ $cta->{$field} ? asset($cta->{$field}) : '' }}" alt="" style="max-height:130px; border-radius:8px; @if(!$cta->{$field}) display:none @endif">
                    </div>
                </div>
            @endforeach
        </div>
        <label>Small image caption *
            <input type="text" name="image_caption" value="{{ old('image_caption', $cta->image_caption) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                   minlength="{{ $c['caption_min'] }}" maxlength="{{ $c['caption_max'] }}" placeholder="{{ $hint }}">
        </label>

        <h3>Visibility</h3>
        <div class="check end">
            <span>Show this section on the About page</span>
            <label class="switch">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $cta->is_active))>
                <i></i>
            </label>
        </div>

        <button type="submit" class="btn">Save changes</button>
    </form>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('input[type=file][data-preview]').forEach(input => {
        const img = document.getElementById(input.dataset.preview);
        input.addEventListener('rejected', () => { img.style.display = 'none'; });
        input.addEventListener('change', () => setTimeout(() => {
            const f = input.files[0];
            if (!f) return;
            img.src = URL.createObjectURL(f); img.style.display = 'block';
        }, 0));
    });
</script>
@endpush
