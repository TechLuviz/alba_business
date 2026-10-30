@extends('admin.layout')

@section('title', 'Insight Articles')
@section('page_title', 'Insights Page - Insight Articles')
@section('breadcrumbs')<a href="{{ route('admin.banners.index') }}">Home</a> <span>&rsaquo;</span> Page Settings <span>&rsaquo;</span> Insights Page <span>&rsaquo;</span> Insight Articles @endsection

@section('content')
    @php($c = config('banner.articles'))
    @php($hint = "Letters, numbers, spaces and . , ' - & ! ? only")
    <p class="hint">Article cards appear on the Insights page, {{ $c['per_page'] }} per page (3 per row, 3 rows), with page numbers below once there are more than {{ $c['per_page'] }}. Add as many as you need. All fields marked * are mandatory.</p>

    <section class="card form-card" style="max-width: 100%;">
        <form method="POST" action="{{ route('admin.insightpage.articles.settings.update') }}" data-live-validate novalidate>
            @csrf
            @method('PUT')
            <h3>Section</h3>
            <label>Section label (eyebrow) *
                <input type="text" name="eyebrow" value="{{ old('eyebrow', $section->eyebrow) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                       minlength="{{ $c['eyebrow_min'] }}" maxlength="{{ $c['eyebrow_max'] }}" placeholder="{{ $hint }}">
            </label>
            <label>Heading *
                <input type="text" name="heading" value="{{ old('heading', $section->heading) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                       minlength="{{ $c['heading_min'] }}" maxlength="{{ $c['heading_max'] }}" placeholder="{{ $hint }}">
            </label>
            <div class="check end" style="margin: 8px 0 16px;">
                <span>Show this section on the Insights page</span>
                <label class="switch">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $section->is_active))>
                    <i></i>
                </label>
            </div>
            <button type="submit" class="btn">Save section</button>
        </form>
    </section>

    <section class="card panel" style="margin-top: 20px;">
        <div class="panel-head">
            <h2>Articles ({{ $articles->count() }})</h2>
            <div class="panel-tools">
                <label class="toggle-all">
                    <span class="switch">
                        <input type="checkbox" data-toggle-all="{{ route('admin.insightpage.articles.items.toggle-all') }}" data-group="items" @checked($articles->isNotEmpty() && $articles->every->is_active)>
                        <i></i>
                    </span> Toggle All
                </label>
                <button type="button" class="btn red" id="createArticleBtn">+ Add Article</button>
            </div>
        </div>

        <table class="tbl">
            <thead>
                <tr><th>S.N.</th><th>Preview</th><th>Article</th><th>Order</th><th>Status</th><th class="r">Action</th></tr>
            </thead>
            <tbody>
            @forelse($articles as $i => $a)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        @if($a->image)
                            <img class="thumb" src="{{ asset($a->image) }}" alt="" onerror="this.classList.add('missing')">
                        @else
                            <span class="thumb" style="display:inline-flex; align-items:center; justify-content:center; color:#999; font-size:.7rem; background:#f0f2f5;">No image</span>
                        @endif
                    </td>
                    <td>
                        <strong class="strong ellipsis" style="display:block;">{{ $a->title }} <span class="tag">{{ $a->category }}</span></strong>
                        <small class="ellipsis" style="color: var(--muted); display:block; max-width: 420px;">{{ $a->published_on->format('d M Y') }} &middot; {{ $a->url ?: 'No link' }}</small>
                    </td>
                    <td><span class="badge-gold">#{{ $a->sort_order }}</span></td>
                    <td>
                        <label class="switch">
                            <input type="checkbox" data-toggle="{{ route('admin.insightpage.articles.items.toggle', $a) }}" data-group="items" @checked($a->is_active)>
                            <i></i>
                        </label>
                    </td>
                    <td class="r actions">
                        <button type="button" class="act" title="View" data-view="{{ json_encode(['type' => 'image', 'src' => $a->image ? asset($a->image) : '', 'title' => $a->title, 'desc' => $a->category . ' - ' . $a->published_on->format('d M Y') . "\n\n" . $a->description]) }}">@include('admin.partials.icon-eye')</button>
                        <button type="button" class="act" title="Edit" data-edit-article="{{ json_encode(['id' => $a->slug, 'category' => $a->category, 'title' => $a->title, 'description' => $a->description, 'author' => $a->author, 'seo_title' => $a->seo_title, 'seo_description' => $a->seo_description, 'banner_url' => $a->banner_image ? asset($a->banner_image) : null, 'sections' => $a->sections->map->only(['title', 'body', 'read_minutes'])->values(), 'published_on' => $a->published_on->toDateString(), 'url' => $a->url, 'sort_order' => $a->sort_order, 'is_active' => $a->is_active, 'show_related' => $a->show_related, 'image_url' => $a->image ? asset($a->image) : null]) }}">@include('admin.partials.icon-edit')</button>
                        <form method="POST" action="{{ route('admin.insightpage.articles.items.destroy', $a) }}" data-confirm="Delete this article?">@csrf @method('DELETE')<button class="act del" title="Delete">@include('admin.partials.icon-trash')</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">No articles yet. Click "+ Add Article" to create one. The section stays hidden on the website until you add at least one.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>

    @include('admin.insights-page._article-canvas')

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
<script>
    (function () {
        const preview = document.getElementById('articleImgPreview');
        const blank = { category: '', title: '', description: '', url: '', published_on: new Date().toISOString().slice(0, 10), sort_order: {{ $articles->count() + 1 }}, is_active: true, show_related: false };

        // Table-of-contents editor: one title + text row per entry, submitted as sections[n][title|body].
        const rows = document.getElementById('tocRows');
        let rowNo = 0;
        function addRow(title = '', body = '', minutes = '') {
            const n = rowNo++;
            const row = document.createElement('div');
            row.className = 'toc-row';
            // data-live="line" turns on the same live checks as the other fields (no emojis / special characters, no repeated spaces, long words)
            row.innerHTML =
                `<label>Entry title *<input type="text" name="sections[${n}][title]" data-live="line" required data-word-max="18" minlength="3" maxlength="80" placeholder="e.g. Start with a real problem"></label>` +
                `<label>Entry text *<textarea name="sections[${n}][body]" rows="3" data-live="line" required data-word-max="24" minlength="10" maxlength="600" placeholder="Write one short paragraph"></textarea></label>` +
                `<label>Read time in minutes (optional)<input type="number" name="sections[${n}][read_minutes]" min="1" max="60" placeholder="Leave blank to calculate automatically"></label>` +
                `<button type="button" class="btn ghost toc-remove" aria-label="Remove entry">Remove</button>`;
            row.querySelector('input').value = title;
            row.querySelector('textarea').value = body;
            row.querySelector('input[type=number]').value = minutes ?? '';
            row.querySelector('.toc-remove').onclick = () => row.remove();
            rows.appendChild(row);
        }
        function setRows(list) { rows.innerHTML = ''; (list || []).forEach(s => addRow(s.title, s.body, s.read_minutes)); }
        document.getElementById('addTocRow').onclick = () => addRow();

        document.getElementById('createArticleBtn').onclick = () => {
            preview.style.display = 'none';
            Admin.openForm('canvas-article', { title: 'Add Article', data: { ...blank, author: '', seo_title: '', seo_description: '' }, fileRequired: 'image' });
            setRows([]);
        };
        document.querySelectorAll('[data-edit-article]').forEach(b => b.onclick = () => {
            const d = JSON.parse(b.dataset.editArticle);
            preview.style.display = 'none';
            Admin.openForm('canvas-article', {
                title: 'Edit Article', id: d.id, data: d,
                media: d.image_url ? `<p class="hint">Current image (choose a new file to replace it):</p><img src="${d.image_url}" alt="" style="max-height:120px;border-radius:6px;border:1px solid #d5dbe5">` : '',
            });
            setRows(d.sections);
        });

        // Detail page banner: preview of a newly chosen file, and the current banner when editing.
        const bPreview = document.getElementById('articleBannerPreview');
        const bCurrent = document.querySelector('#canvas-article .banner-current');
        const bInput = document.getElementById('articleBannerImage');
        function showBanner(url) {
            bPreview.style.display = 'none';
            bCurrent.innerHTML = url ? `<p class="hint">Current banner (choose a new file to replace it):</p><img src="${url}" alt="" style="max-height:120px;border-radius:6px;border:1px solid #d5dbe5">` : '';
        }
        bInput.addEventListener('rejected', () => { bPreview.style.display = 'none'; });
        bInput.addEventListener('change', () => setTimeout(() => {
            const f = bInput.files[0];
            if (!f) { bPreview.style.display = 'none'; return; }
            bPreview.src = URL.createObjectURL(f); bPreview.style.display = 'block';
        }, 0));
        document.getElementById('createArticleBtn').addEventListener('click', () => showBanner(''));
        document.querySelectorAll('[data-edit-article]').forEach(b => b.addEventListener('click', () => showBanner(JSON.parse(b.dataset.editArticle).banner_url)));

        const input = document.getElementById('articleImage');
        input.addEventListener('rejected', () => { preview.style.display = 'none'; });
        input.addEventListener('change', () => setTimeout(() => {
            const f = input.files[0];
            if (!f) { preview.style.display = 'none'; return; }
            preview.src = URL.createObjectURL(f); preview.style.display = 'block';
        }, 0));
    })();
</script>
@endpush
