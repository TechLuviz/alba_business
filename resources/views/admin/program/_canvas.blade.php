{{-- Right-side canvas form for creating / editing a program card --}}
@php
    $c = config('banner.program_card');
    $img = $c['image'];
    $hint = "Letters, numbers, spaces and . , ' - & ! ? only";
@endphp

<aside class="offcanvas" id="canvas-program"
       data-store="{{ route('admin.program.cards.store') }}"
       data-update="{{ route('admin.program.cards.update', '__ID__') }}">
    <div class="canvas-head"><h3 class="canvas-title">Add Program Card</h3><button type="button" class="canvas-close" data-close>&times;</button></div>

    <form method="POST" enctype="multipart/form-data" class="canvas-body" data-live-validate novalidate>
        @csrf
        <input type="hidden" name="_method" value="POST">
        <input type="hidden" name="_id" value="">

        <div class="spec">
            <strong>Requirements</strong>
            <ul>
                <li>Text: letters, numbers, spaces and <code>. , ' - &amp; ! ?</code> only &mdash; no emojis, symbols, leading or repeated spaces.</li>
                <li>Image: {{ $img['recommended'] }}</li>
                <li>Min {{ $img['min_width'] }} x {{ $img['min_height'] }} px &middot; max {{ $img['max_width'] }} x {{ $img['max_height'] }} px &middot; max {{ round($img['max_kb'] / 1024) }} MB</li>
            </ul>
        </div>

        <label>Program image *
            <input type="file" name="image" id="programImage" accept="image/jpeg,image/png,image/webp"
                   data-max-kb="{{ $img['max_kb'] }}" data-min-w="{{ $img['min_width'] }}" data-min-h="{{ $img['min_height'] }}"
                   data-max-w="{{ $img['max_width'] }}" data-max-h="{{ $img['max_height'] }}">
        </label>
        <div class="preview"><img id="programImgPreview" src="" alt="" style="display:none; max-height:140px; border-radius:8px"></div>
        <div class="media-current"></div>

        <label>Program title *
            <input type="text" name="title" data-live="line" required data-word-max="{{ $c['word_max'] }}" minlength="{{ $c['title_min'] }}" maxlength="{{ $c['title_max'] }}" placeholder="{{ $hint }}">
        </label>
        <div class="rich-field">
            <span class="rich-label">Features * <em>(one bullet per feature, up to {{ $c['features_max_items'] }})</em></span>
            <textarea name="features" id="programFeatures" data-min="{{ $c['feature_min'] }}" data-max="{{ $c['feature_max'] }}"
                      data-max-items="{{ $c['features_max_items'] }}" data-word-max="{{ $c['word_max'] }}"></textarea>
            <small class="field-error" id="featuresError"></small>
            <small class="hint">Each bullet: {{ $c['feature_min'] }}-{{ $c['feature_max'] }} characters, letters, numbers, spaces and . , ' - &amp; ! ? only.</small>
        </div>
        <div class="grid2">
            <label>Duration *
                <input type="text" name="duration" data-live="line" required data-word-max="{{ $c['word_max'] }}" minlength="{{ $c['duration_min'] }}" maxlength="{{ $c['duration_max'] }}" placeholder="e.g. 12 Months">
            </label>
            <label>Study mode *
                <input type="text" name="mode" data-live="line" required data-word-max="{{ $c['word_max'] }}" minlength="{{ $c['mode_min'] }}" maxlength="{{ $c['mode_max'] }}" placeholder="e.g. Full-time">
            </label>
        </div>
        <div class="grid2">
            <label>Display order (0-{{ $c['order_max'] }})
                <input type="number" name="sort_order" min="0" max="{{ $c['order_max'] }}" value="0">
            </label>
            <div class="check end">
                <label class="check"><input type="checkbox" name="is_featured" value="1"> Featured (gold highlight)</label>
            </div>
        </div>
        <label class="check"><input type="checkbox" name="is_active" value="1" checked> Active</label>

        <div class="canvas-foot">
            <button type="submit" class="btn red">Save program</button>
            <button type="button" class="btn ghost" data-close>Cancel</button>
        </div>
    </form>
</aside>
