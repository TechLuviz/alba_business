@extends('admin.layout')

@section('title', 'SEO Settings')
@section('page_title', 'SEO Settings')
@section('breadcrumbs')<a href="{{ route('admin.banners.index') }}">Home</a> <span>&rsaquo;</span> Site Settings <span>&rsaquo;</span> SEO Settings @endsection

@section('content')
    <form method="POST" action="{{ route('admin.seo.update') }}" class="card form-card">
        @csrf
        @method('PUT')

        <label>Page
            <select name="page" id="seoPage">
                @foreach($pages as $key => $label)
                    <option value="{{ $key }}" @selected($page === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </label>

        <label>Title
            <input type="text" name="meta_title" value="{{ old('meta_title', $seo->meta_title) }}" placeholder="Page title shown in search results">
        </label>

        <label>Description
            <textarea name="meta_description" rows="4" placeholder="Short summary shown in search results">{{ old('meta_description', $seo->meta_description) }}</textarea>
        </label>

        <button type="submit" class="btn">Save changes</button>
    </form>
@endsection

@push('scripts')
<script>
    // Switching the page reloads the form with that page's saved values.
    document.getElementById('seoPage').addEventListener('change', e => {
        location.href = '{{ route('admin.seo.edit') }}?page=' + encodeURIComponent(e.target.value);
    });
</script>
@endpush
