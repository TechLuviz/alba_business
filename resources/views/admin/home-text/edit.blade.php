@extends('admin.layout')

@section('title', 'Homepage Content')
@section('page_title', 'Homepage Content')
@section('breadcrumbs')<a href="{{ route('admin.banners.index') }}">Home</a> <span>&rsaquo;</span> Page Settings <span>&rsaquo;</span> Home Page <span>&rsaquo;</span> Homepage Content @endsection

@section('content')
    @php($c = config('banner.home_text'))
    <form method="POST" action="{{ route('admin.home-text.update') }}" class="card form-card" id="homeTextForm" novalidate>
        @csrf
        @method('PUT')

        <div class="spec">
            <strong>Text section rules</strong>
            <ul>
                <li>Shown below the banner. As visitors scroll, the text changes from gray to white (and back to gray when scrolling up).</li>
                <li>English letters, numbers, spaces and <code>&amp; , . ' -</code> only &mdash; no emojis, unicode or other special characters.</li>
                <li>{{ $c['min'] }}&ndash;{{ $c['max'] }} characters per line &middot; a single word up to {{ $c['word_max'] }} characters.</li>
                <li>Leave both lines empty to hide the section.</li>
            </ul>
        </div>

        @foreach([1 => 'required', 2 => ''] as $n => $req)
            <label>Heading line {{ $n }}{{ $n === 1 ? ' *' : '' }}
                <input type="text" name="heading_line_{{ $n }}" value="{{ old("heading_line_$n", $section->{"heading_line_$n"}) }}"
                       maxlength="{{ $c['max'] }}" autocomplete="off" data-text-field {{ $req }}>
                <span class="counter"><b>0</b> / {{ $c['max'] }}</span>
            </label>
        @endforeach

        <button type="submit" class="btn">Save changes</button>
    </form>
@endsection

@push('scripts')
<script>
    (function () {
        const max = {{ $c['max'] }}, min = {{ $c['min'] }}, wordMax = {{ $c['word_max'] }};
        const ok = /^[A-Za-z0-9&,.'\-]+(?: [A-Za-z0-9&,.'\-]+)*$/;
        const form = document.getElementById('homeTextForm');
        const fields = form.querySelectorAll('[data-text-field]');

        // Strip anything not allowed while typing/pasting (emoji, unicode, symbols, double spaces).
        function sanitize(el) {
            el.value = el.value.replace(/[^A-Za-z0-9&,.'\- ]/g, '').replace(/^ +/, '').replace(/ {2,}/g, ' ').slice(0, max);
        }
        function error(el) {
            const v = el.value.trim();
            if (!v) return el.required ? 'This line is required.' : '';
            if (v.length < min) return 'Use at least ' + min + ' characters.';
            if (!ok.test(v)) return 'Only letters, numbers, spaces and & , . \' - are allowed.';
            if (v.split(' ').some(w => w.length > wordMax)) return 'A single word may not exceed ' + wordMax + ' characters.';
            return '';
        }
        function refresh(el) {
            el.parentElement.querySelector('.counter b').textContent = el.value.length;
            el.parentElement.querySelector('.counter').classList.toggle('bad', el.value.length >= max);
        }

        fields.forEach(el => {
            refresh(el);
            el.addEventListener('input', () => { sanitize(el); refresh(el); el.setCustomValidity(''); });
            el.addEventListener('blur', () => { el.value = el.value.trim(); refresh(el); });
        });
        form.addEventListener('submit', e => {
            let bad = null;
            fields.forEach(el => { el.value = el.value.trim(); const m = error(el); el.setCustomValidity(m); if (m && !bad) bad = el; });
            if (bad) { e.preventDefault(); bad.reportValidity(); }
        });
    })();
</script>
@endpush
