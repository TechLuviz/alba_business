@extends('admin.layout')

@section('title', 'Content Section')
@php($isAbout = ($page ?? 'home') === 'about')
@section('page_title', ($isAbout ? 'About Page' : 'Home Page') . ' - Content Section')
@section('breadcrumbs')<a href="{{ route('admin.banners.index') }}">Home</a> <span>&rsaquo;</span> Page Settings <span>&rsaquo;</span> {{ ($page ?? 'home') === 'about' ? 'About Page' : 'Home Page' }} <span>&rsaquo;</span> Content Section @endsection

@section('content')
    @php($c = config('banner.home_content'))
    <form method="POST" action="{{ $isAbout ? route('admin.aboutpage.content.update') : route('admin.homecontent.update') }}" class="card form-card" id="homeContentForm" data-live-validate novalidate style="max-width: 100%;">
        @csrf
        @method('PUT')

        <p class="hint">{{ $isAbout ? 'This section appears on the About page, just before the Get In Touch section.' : 'This section appears on the home page right after the Insights / Team area.' }} It stays hidden until it has text and the switch below is on.</p>

        <h3>Content *</h3>
        <p class="hint">Headings, bold, lists and links are supported. Pasted formatting is cleaned automatically. {{ $c['content_min'] }}&ndash;{{ $c['content_max'] }} words.</p>
        <textarea name="content" id="homeContentBody">{{ old('content', $section->content) }}</textarea>
        <p class="field-error" id="homeContentError" role="alert"></p>
        @error('content')<p class="field-error" style="display:block">{{ $message }}</p>@enderror
        <small id="homeContentCount" style="color: var(--muted)"></small>

        <h3>Visibility</h3>
        <div class="check end">
            <span>Show this section on the {{ $isAbout ? 'About' : 'home' }} page</span>
            <label class="switch">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $section->is_active))>
                <i></i>
            </label>
        </div>

        <button type="submit" class="btn">Save changes</button>
    </form>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.js"></script>
<script>
    (function () {
        const MIN = {{ $c['content_min'] }}, MAX = {{ $c['content_max'] }};
        const $ed = $('#homeContentBody'), err = document.getElementById('homeContentError'), count = document.getElementById('homeContentCount');
        $ed.summernote({
            height: 300, placeholder: 'Write the section text here...', disableDragAndDrop: true,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol']],
                ['insert', ['link']],
                ['misc', ['undo', 'redo']],
            ],
            styleTags: ['p', 'h3', 'h4', 'blockquote'],
            callbacks: { onChange() { check(); } },
        });
        function wordCount() {
            const box = document.createElement('div');
            box.innerHTML = $ed.summernote('code').replace(/<\/(p|li|h[1-6]|blockquote)>|<br\s*\/?>/gi, m => m + ' ');
            const t = box.textContent.replace(/[\s ]+/g, ' ').trim();
            return t ? t.split(' ').length : 0;
        }
        function check() {
            const n = wordCount(); let msg = '';
            if (n < MIN) msg = 'The content must have at least ' + MIN + ' words (currently ' + n + ').';
            else if (n > MAX) msg = 'The content may not be longer than ' + MAX + ' words (currently ' + n + ').';
            err.textContent = msg; err.style.display = msg ? 'block' : '';
            document.querySelector('.note-editor').style.borderColor = msg ? '#c0392b' : '';
            count.textContent = n + ' / ' + MAX + ' words (minimum ' + MIN + ')';
            return msg;
        }
        check();
        document.getElementById('homeContentForm').addEventListener('submit', e => {
            $ed.val($ed.summernote('code'));
            if (check()) { e.preventDefault(); e.stopImmediatePropagation(); document.querySelector('.note-editable').focus(); }
        }, true);

    })();
</script>
@endpush
