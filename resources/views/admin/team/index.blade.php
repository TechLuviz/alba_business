@extends('admin.layout')

@section('title', 'Team Section')
@section('page_title', 'Team Section')
@section('breadcrumbs')<a href="{{ route('admin.banners.index') }}">Home</a> <span>&rsaquo;</span> Page Settings <span>&rsaquo;</span> Home Page <span>&rsaquo;</span> Team Section @endsection

@section('content')
    @php($c = config('banner.team'))
    @php($hint = "Letters, numbers, spaces and . , ' - & ! ? only")
    <p class="hint">Edit the section heading and add as many team members as you need. All fields marked * are mandatory.</p>

    <section class="card form-card" style="max-width: 100%;">
        <form method="POST" action="{{ route('admin.team.settings.update') }}" data-live-validate novalidate>
            @csrf
            @method('PUT')
            <h3>Section content</h3>
            <label>Section label (eyebrow) *
                <input type="text" name="eyebrow" value="{{ old('eyebrow', $team->eyebrow) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                       minlength="{{ $c['eyebrow_min'] }}" maxlength="{{ $c['eyebrow_max'] }}" placeholder="{{ $hint }}">
            </label>
            <label>Heading (white part) *
                <input type="text" name="heading" value="{{ old('heading', $team->heading) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                       minlength="{{ $c['heading_min'] }}" maxlength="{{ $c['heading_max'] }}" placeholder="{{ $hint }}">
            </label>
            <label>Heading accent (gold part) *
                <input type="text" name="heading_accent" value="{{ old('heading_accent', $team->heading_accent) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                       minlength="{{ $c['accent_min'] }}" maxlength="{{ $c['accent_max'] }}" placeholder="{{ $hint }}">
            </label>
            <label>Description *
                <textarea name="description" rows="3" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                          minlength="{{ $c['description_min'] }}" maxlength="{{ $c['description_max'] }}" placeholder="{{ $hint }}">{{ old('description', $team->description) }}</textarea>
            </label>
            <div class="check end" style="margin: 8px 0 16px;">
                <span>Show section on website</span>
                <label class="switch">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $team->is_active))>
                    <i></i>
                </label>
            </div>
            <button type="submit" class="btn">Save section</button>
        </form>
    </section>

    <section class="card panel" style="margin-top: 20px;">
        <div class="panel-head">
            <h2>Team Members ({{ $members->count() }})</h2>
            <div class="panel-tools">
                <label class="toggle-all">
                    <span class="switch">
                        <input type="checkbox" data-toggle-all="{{ route('admin.team.members.toggle-all') }}" data-group="members" @checked($members->isNotEmpty() && $members->every->is_active)>
                        <i></i>
                    </span> Toggle All
                </label>
                <button type="button" class="btn red" id="createMemberBtn">+ Add Member</button>
            </div>
        </div>

        <table class="tbl">
            <thead>
                <tr><th>S.N.</th><th>Photo</th><th>Member</th><th>Order</th><th>Status</th><th class="r">Action</th></tr>
            </thead>
            <tbody>
            @forelse($members as $i => $member)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        @if($member->image)
                            <img class="thumb" src="{{ asset($member->image) }}" alt="" onerror="this.classList.add('missing')">
                        @else
                            <span class="thumb" style="display:inline-flex; align-items:center; justify-content:center; color:#999; font-size:.7rem; background:#f0f2f5;">No photo</span>
                        @endif
                    </td>
                    <td>
                        <strong class="strong ellipsis" style="display:block;">{{ $member->name }}</strong>
                        <small class="ellipsis" style="color: var(--muted); display:block; max-width: 260px;">{{ $member->designation }}</small>
                    </td>
                    <td><span class="badge-gold">#{{ $member->sort_order }}</span></td>
                    <td>
                        <label class="switch">
                            <input type="checkbox" data-toggle="{{ route('admin.team.members.toggle', $member) }}" data-group="members" @checked($member->is_active)>
                            <i></i>
                        </label>
                    </td>
                    <td class="r actions">
                        <button type="button" class="act" title="View" data-view="{{ json_encode(['type' => 'image', 'src' => $member->image ? asset($member->image) : '', 'title' => $member->name, 'desc' => $member->designation]) }}">@include('admin.partials.icon-eye')</button>
                        <button type="button" class="act" title="Edit" data-edit-member="{{ json_encode(['id' => $member->id, 'name' => $member->name, 'designation' => $member->designation, 'sort_order' => $member->sort_order, 'is_active' => $member->is_active, 'image_url' => $member->image ? asset($member->image) : null]) }}">@include('admin.partials.icon-edit')</button>
                        <form method="POST" action="{{ route('admin.team.members.destroy', $member) }}" data-confirm="Delete this team member?">@csrf @method('DELETE')<button class="act del" title="Delete">@include('admin.partials.icon-trash')</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">No team members yet. Click "+ Add Member" to create one.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>

    @include('admin.team._canvas')

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
        const next = {{ ($members->max('sort_order') ?? 0) + 1 }};
        const preview = document.getElementById('teamImgPreview');
        const blank = { name: '', designation: '', sort_order: next, is_active: true };

        document.getElementById('createMemberBtn').onclick = () => {
            preview.style.display = 'none';
            Admin.openForm('canvas-team', { title: 'Add Team Member', data: blank, fileRequired: 'image' });
        };
        document.querySelectorAll('[data-edit-member]').forEach(b => b.onclick = () => {
            const d = JSON.parse(b.dataset.editMember);
            preview.style.display = 'none';
            Admin.openForm('canvas-team', {
                title: 'Edit Team Member', id: d.id, data: d,
                media: d.image_url ? `<p class="hint">Current photo (choose a new file to replace it):</p><img src="${d.image_url}" alt="" style="max-height:120px;border-radius:6px;border:1px solid #d5dbe5">` : '',
            });
        });

        const input = document.getElementById('teamImage');
        input.addEventListener('rejected', () => { preview.style.display = 'none'; });
        input.addEventListener('change', () => setTimeout(() => {
            const f = input.files[0];
            if (!f) { preview.style.display = 'none'; return; }
            preview.src = URL.createObjectURL(f); preview.style.display = 'block';
        }, 0));
    })();
</script>
@endpush
