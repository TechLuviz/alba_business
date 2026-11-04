@extends('admin.layout')

@section('title', 'Banner Settings')
@section('page_title', 'Home Page Banner Settings')
@section('breadcrumbs')<a href="{{ route('admin.banners.index') }}">Home</a> <span>&rsaquo;</span> Banner Settings @endsection

@section('content')
    <p class="hint">If a video banner is active, the home page plays it as the background. Otherwise the active dynamic banners rotate as a carousel.</p>

    <div class="split">
        {{-- ============ Dynamic banners ============ --}}
        <section class="card panel">
            <div class="panel-head">
                <h2><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#e5321b" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="9" cy="9" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg> Dynamic Banners</h2>
                <div class="panel-tools">
                    <label class="toggle-all"><span class="switch"><input type="checkbox" data-toggle-all="{{ route('admin.banners.dynamic.toggle-all') }}" data-group="dynamic" @checked($slides->isNotEmpty() && $slides->every->is_active)><i></i></span> Toggle All</label>
                    <button type="button" class="btn red" id="createDynamic">+ Create Dynamic</button>
                </div>
            </div>
            <table class="tbl">
                <thead><tr><th>S.N.</th><th>Preview</th><th>Banner Title</th><th>Active Status</th><th class="r">Action</th></tr></thead>
                <tbody>
                @forelse($slides as $i => $slide)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><img class="thumb" src="{{ asset($slide->image) }}" alt="" onerror="this.classList.add('missing')"></td>
                        <td class="strong ellipsis">{{ trim($slide->heading_line_1 . ' ' . $slide->heading_line_2) ?: 'Untitled Dynamic Banner' }}</td>
                        <td><label class="switch"><input type="checkbox" data-toggle="{{ route('admin.banners.dynamic.toggle', $slide) }}" data-group="dynamic" @checked($slide->is_active)><i></i></label></td>
                        <td class="r actions">
                            <button type="button" class="act" title="View" data-view="{{ json_encode(['type' => 'image', 'src' => asset($slide->image), 'title' => trim($slide->heading_line_1 . ' ' . $slide->heading_line_2 . ' ' . $slide->heading_accent), 'desc' => $slide->description]) }}">@include('admin.partials.icon-eye')</button>
                            <button type="button" class="act" title="Edit" data-edit-dynamic="{{ json_encode(['id' => $slide->id, 'heading_line_1' => $slide->heading_line_1, 'heading_line_2' => $slide->heading_line_2, 'heading_accent' => $slide->heading_accent, 'description' => $slide->description, 'sort_order' => $slide->sort_order, 'is_active' => $slide->is_active, 'media' => asset($slide->image)]) }}">@include('admin.partials.icon-edit')</button>
                            <form method="POST" action="{{ route('admin.banners.dynamic.destroy', $slide) }}" data-confirm="Delete this banner?">@csrf @method('DELETE')<button class="act del" title="Delete">@include('admin.partials.icon-trash')</button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">No dynamic banners yet.</td></tr>
                @endforelse
                </tbody>
            </table>
            <form method="POST" action="{{ route('admin.banners.interval') }}" class="panel-foot">
                @csrf
                <label>Seconds between slides (2-30) <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="2" autocomplete="off" name="autoplay_interval" id="autoplay_interval" value="{{ $interval }}" required></label>
                <button class="btn small">Save</button>
                <small id="autoplay_interval_error" class="field-error" style="display:none;color:#d93025;width:100%"></small>
            </form>
            <script>
            (function () {
                var input = document.getElementById('autoplay_interval');
                var err = document.getElementById('autoplay_interval_error');
                var form = input.closest('form');
                function show(msg) { err.textContent = msg; err.style.display = msg ? 'block' : 'none'; input.style.borderColor = msg ? '#d93025' : ''; }
                function check() {
                    var v = input.value;
                    if (v === '') { show('Please enter a whole number between 2 and 30.'); return false; }
                    var n = parseInt(v, 10);
                    if (n < 2 || n > 30) { show('Value must be between 2 and 30.'); return false; }
                    show(''); return true;
                }
                input.addEventListener('keydown', function (e) {
                    if (e.ctrlKey || e.metaKey || e.altKey || e.key.length > 1) return; // allow navigation/editing keys
                    if (!/^[0-9]$/.test(e.key)) { e.preventDefault(); show('Only whole numbers are allowed (no decimals, letters or symbols).'); }
                });
                input.addEventListener('input', function () {
                    var cleaned = input.value.replace(/\D/g, '');
                    if (cleaned !== input.value) { input.value = cleaned; show('Only whole numbers are allowed (no decimals, letters or symbols).'); }
                    else if (input.value !== '') check(); else show('');
                });
                form.addEventListener('submit', function (e) { if (!check()) e.preventDefault(); });
            })();
            </script>
        </section>

        {{-- ============ Video banners ============ --}}
        <section class="card panel">
            <div class="panel-head">
                <h2><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7a2a8a" stroke-width="2"><rect x="2" y="6" width="14" height="12" rx="2"/><path d="M16 10l6-3v10l-6-3z"/></svg> Video Banners</h2>
                <div class="panel-tools">
                    <button type="button" class="btn red" id="createVideo">+ Create Video</button>
                </div>
            </div>
            <table class="tbl">
                <thead><tr><th>S.N.</th><th>Preview</th><th>Video Source</th><th>Active Status</th><th class="r">Action</th></tr></thead>
                <tbody>
                @forelse($videos as $i => $video)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><span class="vid-thumb">@include('admin.partials.icon-video')</span></td>
                        <td class="strong ellipsis">{{ basename($video->video) }} <span class="tag">Video File</span></td>
                        <td><label class="switch"><input type="checkbox" data-toggle="{{ route('admin.banners.video.toggle', $video) }}" data-group="video" @checked($video->is_active)><i></i></label></td>
                        <td class="r actions">
                            <button type="button" class="act" title="View" data-view="{{ json_encode(['type' => 'video', 'src' => asset($video->video), 'title' => trim($video->heading_line_1 . ' ' . $video->heading_line_2 . ' ' . $video->heading_accent), 'desc' => $video->description]) }}">@include('admin.partials.icon-eye')</button>
                            <button type="button" class="act" title="Edit" data-edit-video="{{ json_encode(['id' => $video->id, 'heading_line_1' => $video->heading_line_1, 'heading_line_2' => $video->heading_line_2, 'heading_accent' => $video->heading_accent, 'description' => $video->description, 'sort_order' => $video->sort_order, 'is_active' => $video->is_active, 'media' => basename($video->video)]) }}">@include('admin.partials.icon-edit')</button>
                            <form method="POST" action="{{ route('admin.banners.video.destroy', $video) }}" data-confirm="Delete this video banner?">@csrf @method('DELETE')<button class="act del" title="Delete">@include('admin.partials.icon-trash')</button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">No video banners yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </section>
    </div>

    @include('admin.banners._canvas', ['id' => 'dynamic', 'title' => 'Create Dynamic Banner'])
    @include('admin.banners._canvas', ['id' => 'video', 'title' => 'Create Video Banner'])

    {{-- ============ Preview modal ============ --}}
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
    const D = {
        nextDynamic: {{ ($slides->max('sort_order') ?? 0) + 1 }},
        nextVideo: {{ ($videos->max('sort_order') ?? 0) + 1 }},
        reopen: @json(old('_panel') && $errors->any() ? ['panel' => old('_panel'), 'id' => old('_id')] : null),
    };

    const blank = n => ({ heading_line_1: '', heading_line_2: '', heading_accent: '', description: '', sort_order: n, is_active: false });

    document.getElementById('createDynamic').onclick = () => Admin.openForm('canvas-dynamic', { title: 'Create Dynamic Banner', data: { ...blank(D.nextDynamic), is_active: true }, fileRequired: 'image' });
    document.getElementById('createVideo').onclick = () => Admin.openForm('canvas-video', { title: 'Create Video Banner', data: blank(D.nextVideo), fileRequired: 'video' });

    document.querySelectorAll('[data-edit-dynamic]').forEach(b => b.onclick = () => {
        const d = JSON.parse(b.dataset.editDynamic);
        Admin.openForm('canvas-dynamic', { title: 'Edit Dynamic Banner', id: d.id, data: d, media: `<img src="${d.media}" alt="">` });
    });
    document.querySelectorAll('[data-edit-video]').forEach(b => b.onclick = () => {
        const d = JSON.parse(b.dataset.editVideo);
        Admin.openForm('canvas-video', { title: 'Edit Video Banner', id: d.id, data: d, media: `<p class="hint">Current file: <strong>${d.media}</strong> (choose a new file to replace it)</p>` });
    });

    // re-open the right canvas after a validation error so entered values are not lost
    if (D.reopen) {
        Admin.openForm('canvas-' + D.reopen.panel, {
            title: (D.reopen.id ? 'Edit ' : 'Create ') + (D.reopen.panel === 'video' ? 'Video Banner' : 'Dynamic Banner'),
            id: D.reopen.id || null, keepValues: true,
            fileRequired: D.reopen.id ? null : (D.reopen.panel === 'video' ? 'video' : 'image'),
        });
    }
</script>
@endpush