{{-- Right-side canvas form shared by dynamic + video banners. Expects: $id ('dynamic'|'video'), $title --}}
@php
    $t = config('banner.text');
    $img = config('banner.image');
    $vid = config('banner.video');
    $o = fn ($key, $default = '') => old('_panel') === $id ? old($key, $default) : $default;
    $isVideo = $id === 'video';
@endphp

<aside class="offcanvas" id="canvas-{{ $id }}"
       data-store="{{ route("admin.banners.$id.store") }}"
       data-update="{{ route("admin.banners.$id.update", '__ID__') }}">
    <div class="canvas-head"><h3 class="canvas-title">{{ $title }}</h3><button type="button" class="canvas-close" data-close>&times;</button></div>

    <form method="POST" enctype="multipart/form-data" class="canvas-body" novalidate>
        @csrf
        <input type="hidden" name="_method" value="POST">
        <input type="hidden" name="_panel" value="{{ $id }}">
        <input type="hidden" name="_id" value="{{ $o('_id') }}">

        @if($isVideo)
            <div class="spec">
                <strong>Video requirements</strong>
                <ul>
                    <li>Recommended: {{ $vid['recommended'] }}</li>
                    <li>Formats: MP4, WebM or OGG &middot; max {{ round($vid['max_kb'] / 1024) }} MB</li>
                    <li>Keep the left side of the footage calm &mdash; the heading is shown there.</li>
                </ul>
            </div>
            <label>Video file *
                <input type="file" name="video" accept="video/mp4,video/webm,video/ogg" data-max-kb="{{ $vid['max_kb'] }}">
            </label>
        @else
            <div class="spec">
                <strong>Image requirements</strong>
                <ul>
                    <li>Recommended: {{ $img['recommended'] }}</li>
                    <li>Min {{ $img['min_width'] }} x {{ $img['min_height'] }} px &middot; max {{ $img['max_width'] }} x {{ $img['max_height'] }} px</li>
                    <li>JPG, PNG or WEBP &middot; max {{ round($img['max_kb'] / 1024) }} MB</li>
                    <li>Keep the left 45% of the image dark or uncluttered &mdash; the heading is shown there.</li>
                </ul>
            </div>
            <label>Banner image *
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                       data-max-kb="{{ $img['max_kb'] }}" data-min-w="{{ $img['min_width'] }}" data-min-h="{{ $img['min_height'] }}"
                       data-max-w="{{ $img['max_width'] }}" data-max-h="{{ $img['max_height'] }}">
            </label>
        @endif
        <div class="media-current"></div>

        <label>Heading line 1 {{ $isVideo ? '' : '*' }}
            <input type="text" name="heading_line_1" value="{{ $o('heading_line_1') }}" minlength="{{ $t['heading_min'] }}" maxlength="{{ $t['heading_max'] }}" @if(!$isVideo) required @endif>
        </label>
        <label>Heading line 2
            <input type="text" name="heading_line_2" value="{{ $o('heading_line_2') }}" minlength="{{ $t['heading_min'] }}" maxlength="{{ $t['heading_max'] }}">
        </label>
        <label>Heading accent (italic line)
            <input type="text" name="heading_accent" value="{{ $o('heading_accent') }}" minlength="{{ $t['accent_min'] }}" maxlength="{{ $t['accent_max'] }}">
        </label>
        <label>Description ({{ $t['description_min'] }}-{{ $t['description_max'] }} characters)
            <textarea name="description" data-single-line rows="4" minlength="{{ $t['description_min'] }}" maxlength="{{ $t['description_max'] }}">{{ $o('description') }}</textarea>
        </label>
        <div class="grid2">
            <label>Display order (0-{{ $t['order_max'] }})
                <input type="number" name="sort_order" min="0" max="{{ $t['order_max'] }}" value="{{ $o('sort_order', 0) }}">
            </label>
            <label class="check end"><input type="checkbox" name="is_active" value="1" @checked(old('_panel') === $id ? old('is_active') : !$isVideo)> Active</label>
        </div>

        <div class="canvas-foot">
            <button type="submit" class="btn red">{{ $isVideo ? 'Save video' : 'Save banner' }}</button>
            <button type="button" class="btn ghost" data-close>Cancel</button>
        </div>
    </form>
</aside>