{{-- Right-side canvas form for adding / editing a Programs page highlight point --}}
@php
    $c = config('banner.program_point');
    $hint = "Letters, numbers, spaces and . , ' - & ! ? only";
@endphp

<aside class="offcanvas" id="canvas-point"
       data-store="{{ route('admin.programpage.points.store') }}"
       data-update="{{ route('admin.programpage.points.update', '__ID__') }}">
    <div class="canvas-head"><h3 class="canvas-title">Add Point</h3><button type="button" class="canvas-close" data-close>&times;</button></div>

    <form method="POST" enctype="multipart/form-data" class="canvas-body" data-live-validate novalidate>
        @csrf
        <input type="hidden" name="_method" value="POST">
        <input type="hidden" name="_id" value="">
        <input type="hidden" name="remove_icon" value="0">

        <div class="spec">
            <strong>Requirements</strong>
            <ul>
                <li>Text: letters, numbers, spaces and <code>. , ' - &amp; ! ?</code> only &mdash; no emojis, symbols, leading or repeated spaces.</li>
                <li>Icon: {{ $c['icon_recommended'] }} &middot; max {{ $c['icon_max_kb'] }} KB</li>
            </ul>
        </div>

        <label>Icon
            <input type="file" name="icon" id="pointIcon" accept="image/png,image/jpeg,image/webp,image/svg+xml" data-max-kb="{{ $c['icon_max_kb'] }}">
        </label>
        <div class="preview"><img id="pointIconPreview" src="" alt="" style="display:none; max-height:64px; border-radius:8px"></div>
        <div class="media-current"></div>

        <label>Title *
            <input type="text" name="title" data-live="line" data-no-digits required data-word-max="{{ $c['word_max'] }}" minlength="{{ $c['title_min'] }}" maxlength="{{ $c['title_max'] }}" placeholder="{{ $hint }}">
        </label>
        <label>Short description
            <input type="text" name="description" data-live="line" data-no-digits data-word-max="{{ $c['word_max'] }}" minlength="{{ $c['description_min'] }}" maxlength="{{ $c['description_max'] }}" placeholder="{{ $hint }}">
        </label>
        <div class="grid2">
            <label>Display order (0-{{ $c['order_max'] }})
                <input type="number" name="sort_order" min="0" max="{{ $c['order_max'] }}" value="0">
            </label>
            <label class="check end"><input type="checkbox" name="is_active" value="1" checked> Active</label>
        </div>

        <div class="canvas-foot">
            <button type="submit" class="btn red">Save point</button>
            <button type="button" class="btn ghost" data-close>Cancel</button>
        </div>
    </form>
</aside>
