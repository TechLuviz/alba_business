@extends('admin.layout')

@section('title', 'About Section')
@section('page_title', 'About Section')
@section('breadcrumbs')<a href="{{ route('admin.banners.index') }}">Home</a> <span>&rsaquo;</span> Page Settings <span>&rsaquo;</span> Home Page <span>&rsaquo;</span> About Section @endsection

@section('content')
    @php($c = config('banner.about'))
    @php($img = config('banner.about_image'))
    <form method="POST" action="{{ route('admin.about.update') }}" enctype="multipart/form-data" class="card form-card" id="aboutForm" data-live-validate>
        @csrf
        @method('PUT')

        <h3>Section content</h3>
        <label>Section label (eyebrow)
            <input type="text" name="eyebrow" data-live="line" data-no-digits value="{{ old('eyebrow', $about->eyebrow) }}" maxlength="{{ $c['eyebrow_max'] }}">
        </label>
        <label>Main heading
            <input type="text" name="heading" data-live="line" data-no-digits value="{{ old('heading', $about->heading) }}" minlength="{{ $c['heading_min'] }}" maxlength="{{ $c['heading_max'] }}">
        </label>
        <label>Description
            <textarea name="description" data-live="line" data-no-digits rows="4" minlength="{{ $c['description_min'] }}" maxlength="{{ $c['description_max'] }}">{{ old('description', $about->description) }}</textarea>
        </label>

        <h3>Images</h3>
        <div class="spec">
            <strong>Image requirements</strong>
            <ul>
                <li>Recommended: {{ $img['recommended'] }}</li>
                <li>Min {{ $img['min_width'] }} x {{ $img['min_height'] }} px &middot; max {{ $img['max_width'] }} x {{ $img['max_height'] }} px</li>
                <li>JPG, JPEG, PNG or WEBP &middot; max {{ round($img['max_kb'] / 1024) }} MB &middot; images are cropped to fit, so keep the subject centred</li>
            </ul>
        </div>
        <div class="grid2">
            @foreach(['primary' => 'Primary image (large, background)', 'secondary' => 'Secondary image (small, overlapping)'] as $key => $label)
                @php($path = $about->{$key . '_image'})
                <div class="img-field">
                    <label>{{ $label }}
                        <input type="file" name="{{ $key }}_image" accept="image/png,image/jpeg,image/webp" data-image-input="{{ $key }}"
                               data-max-kb="{{ $img['max_kb'] }}" data-min-w="{{ $img['min_width'] }}" data-min-h="{{ $img['min_height'] }}"
                               data-max-w="{{ $img['max_width'] }}" data-max-h="{{ $img['max_height'] }}">
                        <small class="help">Image must be between {{ $img['min_width'] }}x{{ $img['min_height'] }} and {{ $img['max_width'] }}x{{ $img['max_height'] }} pixels (max {{ round($img['max_kb'] / 1024) }} MB).</small>
                        <span class="field-error" data-error="{{ $key }}"></span>
                    </label>
                    <div class="preview">
                        <img id="{{ $key }}Preview" src="{{ $path ? asset($path) : '' }}" alt="" @if(!$path) style="display:none" @endif>
                    </div>
                    @if($path)
                        <label class="check"><input type="checkbox" name="remove_{{ $key }}" value="1"> Remove current image</label>
                    @endif
                </div>
            @endforeach
        </div>

        <h3>Layout &amp; visibility</h3>
        <div class="grid2">
            <label>Image position
                <select name="image_position">
                    @foreach(['left' => 'Images on the left', 'right' => 'Images on the right'] as $v => $t)
                        <option value="{{ $v }}" @selected(old('image_position', $about->image_position) === $v)>{{ $t }}</option>
                    @endforeach
                </select>
            </label>
            <div class="check end">
                <span>Show section on website</span>
                <label class="switch">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $about->is_active))>
                    <i></i>
                </label>
            </div>
        </div>

        <button type="submit" class="btn">Save changes</button>
    </form>
@endsection

@push('scripts')
<script>
    (function () {
        // inline validation: no leading spaces, repeated spaces, emojis or unicode
        const TEXT = '#aboutForm input[name=eyebrow], #aboutForm input[name=heading], #aboutForm textarea[name=description]';
        const rule = el => {
            const v = el.value;
            if (/[^\x20-\x7E\r\n]/.test(v)) return 'Emojis, special unicode and control characters are not allowed.';
            if (/^\s/.test(v)) return 'Leading spaces are not allowed.';
            if (/ {2,}/.test(v)) return 'Multiple spaces in a row are not allowed.';
            if (v !== '' && v.trim() === '') return 'This field cannot be blank spaces only.';
            if (v.trim() !== '' && el.minLength > 0 && v.trim().length < el.minLength) return 'Must be at least ' + el.minLength + ' characters.';
            return '';
        };
        const show = el => {
            let e = el.parentElement.querySelector('.field-error');
            if (!e) { e = document.createElement('small'); e.className = 'field-error'; el.parentElement.appendChild(e); }
            e.textContent = rule(el);
            el.classList.toggle('invalid', !!e.textContent);
            return e.textContent;
        };
        document.querySelectorAll(TEXT).forEach(el => {
            el.addEventListener('input', () => show(el));
            el.addEventListener('blur', () => { el.value = el.value.trim(); show(el); el.counterUpdate && el.counterUpdate(); });
        });
        document.querySelectorAll(TEXT).forEach(el => { if (el.value) show(el); }); // flag saved values on load
        document.getElementById('aboutForm').addEventListener('submit', e => {
            let first = null;
            document.querySelectorAll(TEXT).forEach(el => { el.value = el.value.replace(/[ \t]+$/, ''); if (show(el) && !first) first = el; });
            if (first) { e.preventDefault(); first.focus(); }
        });

        // image preview + client-side type/size check
        document.querySelectorAll('[data-image-input]').forEach(input => {
            const key = input.dataset.imageInput, img = document.getElementById(key + 'Preview');
            const err = document.querySelector('[data-error="' + key + '"]');
            input.addEventListener('change', () => {
                const f = input.files[0]; err.textContent = '';
                if (!f) return;
                if (!['image/jpeg', 'image/png', 'image/webp'].includes(f.type)) { err.textContent = 'Only JPG, PNG or WEBP images are allowed.'; input.value = ''; return; }
                if (f.size > input.dataset.maxKb * 1024) { err.textContent = 'Image is larger than ' + Math.round(input.dataset.maxKb / 1024) + ' MB.'; input.value = ''; return; }
                img.src = URL.createObjectURL(f); img.style.display = 'block';
            });
        });
    })();
</script>
@endpush
