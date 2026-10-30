@extends('admin.layout')

@section('title', 'Insights Section')
@section('page_title', 'Insights Section')
@section('breadcrumbs')<a href="{{ route('admin.banners.index') }}">Home</a> <span>&rsaquo;</span> Page Settings <span>&rsaquo;</span> Home Page <span>&rsaquo;</span> Insights Section @endsection

@section('content')
    @php($c = config('banner.insight'))
    @php($hint = "Letters, numbers, spaces and . , ' - & ! ? only")
    <p class="hint">Edit the section text and add as many insight cards as you need. All fields marked * are mandatory.</p>

    <section class="card form-card" style="max-width: 100%;">
        <form method="POST" action="{{ route('admin.insight.settings.update') }}" data-live-validate novalidate>
            @csrf
            @method('PUT')
            <h3>Section content</h3>
            <label>Section label (eyebrow) *
                <input type="text" name="eyebrow" value="{{ old('eyebrow', $insight->eyebrow) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                       minlength="{{ $c['eyebrow_min'] }}" maxlength="{{ $c['eyebrow_max'] }}" placeholder="{{ $hint }}">
            </label>
            <label>Heading (white part) *
                <input type="text" name="heading" value="{{ old('heading', $insight->heading) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                       minlength="{{ $c['heading_min'] }}" maxlength="{{ $c['heading_max'] }}" placeholder="{{ $hint }}">
            </label>
            <label>Heading accent (gold italic part) *
                <input type="text" name="heading_accent" value="{{ old('heading_accent', $insight->heading_accent) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                       minlength="{{ $c['accent_min'] }}" maxlength="{{ $c['accent_max'] }}" placeholder="{{ $hint }}">
            </label>
            <label>Description *
                <textarea name="description" rows="3" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                          minlength="{{ $c['description_min'] }}" maxlength="{{ $c['description_max'] }}" placeholder="{{ $hint }}">{{ old('description', $insight->description) }}</textarea>
            </label>
            <div class="check end" style="margin: 8px 0 16px;">
                <span>Show section on website</span>
                <label class="switch">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $insight->is_active))>
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
                        <input type="checkbox" data-toggle-all="{{ route('admin.insight.cards.toggle-all') }}" data-group="cards" @checked($cards->isNotEmpty() && $cards->every->is_active)>
                        <i></i>
                    </span> Toggle All
                </label>
                <button type="button" class="btn red" id="createInsightBtn">+ Add Card</button>
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
                        <strong class="strong ellipsis" style="display:block;">{{ $card->title }}</strong>
                        <small class="ellipsis" style="color: var(--muted); display:block; max-width: 260px;">{{ $card->description }}</small>
                    </td>
                    <td><span class="badge-gold">#{{ $card->sort_order }}</span></td>
                    <td>
                        <label class="switch">
                            <input type="checkbox" data-toggle="{{ route('admin.insight.cards.toggle', $card) }}" data-group="cards" @checked($card->is_active)>
                            <i></i>
                        </label>
                    </td>
                    <td class="r actions">
                        <button type="button" class="act" title="View" data-view="{{ json_encode(['type' => 'image', 'src' => $card->image ? asset($card->image) : '', 'title' => $card->title, 'desc' => $card->description]) }}">@include('admin.partials.icon-eye')</button>
                        <button type="button" class="act" title="Edit" data-edit-insight="{{ json_encode(['id' => $card->id, 'title' => $card->title, 'description' => $card->description, 'sort_order' => $card->sort_order, 'is_active' => $card->is_active, 'image_url' => $card->image ? asset($card->image) : null]) }}">@include('admin.partials.icon-edit')</button>
                        <form method="POST" action="{{ route('admin.insight.cards.destroy', $card) }}" data-confirm="Delete this insight card?">@csrf @method('DELETE')<button class="act del" title="Delete">@include('admin.partials.icon-trash')</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">No insight cards yet. Click "+ Add Card" to create one.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>

    @include('admin.insight._canvas')

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
        const preview = document.getElementById('insightImgPreview');
        const blank = { title: '', description: '', sort_order: next, is_active: true };

        document.getElementById('createInsightBtn').onclick = () => {
            preview.style.display = 'none';
            Admin.openForm('canvas-insight', { title: 'Add Insight Card', data: blank, fileRequired: 'image' });
        };
        document.querySelectorAll('[data-edit-insight]').forEach(b => b.onclick = () => {
            const d = JSON.parse(b.dataset.editInsight);
            preview.style.display = 'none';
            Admin.openForm('canvas-insight', {
                title: 'Edit Insight Card', id: d.id, data: d,
                media: d.image_url ? `<p class="hint">Current image (choose a new file to replace it):</p><img src="${d.image_url}" alt="" style="max-height:120px;border-radius:6px;border:1px solid #d5dbe5">` : '',
            });
        });

        const input = document.getElementById('insightImage');
        input.addEventListener('rejected', () => { preview.style.display = 'none'; });
        input.addEventListener('change', () => setTimeout(() => {
            const f = input.files[0];
            if (!f) { preview.style.display = 'none'; return; }
            preview.src = URL.createObjectURL(f); preview.style.display = 'block';
        }, 0));
    })();
</script>
@endpush
