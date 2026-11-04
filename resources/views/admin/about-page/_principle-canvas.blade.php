{{-- Right-side canvas form for adding / editing a principle --}}
@php
    $c = config('banner.principle');
    $hint = "Letters, numbers, spaces and . , ' - & ! ? only";
@endphp

<aside class="offcanvas" id="canvas-principle"
       data-store=""
       data-update="{{ route('admin.aboutpage.principles.items.update', '__ID__') }}">
    <div class="canvas-head"><h3 class="canvas-title">Edit Principle</h3><button type="button" class="canvas-close" data-close>&times;</button></div>

    <form method="POST" class="canvas-body" data-live-validate novalidate>
        @csrf
        <input type="hidden" name="_method" value="POST">
        <input type="hidden" name="_id" value="">

        <div class="spec">
            <strong>Requirements</strong>
            <ul>
                <li>Text: letters, numbers, spaces and <code>. , ' - &amp; ! ?</code> only &mdash; no emojis, symbols, leading or repeated spaces.</li>
                <li>The heading is shown in two lines: a dark line, then the gold italic accent line.</li>
            </ul>
        </div>

        <label>Label *
            <input type="text" name="label" data-live="line" data-no-digits required data-word-max="{{ $c['word_max'] }}" minlength="{{ $c['label_min'] }}" maxlength="{{ $c['label_max'] }}" placeholder="e.g. Our Mission">
        </label>
        <label>Heading (dark line) *
            <input type="text" name="heading" data-live="line" data-no-digits required data-word-max="{{ $c['word_max'] }}" minlength="{{ $c['heading_min'] }}" maxlength="{{ $c['heading_max'] }}" placeholder="{{ $hint }}">
        </label>
        <label>Heading accent (gold italic line) *
            <input type="text" name="heading_accent" data-live="line" data-no-digits required data-word-max="{{ $c['word_max'] }}" minlength="{{ $c['accent_min'] }}" maxlength="{{ $c['accent_max'] }}" placeholder="{{ $hint }}">
        </label>
        <label>Description *
            <textarea name="description" rows="4" data-live="line" data-no-digits required data-word-max="{{ $c['long_word_max'] }}" minlength="{{ $c['description_min'] }}" maxlength="{{ $c['description_max'] }}" placeholder="{{ $hint }}"></textarea>
        </label>

        <div class="canvas-foot">
            <button type="submit" class="btn red">Save principle</button>
            <button type="button" class="btn ghost" data-close>Cancel</button>
        </div>
    </form>
</aside>
