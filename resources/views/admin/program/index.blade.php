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
                <input type="text" name="eyebrow" value="{{ old('eyebrow', $program->eyebrow) }}" data-live="line" data-no-digits required data-word-max="{{ $c['word_max'] }}"
                       minlength="{{ $c['eyebrow_min'] }}" maxlength="{{ $c['eyebrow_max'] }}" placeholder="{{ $hint }}">
            </label>
            <label>Highlighted heading line (gold) *
                <input type="text" name="heading_accent" value="{{ old('heading_accent', $program->heading_accent) }}" data-live="line" data-no-digits required data-word-max="{{ $c['word_max'] }}"
                       minlength="{{ $c['accent_min'] }}" maxlength="{{ $c['accent_max'] }}" placeholder="{{ $hint }}">
            </label>
            <label>Heading (second line) *
                <input type="text" name="heading" value="{{ old('heading', $program->heading) }}" data-live="line" data-no-digits required data-word-max="{{ $c['word_max'] }}"
                       minlength="{{ $c['heading_min'] }}" maxlength="{{ $c['heading_max'] }}" placeholder="{{ $hint }}">
            </label>
            <label>Description *
                <textarea name="description" rows="3" data-live="line" data-no-digits required data-word-max="{{ $c['word_max'] }}"
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

    <section class="card form-card" style="max-width: 100%; margin-top: 20px;">
        <form method="POST" action="{{ route('admin.program.help.update') }}" id="programHelpForm" enctype="multipart/form-data" data-live-validate novalidate>
            @csrf
            @method('PUT')
            <h3>Program detail page &ndash; contact card</h3>
            @php($hint = "Letters, numbers, spaces and . , ' - & ! ? only")
            @php($hp = config('banner.program_help'))
            @php($hi = $hp['image'])
            <p class="hint">One "Have any questions?" card shown beside the About section on every program detail page (optional).</p>
            <p class="hint">The "Contact Us" button is fixed and always opens the Contact page. Image: {{ $hi['recommended'] }} &middot; min {{ $hi['min_width'] }} x {{ $hi['min_height'] }} px &middot; max {{ round($hi['max_kb'] / 1024) }} MB.</p>
            <label>Card image
                <input type="file" name="help_image" accept="image/jpeg,image/png,image/webp"
                       data-max-kb="{{ $hi['max_kb'] }}" data-min-w="{{ $hi['min_width'] }}" data-min-h="{{ $hi['min_height'] }}"
                       data-max-w="{{ $hi['max_width'] }}" data-max-h="{{ $hi['max_height'] }}">
            </label>
            @if($program->help_image)
                <p class="hint">Current image (choose a new file to replace it):</p>
                <div id="helpImgWrap" style="position:relative;display:inline-block;margin-bottom:12px">
                    <img src="{{ asset($program->help_image) }}" alt="" style="display:block;max-height:90px;border-radius:6px;border:1px solid #d5dbe5">
                    <button type="button" id="helpImgRemove" title="Remove image" aria-label="Remove current image"
                            style="position:absolute;top:-9px;right:-9px;width:24px;height:24px;border:0;border-radius:50%;background:#c0392b;color:#fff;font-size:16px;line-height:24px;padding:0;cursor:pointer;box-shadow:0 1px 4px rgba(0,0,0,.35)">&times;</button>
                    <input type="hidden" name="remove_help_image" id="helpImgFlag" value="0">
                </div>
                <script>
                    document.getElementById('helpImgRemove').addEventListener('click', function () {
                        document.getElementById('helpImgFlag').value = '1';
                        document.getElementById('helpImgWrap').style.display = 'none';
                        document.getElementById('helpImgNote').style.display = 'block';
                    });
                </script>
                <p class="hint" id="helpImgNote" style="display:none;color:#c0392b">Image will be removed when you click Save contact card.</p>
            @endif
            <label>Card heading
                <input type="text" name="help_heading" value="{{ old('help_heading', $program->help_heading) }}" data-live="line" data-no-digits data-word-max="{{ $hp['word_max'] }}"
                       minlength="{{ $hp['heading_min'] }}" maxlength="{{ $hp['heading_max'] }}" placeholder="e.g. Have Any Questions?">
            </label>
            <label>Card description
                <textarea name="help_description" rows="3" data-live="line" data-no-digits data-word-max="{{ $hp['word_max'] }}"
                          minlength="{{ $hp['description_min'] }}" maxlength="{{ $hp['description_max'] }}" placeholder="{{ $hint }}">{{ old('help_description', $program->help_description) }}</textarea>
            </label>
            <div class="check end" style="margin: 8px 0 16px;">
                <span>Show contact card on website</span>
                <label class="switch">
                    <input type="checkbox" name="help_active" value="1" @checked(old('help_active', $program->help_active ?? true))>
                    <i></i>
                </label>
            </div>
            <button type="submit" class="btn">Save contact card</button>
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
                        <small class="ellipsis" style="color: var(--muted); display:block; max-width: 260px;">{{ is_numeric($card->duration) ? $card->duration . ' mo' : $card->duration }} &middot; {{ $card->mode }}</small>
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
                        <button type="button" class="act" title="Edit" data-edit-program="{{ json_encode(['id' => $card->id, 'title' => $card->title, 'features' => $card->features(), 'duration' => $card->duration, 'mode' => $card->mode, 'sort_order' => $card->sort_order, 'is_featured' => $card->is_featured, 'is_active' => $card->is_active, 'image_url' => $card->image ? asset($card->image) : null, 'about_text' => $card->about_text, 'banner_title' => $card->banner_title, 'banner_description' => $card->banner_description, 'banner_levels' => preg_match('/\d+/', (string) $card->banner_levels, $m) ? (int) $m[0] : '', 'banner_timeline' => $card->banner_timeline, 'levels' => $card->levels ?? [], 'curriculum_points' => $card->curriculum_points, 'tool_items' => collect($card->tool_items ?? [])->map(fn ($t) => ['name' => $t['name'], 'icon' => $t['icon'] ?? '', 'icon_url' => ! empty($t['icon']) ? asset($t['icon']) : ''])->all(), 'formats' => collect($card->formats ?? [])->map(fn ($f) => ['title' => $f['title'], 'description' => $f['description'] ?? '', 'icon' => $f['icon'] ?? '', 'icon_url' => ! empty($f['icon']) ? asset($f['icon']) : ''])->all(), 'banner_image_url' => $card->banner_image ? asset($card->banner_image) : null]) }}">@include('admin.partials.icon-edit')</button>
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
        const FRE = /^[A-Za-z .,'&!?-]+$/;
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
                if (!FRE.test(l)) return msg = 'Feature ' + n + ": only letters, spaces and . , ' - & ! ? are allowed (no numbers, emojis or special characters).";
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
        const blank = { title: '', features: [], duration: '', mode: '', sort_order: next, is_featured: false, is_active: true, about_text: '', banner_title: '', banner_description: '', banner_levels: '', banner_timeline: '', levels: [], curriculum_points: '', tool_items: [], formats: [] };

        // ---- shared helpers for the tool / format repeaters (each row can carry its own icon) ----
        const ICON = @json(config('banner.program_extras.icon'));
        const escA = s => String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/"/g, '&quot;');
        function iconHtml(item) {
            return '<div class="ic-field" style="margin:8px 0">'
                + '<label>Icon (optional)<input type="file" data-f="icon" accept="image/png,image/jpeg,image/webp" data-max-kb="' + ICON.max_kb + '" data-min-w="' + ICON.min_width + '" data-min-h="' + ICON.min_height + '" data-max-w="' + ICON.max_width + '" data-max-h="' + ICON.max_height + '"></label>'
                + '<input type="hidden" data-f="icon_old" value="' + escA(item.icon) + '">'
                + '<div class="ic-cur" style="position:relative;display:' + (item.icon_url ? 'inline-block' : 'none') + ';margin-top:6px">'
                + '<img src="' + escA(item.icon_url) + '" alt="" style="display:block;width:48px;height:48px;object-fit:contain;border-radius:6px;border:1px solid #d5dbe5;background:#f0f2f5">'
                + '<button type="button" class="ic-x" title="Remove icon" aria-label="Remove icon" style="position:absolute;top:-8px;right:-8px;width:22px;height:22px;border:0;border-radius:50%;background:#c0392b;color:#fff;font-size:14px;line-height:22px;padding:0;cursor:pointer">&times;</button>'
                + '</div></div>';
        }
        function bindIcon(row) {
            const file = row.querySelector('input[type=file]'), old = row.querySelector('[data-f=icon_old]'),
                  box = row.querySelector('.ic-cur'), img = box.querySelector('img');
            file.addEventListener('change', () => setTimeout(() => {
                if (file.files[0]) { img.src = URL.createObjectURL(file.files[0]); box.style.display = 'inline-block'; }
                else if (!old.value) { box.style.display = 'none'; }
            }, 0));
            file.addEventListener('rejected', () => { if (!old.value) box.style.display = 'none'; });
            box.querySelector('.ic-x').onclick = () => { old.value = ''; file.value = ''; box.style.display = 'none'; };
        }
        const rowStyle = 'border:1px solid #d5dbe5;border-radius:8px;padding:12px;margin-bottom:10px;position:relative';
        const delStyle = 'position:absolute;top:8px;right:8px;width:24px;height:24px;border:0;border-radius:50%;background:#c0392b;color:#fff;cursor:pointer;padding:0';

        // ---- tools & digital topics repeater ----
        const tlList = document.getElementById('toolsList'), addTl = document.getElementById('addToolBtn');
        function renumberTl() {
            tlList.querySelectorAll('.tl-row').forEach((row, i) => {
                row.querySelector('.tl-num').textContent = 'Tool ' + (i + 1);
                ['name', 'icon', 'icon_old'].forEach(f => { row.querySelector('[data-f=' + f + ']').name = 'tool_items[' + i + '][' + f + ']'; });
            });
            addTl.style.display = tlList.children.length >= +tlList.dataset.max ? 'none' : '';
        }
        function addTool(t = {}) {
            const d = tlList.dataset, row = document.createElement('div');
            row.className = 'tl-row'; row.style.cssText = rowStyle;
            row.innerHTML = '<strong class="tl-num"></strong>'
                + '<button type="button" class="tl-del" title="Remove" aria-label="Remove tool" style="' + delStyle + '">&times;</button>'
                + '<label>Tool name<input type="text" data-f="name" data-live="line" data-no-digits data-word-max="' + d.wordMax + '" minlength="' + d.nameMin + '" maxlength="' + d.nameMax + '" value="' + escA(t.name) + '" placeholder="e.g. CRM"></label>'
                + iconHtml(t);
            bindIcon(row);
            row.querySelector('.tl-del').onclick = () => { row.remove(); renumberTl(); };
            tlList.appendChild(row); renumberTl();
        }
        function setTools(list) { tlList.innerHTML = ''; (list || []).forEach(addTool); renumberTl(); }
        addTl.onclick = () => addTool();

        // ---- learning format repeater ----
        const fmList = document.getElementById('formatsList'), addFm = document.getElementById('addFormatBtn');
        function renumberFm() {
            fmList.querySelectorAll('.fm-row').forEach((row, i) => {
                row.querySelector('.fm-num').textContent = 'Format ' + (i + 1);
                ['title', 'description', 'icon', 'icon_old'].forEach(f => { row.querySelector('[data-f=' + f + ']').name = 'formats[' + i + '][' + f + ']'; });
            });
            addFm.style.display = fmList.children.length >= +fmList.dataset.max ? 'none' : '';
        }
        function addFormat(f = {}) {
            const d = fmList.dataset, row = document.createElement('div');
            row.className = 'fm-row'; row.style.cssText = rowStyle;
            row.innerHTML = '<strong class="fm-num"></strong>'
                + '<button type="button" class="fm-del" title="Remove" aria-label="Remove format" style="' + delStyle + '">&times;</button>'
                + '<label>Format title<input type="text" data-f="title" data-live="line" data-no-digits data-word-max="' + d.wordMax + '" minlength="' + d.titleMin + '" maxlength="' + d.titleMax + '" value="' + escA(f.title) + '" placeholder="e.g. 5 Days per Week"></label>'
                + '<label>Format description<input type="text" data-f="description" data-live="line" data-no-digits data-word-max="' + d.wordMax + '" minlength="' + d.descMin + '" maxlength="' + d.descMax + '" value="' + escA(f.description) + '" placeholder="Letters, numbers, spaces and . , \' - &amp; ! ? only"></label>'
                + iconHtml(f);
            bindIcon(row);
            const t = row.querySelector('[data-f=title]'), ds = row.querySelector('[data-f=description]');
            ds.addEventListener('input', () => { const need = ds.value.trim() !== ''; if (t.required !== need) { t.required = need; t.dispatchEvent(new Event('input', { bubbles: true })); } });
            row.querySelector('.fm-del').onclick = () => { row.remove(); renumberFm(); };
            fmList.appendChild(row); renumberFm();
        }
        function setFormats(list) { fmList.innerHTML = ''; (list || []).forEach(addFormat); renumberFm(); }
        addFm.onclick = () => addFormat();
        // ---- curriculum levels repeater ----
        const lvList = document.getElementById('levelsList'), addLv = document.getElementById('addLevelBtn');
        const esc = s => String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/"/g, '&quot;');
        function renumber() {
            lvList.querySelectorAll('.lv-row').forEach((row, i) => {
                row.querySelector('.lv-num').textContent = 'Level ' + (i + 1);
                ['title', 'period', 'points'].forEach(f => { row.querySelector('[data-f=' + f + ']').name = 'levels[' + i + '][' + f + ']'; });
            });
            addLv.style.display = lvList.children.length >= +lvList.dataset.max ? 'none' : '';
        }
        function addLevel(l = {}) {
            const row = document.createElement('div');
            row.className = 'lv-row';
            row.style.cssText = 'border:1px solid #d5dbe5;border-radius:8px;padding:12px;margin-bottom:10px;position:relative';
            row.innerHTML = '<strong class="lv-num"></strong>'
                + '<button type="button" class="lv-del" title="Remove level" aria-label="Remove level" style="position:absolute;top:8px;right:8px;width:24px;height:24px;border:0;border-radius:50%;background:#c0392b;color:#fff;cursor:pointer;padding:0">&times;</button>'
                + '<label>Level title<input type="text" data-f="title" data-live="line" data-no-digits data-word-max="' + lvList.dataset.wordMax + '" minlength="' + lvList.dataset.titleMin + '" maxlength="' + lvList.dataset.titleMax + '" value="' + esc(l.title) + '" placeholder="e.g. Business Foundations"></label>'
                + '<label>Period (month number)<input type="number" data-f="period" min="1" max="99" step="1" inputmode="numeric" value="' + esc((String(l.period || '').match(/\d+/) || [''])[0]) + '" placeholder="e.g. 1"></label>'
                + '<label>Points * (one per line)<textarea data-f="points" data-live="points" data-no-digits rows="4" data-min="' + lvList.dataset.pointMin + '" data-max="' + lvList.dataset.pointMax + '" data-max-items="' + lvList.dataset.pointsMax + '" data-word-max="' + lvList.dataset.wordMax + '" placeholder="One point per line">' + esc((l.points || []).join('\n')) + '</textarea></label>';
            row.querySelector('.lv-del').onclick = () => { row.remove(); renumber(); };
            lvList.appendChild(row); renumber();
        }
        function setLevels(list) { lvList.innerHTML = ''; (list || []).forEach(addLevel); renumber(); }
        addLv.onclick = () => addLevel();

        function setFeatures(list) {
            $f.summernote('code', list.length ? '<ul>' + list.map(x => '<li>' + $('<i>').text(x).html() + '</li>').join('') + '</ul>' : '');
            fErr.textContent = '';
            document.querySelector('#canvas-program .note-editor').style.borderColor = '';
        }

        document.getElementById('createProgramBtn').onclick = () => {
            preview.style.display = 'none';
            document.querySelector('#canvas-program .media-current-banner').innerHTML = '';
            Admin.openForm('canvas-program', { title: 'Add Program Card', data: blank, fileRequired: 'image' });
            setFeatures([]); setLevels([]); setFormats([]); setTools([]);
        };
        document.querySelectorAll('[data-edit-program]').forEach(b => b.onclick = () => {
            const d = JSON.parse(b.dataset.editProgram);
            preview.style.display = 'none';
            setFeatures(d.features); setLevels(d.levels); setFormats(d.formats); setTools(d.tool_items);
            const bn = document.querySelector('#canvas-program .media-current-banner');
            bn.innerHTML = d.banner_image_url ? `<p class="hint">Current banner (choose a new file to replace it):</p><img src="${d.banner_image_url}" alt="" style="max-height:90px;border-radius:6px;border:1px solid #d5dbe5">` : '';
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
