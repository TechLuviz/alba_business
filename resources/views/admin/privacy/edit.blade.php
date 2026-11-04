@extends('admin.layout')

@php($isTerms = ($type ?? 'privacy') === 'terms')
@php($pageName = $isTerms ? 'Terms of Service' : 'Privacy Policy')
@section('title', $pageName)
@section('page_title', $pageName)
@section('breadcrumbs')<a href="{{ route('admin.banners.index') }}">Home</a> <span>&rsaquo;</span> Site Settings <span>&rsaquo;</span> {{ $pageName }} @endsection

@section('content')
    @php($c = config('banner.privacy'))
    @php($hint = "Letters, numbers, spaces and . , ' - & ! ? only")
    <form method="POST" action="{{ route($isTerms ? 'admin.terms.update' : 'admin.privacy.update') }}" class="card form-card" id="privacyForm" data-live-validate novalidate style="max-width: 100%;">
        @csrf
        @method('PUT')

        <h3>Page</h3>
        <label>Page title *
            <input type="text" name="title" value="{{ old('title', $policy->title) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                   minlength="{{ $c['title_min'] }}" maxlength="{{ $c['title_max'] }}" placeholder="{{ $hint }}">
        </label>

        <h3>Policy text</h3>
        <p class="hint">Write the policy in the editor: headings, bold, lists and links are supported. Pasted formatting is cleaned automatically and scripts, images and styles are removed. {{ number_format($c['content_min']) }}&ndash;{{ number_format($c['content_max']) }} characters.</p>
        <textarea name="content" id="privacyContent">{{ old('content', $policy->content) }}</textarea>
        <p class="field-error" id="privacyError" role="alert"></p>
        @error('content')<p class="field-error" style="display:block">{{ $message }}</p>@enderror
        <small id="privacyCount" style="color: var(--muted)"></small>

        <h3>Visibility</h3>
        <div class="check end">
            <span>Show the {{ $pageName }} page and its footer link</span>
            <label class="switch">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $policy->is_active))>
                <i></i>
            </label>
        </div>

        <button type="submit" class="btn">Save changes</button>
        @if($policy->is_active)<a href="{{ route($isTerms ? 'terms' : 'privacy') }}" target="_blank" rel="noopener" class="btn ghost" style="margin-left:8px">View page</a>@endif
    </form>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.js"></script>
<script>
    (function () {
        const MIN = {{ $c['content_min'] }}, MAX = {{ $c['content_max'] }};
        const $ed = $('#privacyContent'), err = document.getElementById('privacyError'), count = document.getElementById('privacyCount');
        $ed.summernote({
            height: 420, placeholder: 'Write the page text here...', disableDragAndDrop: true,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol']],
                ['insert', ['link', 'hr']],
                ['misc', ['undo', 'redo']],
            ],
            styleTags: ['p', 'h2', 'h3', 'h4', 'blockquote'],
            callbacks: { onChange() { check(); } },
        });
        function textLen() {
            const box = document.createElement('div');
            box.innerHTML = $ed.summernote('code').replace(/<\/(p|li|h[1-6]|blockquote)>|<br\s*\/?>/gi, m => m + ' ');
            return box.textContent.replace(/[\s ]+/g, ' ').trim().length;
        }
        function check() {
            const n = textLen(); let msg = '';
            if (n < MIN) msg = 'The policy text must have at least ' + MIN + ' characters (currently ' + n + ').';
            else if (n > MAX) msg = 'The policy text may not be longer than ' + MAX.toLocaleString() + ' characters (currently ' + n.toLocaleString() + ').';
            err.textContent = msg; err.style.display = msg ? 'block' : '';
            document.querySelector('.note-editor').style.borderColor = msg ? '#c0392b' : '';
            count.textContent = n.toLocaleString() + ' / ' + MAX.toLocaleString() + ' characters';
            return msg;
        }
        check();
        document.getElementById('privacyForm').addEventListener('submit', e => {
            $ed.val($ed.summernote('code'));
            if (check()) { e.preventDefault(); e.stopImmediatePropagation(); document.querySelector('.note-editable').focus(); }
        }, true);
    })();
</script>
@endpush
