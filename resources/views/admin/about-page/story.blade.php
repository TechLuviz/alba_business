@extends('admin.layout')

@section('title', 'Our Story Section')
@section('page_title', 'About Page - Our Story')
@section('breadcrumbs')<a href="{{ route('admin.banners.index') }}">Home</a> <span>&rsaquo;</span> About Page Settings <span>&rsaquo;</span> Our Story Section @endsection

@section('content')
    @php($c = config('banner.about_story'))
    @php($img = $c['image'])
    @php($hint = "Letters, numbers, spaces and . , ' - & ! ? only")
    <form method="POST" action="{{ route('admin.aboutpage.story.update') }}" enctype="multipart/form-data" class="card form-card" data-live-validate novalidate>
        @csrf
        @method('PUT')

        <h3>Heading</h3>
        <label>Section label (eyebrow) *
            <input type="text" name="eyebrow" value="{{ old('eyebrow', $story->eyebrow) }}" data-live="line" data-no-digits required data-word-max="{{ $c['word_max'] }}"
                   minlength="{{ $c['eyebrow_min'] }}" maxlength="{{ $c['eyebrow_max'] }}" placeholder="{{ $hint }}">
        </label>
        <label>Heading (dark part) *
            <input type="text" name="heading" value="{{ old('heading', $story->heading) }}" data-live="line" data-no-digits required data-word-max="{{ $c['word_max'] }}"
                   minlength="{{ $c['heading_min'] }}" maxlength="{{ $c['heading_max'] }}" placeholder="{{ $hint }}">
        </label>
        <label>Heading accent (gold italic part) *
            <input type="text" name="heading_accent" value="{{ old('heading_accent', $story->heading_accent) }}" data-live="line" data-no-digits required data-word-max="{{ $c['word_max'] }}"
                   minlength="{{ $c['accent_min'] }}" maxlength="{{ $c['accent_max'] }}" placeholder="{{ $hint }}">
        </label>

        <h3>Story text</h3>
        <label>First paragraph *
            <textarea name="paragraph_1" rows="5" data-live="line" data-no-digits required data-word-max="{{ $c['long_word_max'] }}"
                      minlength="{{ $c['para1_min'] }}" maxlength="{{ $c['para1_max'] }}" placeholder="{{ $hint }}">{{ old('paragraph_1', $story->paragraph_1) }}</textarea>
        </label>
        <label>Second paragraph *
            <textarea name="paragraph_2" rows="4" data-live="line" data-no-digits required data-word-max="{{ $c['long_word_max'] }}"
                      minlength="{{ $c['para2_min'] }}" maxlength="{{ $c['para2_max'] }}" placeholder="{{ $hint }}">{{ old('paragraph_2', $story->paragraph_2) }}</textarea>
        </label>
        <label>Quote *
            <textarea name="quote" rows="3" data-live="line" data-no-digits required data-word-max="{{ $c['long_word_max'] }}"
                      minlength="{{ $c['quote_min'] }}" maxlength="{{ $c['quote_max'] }}" placeholder="{{ $hint }}">{{ old('quote', $story->quote) }}</textarea>
        </label>

        <h3>Person card</h3>
        <div class="spec">
            <strong>Photo requirements</strong>
            <ul>
                <li>Recommended: {{ $img['recommended'] }}</li>
                <li>Min {{ $img['min_width'] }} x {{ $img['min_height'] }} px &middot; max {{ $img['max_width'] }} x {{ $img['max_height'] }} px</li>
                <li>JPG, PNG or WEBP &middot; max {{ round($img['max_kb'] / 1024) }} MB &middot; the name and role are shown over the bottom of the photo.</li>
            </ul>
        </div>
        <label>Photo *
            <input type="file" name="image" id="storyImage" accept="image/png,image/jpeg,image/webp" @if(!$story->image) required @endif
                   data-max-kb="{{ $img['max_kb'] }}" data-min-w="{{ $img['min_width'] }}" data-min-h="{{ $img['min_height'] }}"
                   data-max-w="{{ $img['max_width'] }}" data-max-h="{{ $img['max_height'] }}">
        </label>
        <div class="preview">
            <img id="storyPreview" src="{{ $story->image ? asset($story->image) : '' }}" alt="" style="max-height:160px; border-radius:8px; @if(!$story->image) display:none @endif">
        </div>
        <div class="grid2">
            <label>Name *
                <input type="text" name="person_name" value="{{ old('person_name', $story->person_name) }}" data-live="line" data-no-digits required data-word-max="{{ $c['word_max'] }}"
                       minlength="{{ $c['name_min'] }}" maxlength="{{ $c['name_max'] }}" placeholder="{{ $hint }}">
            </label>
            <label>Role / designation *
                <input type="text" name="person_role" value="{{ old('person_role', $story->person_role) }}" data-live="line" data-no-digits required data-word-max="{{ $c['word_max'] }}"
                       minlength="{{ $c['role_min'] }}" maxlength="{{ $c['role_max'] }}" placeholder="{{ $hint }}">
            </label>
        </div>

        <h3>Visibility</h3>
        <div class="check end">
            <span>Show this section on the About page</span>
            <label class="switch">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $story->is_active))>
                <i></i>
            </label>
        </div>

        <button type="submit" class="btn">Save changes</button>
    </form>
@endsection

@push('scripts')
<script>
    (function () {
        const input = document.getElementById('storyImage'), img = document.getElementById('storyPreview');
        input.addEventListener('rejected', () => { img.style.display = 'none'; });
        input.addEventListener('change', () => setTimeout(() => {
            const f = input.files[0];
            if (!f) return;
            img.src = URL.createObjectURL(f); img.style.display = 'block';
        }, 0));
    })();
</script>
@endpush
