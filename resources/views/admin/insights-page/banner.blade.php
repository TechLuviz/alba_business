@extends('admin.layout')

@section('title', 'Insights Banner')
@section('page_title', 'Insights Page Banner')
@section('breadcrumbs')<a href="{{ route('admin.banners.index') }}">Home</a> <span>&rsaquo;</span> Page Settings <span>&rsaquo;</span> Insights Page <span>&rsaquo;</span> Banner Settings @endsection

@section('content')
    @php($c = config('banner.insight_banner'))
    @php($hint = "Letters, numbers, spaces and . , ' - & ! ? only")
    <form method="POST" action="{{ route('admin.insightpage.banner.update') }}" enctype="multipart/form-data" class="card form-card" data-live-validate novalidate>
        @csrf
        @method('PUT')

        <h3>Banner image</h3>
        <div class="spec">
            <strong>Image requirements</strong>
            <ul>
                <li>Recommended: {{ $c['recommended'] }}</li>
                <li>Min {{ $c['min_width'] }} x {{ $c['min_height'] }} px &middot; max {{ $c['max_width'] }} x {{ $c['max_height'] }} px</li>
                <li>JPG, PNG or WEBP &middot; max {{ round($c['max_kb'] / 1024) }} MB &middot; the text is centred, so keep the middle of the picture calm.</li>
            </ul>
        </div>
        <label>Banner image *
            <input type="file" name="image" id="insightBannerImage" accept="image/png,image/jpeg,image/webp" @if(!$banner->image) required @endif
                   data-max-kb="{{ $c['max_kb'] }}" data-min-w="{{ $c['min_width'] }}" data-min-h="{{ $c['min_height'] }}"
                   data-max-w="{{ $c['max_width'] }}" data-max-h="{{ $c['max_height'] }}">
        </label>
        <div class="preview">
            <img id="insightBannerPreview" src="{{ $banner->image ? asset($banner->image) : '' }}" alt="" style="max-height:160px; border-radius:8px; @if(!$banner->image) display:none @endif">
        </div>

        <h3>Banner text</h3>
        <label>Heading line 1 *
            <input type="text" name="heading_line_1" value="{{ old('heading_line_1', $banner->heading_line_1) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                   minlength="{{ $c['heading_min'] }}" maxlength="{{ $c['heading_max'] }}" placeholder="{{ $hint }}">
        </label>
        <label>Heading line 2 *
            <input type="text" name="heading_line_2" value="{{ old('heading_line_2', $banner->heading_line_2) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                   minlength="{{ $c['heading_min'] }}" maxlength="{{ $c['heading_max'] }}" placeholder="{{ $hint }}">
        </label>
        <label>Description *
            <textarea name="description" rows="3" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                      minlength="{{ $c['description_min'] }}" maxlength="{{ $c['description_max'] }}" placeholder="{{ $hint }}">{{ old('description', $banner->description) }}</textarea>
        </label>

        <h3>Visibility</h3>
        <div class="check end">
            <span>Show banner on the Insights page</span>
            <label class="switch">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $banner->is_active))>
                <i></i>
            </label>
        </div>

        <button type="submit" class="btn">Save changes</button>
    </form>
@endsection

@push('scripts')
<script>
    (function () {
        const input = document.getElementById('insightBannerImage'), img = document.getElementById('insightBannerPreview');
        input.addEventListener('rejected', () => { img.style.display = 'none'; });
        input.addEventListener('change', () => setTimeout(() => {
            const f = input.files[0];
            if (!f) return;
            img.src = URL.createObjectURL(f); img.style.display = 'block';
        }, 0));
    })();
</script>
@endpush
