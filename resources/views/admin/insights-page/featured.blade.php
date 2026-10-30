@extends('admin.layout')

@section('title', 'Featured Insights')
@section('page_title', 'Insights Page - Featured Insights')
@section('breadcrumbs')<a href="{{ route('admin.banners.index') }}">Home</a> <span>&rsaquo;</span> Page Settings <span>&rsaquo;</span> Insights Page <span>&rsaquo;</span> Featured Insights @endsection

@section('content')
    @php($c = config('banner.featured'))
    @php($hint = "Letters, numbers, spaces and . , ' - & ! ? only")
    <p class="hint">These cards scroll to the left without stopping on the Insights page. Add as many as you need. All fields marked * are mandatory.</p>

    <section class="card form-card" style="max-width: 100%;">
        <form method="POST" action="{{ route('admin.insightpage.featured.settings.update') }}" data-live-validate novalidate>
            @csrf
            @method('PUT')
            <h3>Section</h3>
            <label>Section label (eyebrow) *
                <input type="text" name="eyebrow" value="{{ old('eyebrow', $section->eyebrow) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                       minlength="{{ $c['eyebrow_min'] }}" maxlength="{{ $c['eyebrow_max'] }}" placeholder="{{ $hint }}">
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
            <h2>Insight Cards ({{ $cards->count() }})</h2>
            <div class="panel-tools">
                <label class="toggle-all">
                    <span class="switch">
                        <input type="checkbox" data-toggle-all="{{ route('admin.insightpage.featured.cards.toggle-all') }}" data-group="cards" @checked($cards->isNotEmpty() && $cards->every->is_active)>
                        <i></i>
                    </span> Toggle All
                </label>
                <button type="button" class="btn red" id="createFeaturedBtn">+ Add Card</button>
            </div>
        </div>

        <table class="tbl">
            <thead>
                <tr><th>S.N.</th><th>Preview</th><th>Card</th><th>Order</th><th>Status</th><th class="r">Action</th></tr>
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
                        <strong class="strong ellipsis" style="display:block;">{{ $card->title }} <span class="tag">{{ $card->category }}</span></strong>
                        <small class="ellipsis" style="color: var(--muted); display:block; max-width: 320px;">{{ $card->published_on->format('d M Y') }}</small>
                    </td>
                    <td><span class="badge-gold">#{{ $card->sort_order }}</span></td>
                    <td>
                        <label class="switch">
                            <input type="checkbox" data-toggle="{{ route('admin.insightpage.featured.cards.toggle', $card) }}" data-group="cards" @checked($card->is_active)>
                            <i></i>
                        </label>
                    </td>
                    <td class="r actions">
                        <button type="button" class="act" title="View" data-view="{{ json_encode(['type' => 'image', 'src' => $card->image ? asset($card->image) : '', 'title' => $card->title, 'desc' => $card->category . ' - ' . $card->published_on->format('d M Y')]) }}">@include('admin.partials.icon-eye')</button>
                        <button type="button" class="act" title="Edit" data-edit-featured="{{ json_encode(['id' => $card->id, 'category' => $card->category, 'title' => $card->title, 'published_on' => $card->published_on->toDateString(), 'sort_order' => $card->sort_order, 'is_active' => $card->is_active, 'image_url' => $card->image ? asset($card->image) : null]) }}">@include('admin.partials.icon-edit')</button>
                        <form method="POST" action="{{ route('admin.insightpage.featured.cards.destroy', $card) }}" data-confirm="Delete this insight card?">@csrf @method('DELETE')<button class="act del" title="Delete">@include('admin.partials.icon-trash')</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">No cards yet. Click "+ Add Card" to create one. The section stays hidden on the website until you add at least one.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>

    @include('admin.insights-page._featured-canvas')

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
        const next = {{ ($cards->max('sort_order') ?? 0) + 1 }};
        const preview = document.getElementById('featuredImgPreview');
        const blank = { category: '', title: '', published_on: new Date().toISOString().slice(0, 10), sort_order: next, is_active: true };

        document.getElementById('createFeaturedBtn').onclick = () => {
            preview.style.display = 'none';
            Admin.openForm('canvas-featured', { title: 'Add Insight Card', data: blank, fileRequired: 'image' });
        };
        document.querySelectorAll('[data-edit-featured]').forEach(b => b.onclick = () => {
            const d = JSON.parse(b.dataset.editFeatured);
            preview.style.display = 'none';
            Admin.openForm('canvas-featured', {
                title: 'Edit Insight Card', id: d.id, data: d,
                media: d.image_url ? `<p class="hint">Current image (choose a new file to replace it):</p><img src="${d.image_url}" alt="" style="max-height:120px;border-radius:6px;border:1px solid #d5dbe5">` : '',
            });
        });

        const input = document.getElementById('featuredImage');
        input.addEventListener('rejected', () => { preview.style.display = 'none'; });
        input.addEventListener('change', () => setTimeout(() => {
            const f = input.files[0];
            if (!f) { preview.style.display = 'none'; return; }
            preview.src = URL.createObjectURL(f); preview.style.display = 'block';
        }, 0));
    })();
</script>
@endpush
