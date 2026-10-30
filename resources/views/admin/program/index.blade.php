@extends('admin.layout')

@section('title', 'Programs Section')
@section('page_title', 'Programs Section')
@section('breadcrumbs')<a href="{{ route('admin.banners.index') }}">Home</a> <span>&rsaquo;</span> Page Settings <span>&rsaquo;</span> Home Page <span>&rsaquo;</span> Programs Section @endsection

@section('content')
    @php($c = config('banner.program'))
    @php($hint = "Letters, numbers, spaces and . , ' - & ! ? only")
    <p class="hint">Edit the section heading and add as many program cards as you need. All fields marked * are mandatory.</p>

    <section class="card form-card" style="max-width: 100%;">
        <form method="POST" action="{{ route('admin.program.settings.update') }}" id="programForm" data-live-validate novalidate>
            @csrf
            @method('PUT')
            <h3>Section content</h3>
            <label>Section label (eyebrow) *
                <input type="text" name="eyebrow" value="{{ old('eyebrow', $program->eyebrow) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                       minlength="{{ $c['eyebrow_min'] }}" maxlength="{{ $c['eyebrow_max'] }}" placeholder="{{ $hint }}">
            </label>
            <label>Highlighted heading line (gold) *
                <input type="text" name="heading_accent" value="{{ old('heading_accent', $program->heading_accent) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                       minlength="{{ $c['accent_min'] }}" maxlength="{{ $c['accent_max'] }}" placeholder="{{ $hint }}">
            </label>
            <label>Heading (second line) *
                <input type="text" name="heading" value="{{ old('heading', $program->heading) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                       minlength="{{ $c['heading_min'] }}" maxlength="{{ $c['heading_max'] }}" placeholder="{{ $hint }}">
            </label>
            <label>Description *
                <textarea name="description" rows="3" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                          minlength="{{ $c['description_min'] }}" maxlength="{{ $c['description_max'] }}" placeholder="{{ $hint }}">{{ old('description', $program->description) }}</textarea>
            </label>
            <div class="check end" style="margin: 8px 0 16px;">
                <span>Show section on website</span>
                <label class="switch">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $program->is_active))>
                    <i></i>
                </label>
            </div>
            <button type="submit" class="btn">Save section</button>
        </form>
    </section>

    <section class="card panel" style="margin-top: 20px;">
        <div class="panel-head">
            <h2>Program Cards ({{ $cards->count() }})</h2>
            <div class="panel-tools">
                <label class="toggle-all">
                    <span class="switch">
                        <input type="checkbox" data-toggle-all="{{ route('admin.program.cards.toggle-all') }}" data-group="cards" @checked($cards->isNotEmpty() && $cards->every->is_active)>
                        <i></i>
                    </span> Toggle All
                </label>
                <button type="button" class="btn red" id="createProgramBtn">+ Add Program</button>
            </div>
        </div>

        <table class="tbl">
            <thead>
                <tr><th>S.N.</th><th>Preview</th><th>Program</th><th>Order</th><th>Status</th><th class="r">Action</th></tr>
            </thead>
            <tbody>
            @forelse($cards as $i => $card)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        @if($card->image)
                            <img class="thumb" src="{{ asset($card->image) }}" alt="" onerror="this.classList.add('missing')">
                        @else
                            <span class="thumb" style="display:inline-flex; align-items:center; justify-content:center; color:#999; font-size:.7rem; background:#f0f2f5;">No image</span>
                        @endif
                    </td>
                    <td>
                        <strong class="strong ellipsis" style="display:block;">{{ $card->title }} @if($card->is_featured)<span class="tag">Featured</span>@endif</strong>
                        <small class="ellipsis" style="color: var(--muted); display:block; max-width: 260px;">{{ $card->duration }} &middot; {{ $card->mode }}</small>
                    </td>
                    <td><span class="badge-gold">#{{ $card->sort_order }}</span></td>
                    <td>
                        <label class="switch">
                            <input type="checkbox" data-toggle="{{ route('admin.program.cards.toggle', $card) }}" data-group="cards" @checked($card->is_active)>
                            <i></i>
                        </label>
                    </td>
                    <td class="r actions">
                        <button type="button" class="act" title="View" data-view="{{ json_encode(['type' => 'image', 'src' => $card->image ? asset($card->image) : '', 'title' => $card->title, 'desc' => implode(' | ', $card->features())]) }}">@include('admin.partials.icon-eye')</button>
                        <button type="button" class="act" title="Edit" data-edit-program="{{ json_encode(['id' => $card->id, 'title' => $card->title, 'features' => $card->features(), 'duration' => $card->duration, 'mode' => $card->mode, 'sort_order' => $card->sort_order, 'is_featured' => $card->is_featured, 'is_active' => $card->is_active, 'image_url' => $card->image ? asset($card->image) : null]) }}">@include('admin.partials.icon-edit')</button>
                        <form method="POST" action="{{ route('admin.program.cards.destroy', $card) }}" data-confirm="Delete this program card?">@csrf @method('DELETE')<button class="act del" title="Delete">@include('admin.partials.icon-trash')</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">No program cards yet. Click "+ Add Program" to create one.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>

    @include('admin.program._canvas')

    <div class="modal" id="viewModal">
        <div class="modal-box">
            <button type="button" class="canvas-close" data-close-modal>&times;</button>
            <div id="viewMedia"></div>
            <h3 id="viewTitle"></h3>
            <p id="viewDesc"></p>
        </div>
    </div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.js"></script>
<script>
    (function () {
        // ---- features editor: a single bullet list, one bullet per feature ----
        const $f = $('#programFeatures'), fErr = document.getElementById('featuresError');
        const FRE = /^[A-Za-z0-9 .,'&!?-]+$/;
        $f.summernote({
            height: 170, placeholder: 'Click the bullet-list button, then type one feature per bullet',
            toolbar: [['para', ['ul']]], disableDragAndDrop: true, shortcuts: false,
            callbacks: {
                onPaste(e) { // paste as plain text only
                    e.preventDefault();
                    const t = (e.originalEvent || e).clipboardData.getData('text/plain');
                    document.execCommand('insertText', false, t);
                },
                onChange() { checkFeatures(); },
            },
        });
        function featureLines() {
            const box = document.createElement('div');
            box.innerHTML = $f.summernote('code').replace(/<\/(li|p|div)>|<br\s*\/?>/gi, m => m + '\n');
            return box.textContent.replace(/ /g, ' ').split(/\n/).map(x => x.trim()).filter(Boolean);
        }
        function checkFeatures() {
            const d = $f[0].dataset, lines = featureLines(); let msg = '';
            if (!lines.length) msg = 'Add at least one feature.';
            else if (lines.length > +d.maxItems) msg = 'Add at most ' + d.maxItems + ' features.';
            else lines.some((l, i) => {
                const n = i + 1;
                if (!FRE.test(l)) return msg = 'Feature ' + n + ": only letters, numbers, spaces and . , ' - & ! ? are allowed (no emojis or special characters).";
                if (/ {2,}/.test(l)) return msg = 'Feature ' + n + ': repeated spaces are not allowed.';
                if (l.length < +d.min || l.length > +d.max) return msg = 'Feature ' + n + ': must be ' + d.min + '-' + d.max + ' characters (currently ' + l.length + ').';
                const w = l.split(/\s+/).find(x => x.length > +d.wordMax);
                if (w) return msg = 'Feature ' + n + ': a word is longer than ' + d.wordMax + ' characters.';
            });
            fErr.textContent = msg;
            document.querySelector('#canvas-program .note-editor').style.borderColor = msg ? '#c0392b' : '';
            return msg;
        }
        // validate before submit (runs ahead of the generic handler)
        document.querySelector('#canvas-program form').addEventListener('submit', e => {
            $f.val($f.summernote('code'));
            if (checkFeatures()) { e.preventDefault(); e.stopImmediatePropagation(); document.querySelector('#canvas-program .note-editable').focus(); }
        }, true);

        const next = {{ ($cards->max('sort_order') ?? 0) + 1 }};
        const preview = document.getElementById('programImgPreview');
        const blank = { title: '', features: [], duration: '', mode: '', sort_order: next, is_featured: false, is_active: true };

        function setFeatures(list) {
            $f.summernote('code', list.length ? '<ul>' + list.map(x => '<li>' + $('<i>').text(x).html() + '</li>').join('') + '</ul>' : '');
            fErr.textContent = '';
            document.querySelector('#canvas-program .note-editor').style.borderColor = '';
        }

        document.getElementById('createProgramBtn').onclick = () => {
            preview.style.display = 'none';
            Admin.openForm('canvas-program', { title: 'Add Program Card', data: blank, fileRequired: 'image' });
            setFeatures([]);
        };
        document.querySelectorAll('[data-edit-program]').forEach(b => b.onclick = () => {
            const d = JSON.parse(b.dataset.editProgram);
            preview.style.display = 'none';
            setFeatures(d.features);
            Admin.openForm('canvas-program', {
                title: 'Edit Program Card', id: d.id, data: d,
                media: d.image_url ? `<p class="hint">Current image (choose a new file to replace it):</p><img src="${d.image_url}" alt="" style="max-height:120px;border-radius:6px;border:1px solid #d5dbe5">` : '',
            });
        });

        const input = document.getElementById('programImage');
        input.addEventListener('rejected', () => { preview.style.display = 'none'; });
        input.addEventListener('change', () => setTimeout(() => {
            const f = input.files[0];
            if (!f) { preview.style.display = 'none'; return; }
            preview.src = URL.createObjectURL(f); preview.style.display = 'block';
        }, 0));
    })();
</script>
@endpush
