{{-- Offcanvas Drawer for Creating / Editing Audience Cards --}}
@php
    $c = config('banner.audience_card');
    $img = $c['image'];
@endphp

<aside class="offcanvas" id="canvas-card"
       data-store="{{ route('admin.audience.cards.store') }}"
       data-update="{{ route('admin.audience.cards.update', '__ID__') }}">
    <div class="canvas-head">
        <h3 class="canvas-title">Create Audience Card</h3>
        <button type="button" class="canvas-close" data-close>&times;</button>
    </div>

    <form method="POST" enctype="multipart/form-data" class="canvas-body" novalidate id="cardForm">
        @csrf
        <input type="hidden" name="_method" value="POST">
        <input type="hidden" name="_id" value="">

        <div class="spec">
            <strong>Card Requirements</strong>
            <ul>
                <li>English letters, numbers, spaces and <code>&amp; , . ' " - ? ! ( ) : ; /</code> only &mdash; no emojis, unicode, or special characters.</li>
                <li>Recommended: {{ $img['recommended'] }}</li>
                <li>Dimensions: Min {{ $img['min_width'] }} x {{ $img['min_height'] }} px &middot; Max {{ $img['max_width'] }} x {{ $img['max_height'] }} px</li>
                <li>Formats: JPG, JPEG, PNG, or WEBP &middot; Max {{ round($img['max_kb'] / 1024) }} MB</li>
            </ul>
        </div>

        <label>Audience Title *
            <input type="text" name="title" required minlength="{{ $c['title_min'] }}" maxlength="{{ $c['title_max'] }}" placeholder="e.g. Future Students" data-clean-text>
        </label>

        <label>Audience Description
            <textarea name="description" rows="3" maxlength="{{ $c['description_max'] }}" placeholder="Short summary of this audience group..." data-clean-text></textarea>
        </label>

        <label>Card Image <span id="imgReqStar">*</span>
            <input type="file" name="image" id="cardImageInput" accept="image/jpeg,image/png,image/webp"
                   data-max-kb="{{ $img['max_kb'] }}"
                   data-min-w="{{ $img['min_width'] }}"
                   data-min-h="{{ $img['min_height'] }}"
                   data-max-w="{{ $img['max_width'] }}"
                   data-max-h="{{ $img['max_height'] }}">
            <span class="field-error" id="cardImageError"></span>
        </label>

        {{-- Live preview for newly selected file --}}
        <div class="preview" id="newImagePreviewWrap" style="display:none;">
            <p style="font-size: .78rem; color: var(--muted); margin-bottom: 4px;">New image preview:</p>
            <img id="newImagePreview" src="" alt="Selected image preview" style="max-height: 140px; border-radius: 8px; object-fit: cover;">
        </div>

        {{-- Container for current image and removal checkbox when editing --}}
        <div class="media-current"></div>

        <label>Image Alt Text (Accessibility)
            <input type="text" name="image_alt" maxlength="{{ $c['alt_max'] }}" placeholder="Describe the image for screen readers" data-clean-text>
        </label>

        <div class="grid2">
            <label>Display Order (0-{{ $c['order_max'] }})
                <input type="number" name="sort_order" min="0" max="{{ $c['order_max'] }}" value="0">
            </label>

            <div class="check end">
                <span>Card Active</span>
                <label class="switch">
                    <input type="checkbox" name="is_active" value="1" checked>
                    <i></i>
                </label>
            </div>
        </div>

        <div class="canvas-foot">
            <button type="submit" class="btn red">Save Card</button>
            <button type="button" class="btn ghost" data-close>Cancel</button>
        </div>
    </form>
</aside>
