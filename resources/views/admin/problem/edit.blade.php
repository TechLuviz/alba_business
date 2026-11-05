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
            <input type="text" name="eyebrow" value="{{ old('eyebrow', $problem->eyebrow) }}" data-live="line" data-no-digits required data-word-max="{{ $c['word_max'] }}"
                   minlength="{{ $c['eyebrow_min'] }}" maxlength="{{ $c['eyebrow_max'] }}" placeholder="{{ $hint }}">
        </label>
        <label>Heading *
            <input type="text" name="heading" value="{{ old('heading', $problem->heading) }}" data-live="line" data-no-digits required data-word-max="{{ $c['word_max'] }}"
                   minlength="{{ $c['heading_min'] }}" maxlength="{{ $c['heading_max'] }}" placeholder="{{ $hint }}">
        </label>
        <label>Highlighted heading line (gold) *
            <input type="text" name="heading_accent" value="{{ old('heading_accent', $problem->heading_accent) }}" data-live="line" data-no-digits required data-word-max="{{ $c['word_max'] }}"
                   minlength="{{ $c['accent_min'] }}" maxlength="{{ $c['accent_max'] }}" placeholder="{{ $hint }}">
        </label>
        <label>Description *
            <textarea name="description" rows="4" data-live="line" data-no-digits required data-word-max="{{ $c['description_word_max'] }}"
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
                        <span class="img-wrap" data-img-wrap="card{{ $n }}" data-saved="{{ $path ? asset($path) : '' }}" @if(!$path) style="display:none" @endif>
                            <img id="card{{ $n }}Preview" src="{{ $path ? asset($path) : '' }}" alt="">
                            <button type="button" class="img-remove" data-img-remove="card{{ $n }}" aria-label="Remove card {{ $n }} image" title="Remove image">&times;</button>
                        </span>
                        <input type="hidden" name="remove_card{{ $n }}_image" value="0" data-remove-flag="card{{ $n }}">
                    </div>
                    <label>Card {{ $n }} caption *
                        <input type="text" name="card{{ $n }}_title" value="{{ old("card{$n}_title", $problem->{"card{$n}_title"}) }}" data-live="line" data-no-digits required data-word-max="{{ $c['word_max'] }}"
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

    <style>
        .img-wrap { position: relative; display: inline-block; line-height: 0; }
        .img-remove { position: absolute; top: 6px; right: 6px; width: 26px; height: 26px; border: 0; border-radius: 50%; background: rgba(15, 23, 42, .78); color: #fff; font-size: 18px; line-height: 26px; text-align: center; padding: 0; cursor: pointer; transition: background .2s, transform .2s; }
        .img-remove:hover, .img-remove:focus-visible { background: #e5321b; transform: scale(1.1); outline: none; }
    </style>
    <script>
    (function () {
        document.querySelectorAll('[data-image-input]').forEach(function (input) {
            var k = input.dataset.imageInput;
            var wrap = document.querySelector('[data-img-wrap="' + k + '"]');
            var img = document.getElementById(k + 'Preview');
            var flag = document.querySelector('[data-remove-flag="' + k + '"]');
            var saved = wrap.dataset.saved, hadSaved = !!saved, baseRequired = input.required;

            function sync() {
                var hasFile = input.files && input.files[0];
                var showing = hasFile || (hadSaved && flag.value !== '1');
                wrap.style.display = showing ? '' : 'none';
                input.required = baseRequired || (hadSaved && flag.value === '1');
            }
            input.addEventListener('change', function () {
                var f = input.files && input.files[0];
                if (f && /^image\//.test(f.type)) { img.src = URL.createObjectURL(f); flag.value = '0'; }
                else if (!f) { img.src = saved; }
                sync();
            });
            wrap.querySelector('[data-img-remove]').addEventListener('click', function () {
                if (input.files && input.files[0]) {          // discard the newly chosen file, go back to the saved image
                    input.value = '';
                    img.src = saved;
                } else {                                      // remove the saved image: a replacement is then required
                    flag.value = '1';
                    img.removeAttribute('src');
                    input.focus();
                }
                sync();
            });
        });
    })();
    </script>
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
