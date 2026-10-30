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


        <button type="submit" class="btn">Save changes</button>
    </form>
@endsection

@push('scripts')
<script>
    document.getElementById('logoInput').addEventListener('change', e => {
        const f = e.target.files[0], img = document.getElementById('logoPreview');
        if (f) { img.src = URL.createObjectURL(f); img.style.display = 'block'; }
    });
</script>
@endpush