@extends('admin.layout')

@section('title', 'Problem Section')
@section('page_title', 'Problem Section')
@section('breadcrumbs')<a href="{{ route('admin.banners.index') }}">Home</a> <span>&rsaquo;</span> Page Settings <span>&rsaquo;</span> Home Page <span>&rsaquo;</span> Problem Section @endsection

@section('content')
    @php($c = config('banner.problem'))
    @php($img = $c['image'])
    @php($hint = "Letters, numbers, spaces and . , ' - & ! ? only")
    <form method="POST" action="{{ route('admin.problem.update') }}" enctype="multipart/form-data" class="card form-card" id="problemForm" data-live-validate novalidate>
        @csrf
        @method('PUT')

        <h3>Section content</h3>
        <label>Section label (eyebrow) *
            <input type="text" name="eyebrow" value="{{ old('eyebrow', $problem->eyebrow) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                   minlength="{{ $c['eyebrow_min'] }}" maxlength="{{ $c['eyebrow_max'] }}" placeholder="{{ $hint }}">
        </label>
        <label>Heading *
            <input type="text" name="heading" value="{{ old('heading', $problem->heading) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                   minlength="{{ $c['heading_min'] }}" maxlength="{{ $c['heading_max'] }}" placeholder="{{ $hint }}">
        </label>
        <label>Highlighted heading line (gold) *
            <input type="text" name="heading_accent" value="{{ old('heading_accent', $problem->heading_accent) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                   minlength="{{ $c['accent_min'] }}" maxlength="{{ $c['accent_max'] }}" placeholder="{{ $hint }}">
        </label>
        <label>Description *
            <textarea name="description" rows="4" data-live="line" required data-word-max="{{ $c['description_word_max'] }}"
                      minlength="{{ $c['description_min'] }}" maxlength="{{ $c['description_max'] }}" placeholder="{{ $hint }}">{{ old('description', $problem->description) }}</textarea>
        </label>

        <h3>Cards</h3>
        <p class="hint">All fields marked * are mandatory.</p>
        <div class="spec">
            <strong>Image requirements</strong>
            <ul>
                <li>Recommended: {{ $img['recommended'] }}</li>
                <li>Min {{ $img['min_width'] }} x {{ $img['min_height'] }} px &middot; max {{ $img['max_width'] }} x {{ $img['max_height'] }} px</li>
                <li>JPG, JPEG, PNG or WEBP &middot; max {{ round($img['max_kb'] / 1024) }} MB &middot; images are cropped to fit, so keep the subject centred</li>
            </ul>
        </div>
        <div class="grid2">
            @foreach(\App\Models\ProblemSection::CARDS as $n)
                @php($path = $problem->{"card{$n}_image"})
                <div class="img-field">
                    <label>Card {{ $n }} image *
                        <input type="file" name="card{{ $n }}_image" accept="image/png,image/jpeg,image/webp" data-image-input="card{{ $n }}"
                               @if(!$path) required @endif data-max-kb="{{ $img['max_kb'] }}" data-min-w="{{ $img['min_width'] }}" data-min-h="{{ $img['min_height'] }}"
                               data-max-w="{{ $img['max_width'] }}" data-max-h="{{ $img['max_height'] }}">
                    </label>
                    <div class="preview">
                        <img id="card{{ $n }}Preview" src="{{ $path ? asset($path) : '' }}" alt="" @if(!$path) style="display:none" @endif>
                    </div>
                    <label>Card {{ $n }} caption *
                        <input type="text" name="card{{ $n }}_title" value="{{ old("card{$n}_title", $problem->{"card{$n}_title"}) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                               minlength="{{ $c['caption_min'] }}" maxlength="{{ $c['caption_max'] }}" placeholder="{{ $hint }}">
                    </label>
                </div>
            @endforeach
        </div>

        <h3>Visibility</h3>
        <div class="check end">
            <span>Show section on website</span>
            <label class="switch">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $problem->is_active))>
                <i></i>
            </label>
        </div>

        <button type="submit" class="btn">Save changes</button>
    </form>
@endsection

@push('scripts')
<script>
    // live image preview (the type / size / dimension checks and messages come from admin.js)
    document.querySelectorAll('[data-image-input]').forEach(input => {
        const img = document.getElementById(input.dataset.imageInput + 'Preview');
        input.addEventListener('rejected', () => { img.style.display = 'none'; });
        input.addEventListener('change', () => setTimeout(() => {
            const f = input.files[0];
            if (!f) return;
            img.src = URL.createObjectURL(f); img.style.display = 'block';
        }, 0));
    });
</script>
@endpush
