{{-- Right-side canvas form for adding / editing a video card --}}
@php
    $c = config('banner.reel_card');
    $hint = "Letters, numbers, spaces and . , ' - & ! ? only";
    $hosts = implode(',', array_keys(\App\Models\ReelCard::PLATFORMS));
    $names = implode(', ', array_unique(array_values(\App\Models\ReelCard::PLATFORMS)));
@endphp

<aside class="offcanvas" id="canvas-reel"
       data-store="{{ route('admin.reel.cards.store') }}"
       data-update="{{ route('admin.reel.cards.update', '__ID__') }}">
    <div class="canvas-head"><h3 class="canvas-title">Add Video Card</h3><button type="button" class="canvas-close" data-close>&times;</button></div>

    <form method="POST" enctype="multipart/form-data" class="canvas-body" data-live-validate novalidate>
        @csrf
        <input type="hidden" name="_method" value="POST">
        <input type="hidden" name="_id" value="">

        <div class="spec">
            <strong>Requirements</strong>
            <ul>
                <li>Text: letters, numbers, spaces and <code>. , ' - &amp; ! ?</code> only &mdash; no emojis, symbols, leading or repeated spaces.</li>
                <li>Video link: a https link from {{ $names }}.</li>
                <li><strong>YouTube and Vimeo links play automatically (muted) inside the card.</strong> Clicking a card opens the video link in a new tab.</li>
            </ul>
        </div>

        <label>Video link (where the card redirects) *
            <input type="text" name="url" data-live="url" required maxlength="{{ $c['url_max'] }}" data-hosts="{{ $hosts }}" data-names="{{ $names }}" placeholder="https://www.youtube.com/watch?v=...">
        </label>
        <label>Category label *
            <input type="text" name="tag" data-live="line" data-no-digits required data-word-max="{{ $c['word_max'] }}" minlength="{{ $c['tag_min'] }}" maxlength="{{ $c['tag_max'] }}" placeholder="e.g. Founders">
        </label>
        <label>Card title *
            <input type="text" name="title" data-live="line" data-no-digits required data-word-max="{{ $c['word_max'] }}" minlength="{{ $c['title_min'] }}" maxlength="{{ $c['title_max'] }}" placeholder="{{ $hint }}">
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
