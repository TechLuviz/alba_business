@extends('admin.layout')

@section('title', 'Video Section')
@section('page_title', 'Video Section')
@section('breadcrumbs')<a href="{{ route('admin.banners.index') }}">Home</a> <span>&rsaquo;</span> Page Settings <span>&rsaquo;</span> Home Page <span>&rsaquo;</span> Video Section @endsection

@section('content')
    @php($c = config('banner.reel'))
    @php($hint = "Letters, numbers, spaces and . , ' - & ! ? only")
    <p class="hint">Each card plays its YouTube or Vimeo video automatically and sends visitors to the full video when clicked. Add as many cards as you need. All fields marked * are mandatory.</p>

    <section class="card form-card" style="max-width: 100%;">
        <form method="POST" action="{{ route('admin.reel.settings.update') }}" data-live-validate novalidate>
            @csrf
            @method('PUT')
            <h3>Section content</h3>
            <label>Section label (eyebrow) *
                <input type="text" name="eyebrow" value="{{ old('eyebrow', $reel->eyebrow) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                       minlength="{{ $c['eyebrow_min'] }}" maxlength="{{ $c['eyebrow_max'] }}" placeholder="{{ $hint }}">
            </label>
            <label>Heading *
                <input type="text" name="heading" value="{{ old('heading', $reel->heading) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                       minlength="{{ $c['heading_min'] }}" maxlength="{{ $c['heading_max'] }}" placeholder="{{ $hint }}">
            </label>
            <label>Heading accent (gold italic line) *
                <input type="text" name="heading_accent" value="{{ old('heading_accent', $reel->heading_accent) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                       minlength="{{ $c['accent_min'] }}" maxlength="{{ $c['accent_max'] }}" placeholder="{{ $hint }}">
            </label>
            <label>Description *
                <textarea name="description" rows="3" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                          minlength="{{ $c['description_min'] }}" maxlength="{{ $c['description_max'] }}" placeholder="{{ $hint }}">{{ old('description', $reel->description) }}</textarea>
            </label>
            <div class="check end" style="margin: 8px 0 16px;">
                <span>Show section on website</span>
                <label class="switch">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $reel->is_active))>
                    <i></i>
                </label>
            </div>
            <button type="submit" class="btn">Save section</button>
        </form>
    </section>

    <section class="card panel" style="margin-top: 20px;">
        <div class="panel-head">
            <h2>Video Cards ({{ $cards->count() }})</h2>
            <div class="panel-tools">
                <label class="toggle-all">
                    <span class="switch">
                        <input type="checkbox" data-toggle-all="{{ route('admin.reel.cards.toggle-all') }}" data-group="cards" @checked($cards->isNotEmpty() && $cards->every->is_active)>
                        <i></i>
                    </span> Toggle All
                </label>
                <button type="button" class="btn red" id="createReelBtn">+ Add Video</button>
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
                    <td><span class="vid-thumb">@include('admin.partials.icon-video')</span></td>
                    <td>
                        <strong class="strong ellipsis" style="display:block;">{{ $card->title }} <span class="tag">{{ $card->tag }}</span></strong>
                        <small class="ellipsis" style="color: var(--muted); display:block; max-width: 260px;">{{ $card->platform() ?? 'Link' }} &middot; {{ $card->url }}</small>
                    </td>
                    <td><span class="badge-gold">#{{ $card->sort_order }}</span></td>
                    <td>
                        <label class="switch">
                            <input type="checkbox" data-toggle="{{ route('admin.reel.cards.toggle', $card) }}" data-group="cards" @checked($card->is_active)>
                            <i></i>
                        </label>
                    </td>
                    <td class="r actions">
                        <button type="button" class="act" title="View" data-view="{{ json_encode(['type' => 'video', 'src' => '', 'title' => $card->title, 'desc' => $card->tag . ' - ' . $card->url]) }}">@include('admin.partials.icon-eye')</button>
                        <button type="button" class="act" title="Edit" data-edit-reel="{{ json_encode(['id' => $card->id, 'tag' => $card->tag, 'title' => $card->title, 'url' => $card->url, 'sort_order' => $card->sort_order, 'is_active' => $card->is_active]) }}">@include('admin.partials.icon-edit')</button>
                        <form method="POST" action="{{ route('admin.reel.cards.destroy', $card) }}" data-confirm="Delete this video card?">@csrf @method('DELETE')<button class="act del" title="Delete">@include('admin.partials.icon-trash')</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">No video cards yet. Click "+ Add Video" to create one. The section stays hidden on the website until you add at least one.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>

    @include('admin.reel._canvas')

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
        const blank = { tag: '', title: '', url: '', sort_order: next, is_active: true };

        document.getElementById('createReelBtn').onclick = () =>
            Admin.openForm('canvas-reel', { title: 'Add Video Card', data: blank });

        document.querySelectorAll('[data-edit-reel]').forEach(b => b.onclick = () => {
            const d = JSON.parse(b.dataset.editReel);
            Admin.openForm('canvas-reel', {
                title: 'Edit Video Card', id: d.id, data: d,
            });
        });
    })();
</script>
@endpush
