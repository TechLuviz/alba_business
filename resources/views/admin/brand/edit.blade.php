@extends('admin.layout')

@section('title', 'Logo & Brand')
@section('page_title', 'Logo & Brand')
@section('breadcrumbs')<a href="{{ route('admin.banners.index') }}">Home</a> <span>&rsaquo;</span> Site Settings <span>&rsaquo;</span> Logo &amp; Brand @endsection

@section('content')
    <form method="POST" action="{{ route('admin.brand.update') }}" enctype="multipart/form-data" class="card form-card">
        @csrf
        @method('PUT')

        <div class="spec">
            <strong>Logo requirements</strong>
            <ul>
                <li>Recommended: {{ config('banner.logo.recommended') }}</li>
                <li>Min {{ config('banner.logo.min_width') }} x {{ config('banner.logo.min_height') }} px &middot; max {{ config('banner.logo.max_width') }} x {{ config('banner.logo.max_height') }} px</li>
                <li>PNG, JPG or WEBP &middot; max {{ round(config('banner.logo.max_kb') / 1024) }} MB</li>
            </ul>
        </div>
        <label>Logo image
            <input type="file" name="brand_logo" accept="image/png,image/jpeg,image/webp" id="logoInput"
                   data-max-kb="{{ config('banner.logo.max_kb') }}" data-min-w="{{ config('banner.logo.min_width') }}" data-min-h="{{ config('banner.logo.min_height') }}"
                   data-max-w="{{ config('banner.logo.max_width') }}" data-max-h="{{ config('banner.logo.max_height') }}">
        </label>
        <div class="preview logo-preview">
            <img id="logoPreview" src="{{ $brand->brand_logo ? asset($brand->brand_logo) : '' }}" alt="" @if(!$brand->brand_logo) style="display:none" @endif>
        </div>
        @if($brand->brand_logo)
            <label class="check"><input type="checkbox" name="remove_logo" value="1"> Remove current logo (show text instead)</label>
        @endif

        <div class="spec">
            <strong>Favicon requirements</strong>
            <ul>
                <li>Recommended: {{ config('banner.favicon.recommended') }}</li>
                <li>PNG, ICO, JPG or WEBP &middot; max {{ config('banner.favicon.max_kb') }} KB</li>
            </ul>
        </div>
        <label>Favicon
            <input type="file" name="brand_favicon" accept="image/png,image/x-icon,image/vnd.microsoft.icon,image/jpeg,image/webp,.ico" id="faviconInput">
        </label>
        <div class="preview logo-preview">
            <img id="faviconPreview" src="{{ $brand->brand_favicon ? asset($brand->brand_favicon) : '' }}" alt="" style="max-width:64px;max-height:64px;@if(!$brand->brand_favicon)display:none @endif">
        </div>
        @if($brand->brand_favicon)
            <label class="check"><input type="checkbox" name="remove_favicon" value="1"> Remove current favicon</label>
        @endif

        <button type="submit" class="btn">Save changes</button>
    </form>
@endsection

@push('scripts')
<script>
    document.getElementById('logoInput').addEventListener('change', e => {
        const f = e.target.files[0], img = document.getElementById('logoPreview');
        if (f) { img.src = URL.createObjectURL(f); img.style.display = 'block'; }
    });
    document.getElementById('faviconInput').addEventListener('change', e => {
        const f = e.target.files[0], img = document.getElementById('faviconPreview');
        if (f) { img.src = URL.createObjectURL(f); img.style.display = 'block'; }
    });
</script>
@endpush