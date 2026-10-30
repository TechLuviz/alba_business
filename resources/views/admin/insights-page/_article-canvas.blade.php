{{-- Right-side canvas form for adding / editing an article card --}}
@php
    $c = config('banner.article');
    $img = $c['image'];
    $hint = "Letters, numbers, spaces and . , ' - & ! ? only";
@endphp

<aside class="offcanvas" id="canvas-article"
       data-store="{{ route('admin.insightpage.articles.items.store') }}"
       data-update="{{ route('admin.insightpage.articles.items.update', '__ID__') }}">
    <div class="canvas-head"><h3 class="canvas-title">Add Article</h3><button type="button" class="canvas-close" data-close>&times;</button></div>

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
                <li>The "Read article" link is optional: without one, the card has no link.</li>
            </ul>
        </div>

        <label>Article image *
            <input type="file" name="image" id="articleImage" accept="image/jpeg,image/png,image/webp"
                   data-max-kb="{{ $img['max_kb'] }}" data-min-w="{{ $img['min_width'] }}" data-min-h="{{ $img['min_height'] }}"
                   data-max-w="{{ $img['max_width'] }}" data-max-h="{{ $img['max_height'] }}">
        </label>
        <div class="preview"><img id="articleImgPreview" src="" alt="" style="display:none; max-height:140px; border-radius:8px"></div>
        <div class="media-current"></div>

        @php($bn = config('banner.insight_banner'))
        <label>Detail page banner image (optional)
            <input type="file" name="banner_image" id="articleBannerImage" accept="image/jpeg,image/png,image/webp"
                   data-max-kb="{{ $bn['max_kb'] }}" data-min-w="{{ $bn['min_width'] }}" data-min-h="{{ $bn['min_height'] }}"
                   data-max-w="{{ $bn['max_width'] }}" data-max-h="{{ $bn['max_height'] }}">
        </label>
        <p class="hint">Shown at the top of this article's own page. Recommended {{ $bn['recommended'] }}; min {{ $bn['min_width'] }} x {{ $bn['min_height'] }} px, max {{ round($bn['max_kb'] / 1024) }} MB.</p>
        <div class="preview"><img id="articleBannerPreview" src="" alt="" style="display:none; max-height:140px; border-radius:8px"></div>
        <div class="banner-current"></div>

        <div class="grid2">
            <label>Category *
                <input type="text" name="category" data-live="line" required data-word-max="{{ $c['word_max'] }}" minlength="{{ $c['category_min'] }}" maxlength="{{ $c['category_max'] }}" placeholder="e.g. Marketing">
            </label>
            <label>Date *
                <input type="date" name="published_on" data-live="date" required min="{{ $c['date_min'] }}" max="{{ now()->addYear()->toDateString() }}">
            </label>
        </div>
        <label>Title *
            <input type="text" name="title" data-live="line" required data-word-max="{{ $c['word_max'] }}" minlength="{{ $c['title_min'] }}" maxlength="{{ $c['title_max'] }}" placeholder="{{ $hint }}">
        </label>
        <label>Description *
            <textarea name="description" rows="4" data-live="line" required data-word-max="{{ $c['long_word_max'] }}" minlength="{{ $c['description_min'] }}" maxlength="{{ $c['description_max'] }}" placeholder="{{ $hint }}"></textarea>
        </label>
        <label>Author (optional)
            <input type="text" name="author" maxlength="60" placeholder="e.g. Alba Editorial Team">
        </label>

        <h4>SEO</h4>
        <label>SEO title
            <input type="text" name="seo_title" placeholder="Title shown in search results (blank uses the article title)">
        </label>
        <label>SEO description
            <textarea name="seo_description" rows="3" placeholder="Summary shown in search results (blank uses the article description)"></textarea>
        </label>

        <div class="toc-editor">
            <strong>Table of contents</strong>
            <p class="hint">Each entry is listed on the article page; clicking it shows the text with that entry's own reading time (set it, or leave it blank to calculate from the words). Letters, numbers, spaces and <code>. , ' - &amp; ! ?</code> only, no emojis or special characters.</p>
            <div id="tocRows"></div>
            <button type="button" class="btn ghost" id="addTocRow">+ Add entry</button>
        </div>

        <label>Article link (optional, https only)
            <input type="text" name="url" data-live="url" maxlength="{{ $c['url_max'] }}" placeholder="https://example.com/my-article">
        </label>
        <div class="grid2">
            <label>Position (1-{{ $c['order_max'] }})
                <input type="number" name="sort_order" min="1" max="{{ $c['order_max'] }}" value="1">
            </label>
            <label class="check end"><input type="checkbox" name="is_active" value="1" checked> Active</label>
        </div>
        <div class="check end">
            <span>Display in related articles (shown on other article detail pages)</span>
            <label class="switch"><input type="checkbox" name="show_related" value="1"><i></i></label>
        </div>
        <p class="hint">1 is shown first. Choosing a position moves the other articles down automatically, so the positions always stay 1, 2, 3...</p>

        <div class="canvas-foot">
            <button type="submit" class="btn red">Save article</button>
            <button type="button" class="btn ghost" data-close>Cancel</button>
        </div>
    </form>
</aside>
