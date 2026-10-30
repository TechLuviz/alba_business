{{-- Right-side canvas form for adding / editing a featured insight card --}}
@php
    $c = config('banner.featured_card');
    $img = $c['image'];
    $hint = "Letters, numbers, spaces and . , ' - & ! ? only";
@endphp

<aside class="offcanvas" id="canvas-featured"
       data-store="{{ route('admin.insightpage.featured.cards.store') }}"
       data-update="{{ route('admin.insightpage.featured.cards.update', '__ID__') }}">
    <div class="canvas-head"><h3 class="canvas-title">Add Insight Card</h3><button type="button" class="canvas-close" data-close>&times;</button></div>

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

        <label>Card image *
            <input type="file" name="image" id="featuredImage" accept="image/jpeg,image/png,image/webp"
                   data-max-kb="{{ $img['max_kb'] }}" data-min-w="{{ $img['min_width'] }}" data-min-h="{{ $img['min_height'] }}"
                   data-max-w="{{ $img['max_width'] }}" data-max-h="{{ $img['max_height'] }}">
        </label>
        <div class="preview"><img id="featuredImgPreview" src="" alt="" style="display:none; max-height:140px; border-radius:8px"></div>
        <div class="media-current"></div>

        <div class="grid2">
            <label>Category *
                <input type="text" name="category" data-live="line" required data-word-max="{{ $c['word_max'] }}" minlength="{{ $c['category_min'] }}" maxlength="{{ $c['category_max'] }}" placeholder="e.g. Strategy">
            </label>
            <label>Date *
                <input type="date" name="published_on" data-live="date" required min="{{ $c['date_min'] }}" max="{{ now()->addYear()->toDateString() }}">
            </label>
        </div>
        <label>Title *
            <input type="text" name="title" data-live="line" required data-word-max="{{ $c['word_max'] }}" minlength="{{ $c['title_min'] }}" maxlength="{{ $c['title_max'] }}" placeholder="{{ $hint }}">
        </label>
        <div class="grid2">
            <label>Display order (0-{{ $c['order_max'] }})
                <input type="number" name="sort_order" min="0" max="{{ $c['order_max'] }}" value="0">
            </label>
            <label class="check end"><input type="checkbox" name="is_active" value="1" checked> Active</label>
        </div>

        <div class="canvas-foot">
            <button type="submit" class="btn red">Save card</button>
            <button type="button" class="btn ghost" data-close>Cancel</button>
        </div>
    </form>
</aside>
