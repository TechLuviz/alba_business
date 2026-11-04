{{-- Right-side canvas form for adding / editing a team member --}}
@php
    $c = config('banner.team_member');
    $img = $c['image'];
    $hint = "Letters, numbers, spaces and . , ' - & ! ? only";
@endphp

<aside class="offcanvas" id="canvas-team"
       data-store="{{ route('admin.team.members.store') }}"
       data-update="{{ route('admin.team.members.update', '__ID__') }}">
    <div class="canvas-head"><h3 class="canvas-title">Add Team Member</h3><button type="button" class="canvas-close" data-close>&times;</button></div>

    <form method="POST" enctype="multipart/form-data" class="canvas-body" data-live-validate novalidate>
        @csrf
        <input type="hidden" name="_method" value="POST">
        <input type="hidden" name="_id" value="">

        <div class="spec">
            <strong>Requirements</strong>
            <ul>
                <li>Text: letters, numbers, spaces and <code>. , ' - &amp; ! ?</code> only &mdash; no emojis, symbols, leading or repeated spaces.</li>
                <li>Photo: {{ $img['recommended'] }}</li>
                <li>Min {{ $img['min_width'] }} x {{ $img['min_height'] }} px &middot; max {{ $img['max_width'] }} x {{ $img['max_height'] }} px &middot; max {{ round($img['max_kb'] / 1024) }} MB</li>
            </ul>
        </div>

        <label>Member photo *
            <input type="file" name="image" id="teamImage" accept="image/jpeg,image/png,image/webp"
                   data-max-kb="{{ $img['max_kb'] }}" data-min-w="{{ $img['min_width'] }}" data-min-h="{{ $img['min_height'] }}"
                   data-max-w="{{ $img['max_width'] }}" data-max-h="{{ $img['max_height'] }}">
        </label>
        <div class="preview"><img id="teamImgPreview" src="" alt="" style="display:none; max-height:140px; border-radius:8px"></div>
        <div class="media-current"></div>

        <label>Member name *
            <input type="text" name="name" data-live="line" data-no-digits required data-word-max="{{ $c['word_max'] }}" minlength="{{ $c['name_min'] }}" maxlength="{{ $c['name_max'] }}" placeholder="{{ $hint }}">
        </label>
        <label>Designation *
            <input type="text" name="designation" data-live="line" data-no-digits required data-word-max="{{ $c['word_max'] }}" minlength="{{ $c['designation_min'] }}" maxlength="{{ $c['designation_max'] }}" placeholder="{{ $hint }}">
        </label>
        <div class="grid2">
            <label>Display order (0-{{ $c['order_max'] }})
                <input type="number" name="sort_order" min="0" max="{{ $c['order_max'] }}" value="0">
            </label>
            <label class="check end"><input type="checkbox" name="is_active" value="1" checked> Active</label>
        </div>

        <div class="canvas-foot">
            <button type="submit" class="btn red">Save member</button>
            <button type="button" class="btn ghost" data-close>Cancel</button>
        </div>
    </form>
</aside>
