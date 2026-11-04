@extends('admin.layout')

@section('title', 'SEO Settings')
@section('page_title', 'SEO Settings')
@section('breadcrumbs')<a href="{{ route('admin.banners.index') }}">Home</a> <span>&rsaquo;</span> Site Settings <span>&rsaquo;</span> SEO Settings @endsection

@section('content')
    <form method="POST" action="{{ route('admin.seo.update') }}" class="card form-card" data-live-validate novalidate>
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

        <label>Focus keyword (100 to 150 words)
            <textarea name="focus_keyword" rows="5" data-live="line" data-word-max="24" maxlength="2000" placeholder="Describe the main topic and keywords this page should rank for">{{ old('focus_keyword', $seo->focus_keyword) }}</textarea>
        </label>
        <p class="hint" id="kwHint" style="margin-top:-6px"></p>

        <button type="submit" class="btn">Save changes</button>
    </form>
@endsection

@push('scripts')
<script>
    // Switching the page reloads the form with that page's saved values.
    document.getElementById('seoPage').addEventListener('change', e => {
        location.href = '{{ route('admin.seo.edit') }}?page=' + encodeURIComponent(e.target.value);
    });
    // Tells whether the focus keyword is used in the title and description.
    (function () {
        const f = document.querySelector('[name=focus_keyword]'), t = document.querySelector('[name=meta_title]'), d = document.querySelector('[name=meta_description]'), out = document.getElementById('kwHint');
        function run() {
            const v = f.value.trim();
            if (!v) { out.textContent = ''; return; }
            const n = v.split(/\s+/).length, ok = n >= 100 && n <= 150;
            out.textContent = n + ' words (must be 100 to 150)' + (ok ? '' : n < 100 ? ' - add ' + (100 - n) + ' more' : ' - remove ' + (n - 150));
            out.style.color = ok ? '#1e8e4e' : '#c0392b';
            return ok;
        }
        [f, t, d].forEach(el => el.addEventListener('input', run));
        run();
        f.form.addEventListener('submit', e => { if (f.value.trim() && !run()) { e.preventDefault(); e.stopImmediatePropagation(); f.focus(); } }, true);
    })();
</script>
@endpush
