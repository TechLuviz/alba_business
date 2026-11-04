@extends('admin.layout')

@section('title', 'Programs Highlight Points')
@section('page_title', 'Programs Page - Highlight Points')
@section('breadcrumbs')<a href="{{ route('admin.banners.index') }}">Home</a> <span>&rsaquo;</span> Page Settings <span>&rsaquo;</span> Programs Page <span>&rsaquo;</span> Highlight Points @endsection

@section('content')
    <p class="hint">These points appear in a row right after the banner on the Programs page. Upload an icon for each one. The programs below them are listed automatically from <strong>Programs Section</strong> under Home Page; the section heading is edited there too.</p>

    <section class="card panel">
        <div class="panel-head">
            <h2>Points ({{ $points->count() }})</h2>
            <div class="panel-tools">
                <label class="toggle-all">
                    <span class="switch">
                        <input type="checkbox" data-toggle-all="{{ route('admin.programpage.points.toggle-all') }}" data-group="points" @checked($points->isNotEmpty() && $points->every->is_active)>
                        <i></i>
                    </span> Toggle All
                </label>
                <button type="button" class="btn red" id="createPointBtn">+ Add Point</button>
            </div>
        </div>

        <table class="tbl">
            <thead>
                <tr><th>S.N.</th><th>Icon</th><th>Point</th><th>Order</th><th>Status</th><th class="r">Action</th></tr>
            </thead>
            <tbody>
            @forelse($points as $i => $point)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        @if($point->icon)
                            <img class="thumb" src="{{ asset($point->icon) }}" alt="" style="object-fit:contain; background:#f0f2f5; padding:6px;" onerror="this.classList.add('missing')">
                        @else
                            <span class="thumb" style="display:inline-flex; align-items:center; justify-content:center; color:#999; font-size:.7rem; background:#f0f2f5;">No icon</span>
                        @endif
                    </td>
                    <td>
                        <strong class="strong ellipsis" style="display:block;">{{ $point->title }}</strong>
                        <small class="ellipsis" style="color: var(--muted); display:block; max-width: 320px;">{{ $point->description }}</small>
                    </td>
                    <td><span class="badge-gold">#{{ $point->sort_order }}</span></td>
                    <td>
                        <label class="switch">
                            <input type="checkbox" data-toggle="{{ route('admin.programpage.points.toggle', $point) }}" data-group="points" @checked($point->is_active)>
                            <i></i>
                        </label>
                    </td>
                    <td class="r actions">
                        <button type="button" class="act" title="Edit" data-edit-point="{{ json_encode(['id' => $point->id, 'title' => $point->title, 'description' => $point->description, 'sort_order' => $point->sort_order, 'is_active' => $point->is_active, 'icon_url' => $point->icon ? asset($point->icon) : null]) }}">@include('admin.partials.icon-edit')</button>
                        <form method="POST" action="{{ route('admin.programpage.points.destroy', $point) }}" data-confirm="Delete this point?">@csrf @method('DELETE')<button class="act del" title="Delete">@include('admin.partials.icon-trash')</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">No points yet. Click "+ Add Point" to create one. The row stays hidden on the website until you add at least one.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>

    @include('admin.programs-page._point-canvas')
@endsection

@push('scripts')
<script>
    (function () {
        const next = {{ ($points->max('sort_order') ?? 0) + 1 }};
        const preview = document.getElementById('pointIconPreview');
        const form = document.querySelector('#canvas-point form');
        const input = document.getElementById('pointIcon');

        document.getElementById('createPointBtn').onclick = () => {
            preview.style.display = 'none';
            Admin.openForm('canvas-point', { title: 'Add Point', data: { title: '', description: '', sort_order: next, is_active: true, remove_icon: '0' } });
        };
        document.querySelectorAll('[data-edit-point]').forEach(b => b.onclick = () => {
            const d = JSON.parse(b.dataset.editPoint);
            preview.style.display = 'none';
            Admin.openForm('canvas-point', {
                title: 'Edit Point', id: d.id, data: Object.assign({ remove_icon: '0' }, d),
                media: d.icon_url
                    ? '<p class="hint">Current icon (choose a new file to replace it):</p>'
                      + '<span class="icon-current" style="position:relative;display:inline-block">'
                      + '<img src="' + d.icon_url + '" alt="" style="max-height:64px;border-radius:6px;border:1px solid #d5dbe5;background:#f0f2f5;padding:6px">'
                      + '<button type="button" class="icon-remove" aria-label="Remove icon" title="Remove icon" style="position:absolute;top:-8px;right:-8px;width:22px;height:22px;border-radius:50%;border:0;background:#c0392b;color:#fff;cursor:pointer;line-height:1">&times;</button>'
                      + '</span>'
                    : '',
            });
        });

        // The "x" on the current icon removes it when the form is saved
        form.addEventListener('click', e => {
            const x = e.target.closest('.icon-remove');
            if (!x) return;
            form.elements['remove_icon'].value = '1';
            x.closest('.icon-current').remove();
        });

        input.addEventListener('rejected', () => { preview.style.display = 'none'; });
        input.addEventListener('change', () => setTimeout(() => {
            const f = input.files[0];
            if (!f) { preview.style.display = 'none'; return; }
            form.elements['remove_icon'].value = '0';
            preview.src = URL.createObjectURL(f); preview.style.display = 'block';
        }, 0));
    })();
</script>
@endpush
