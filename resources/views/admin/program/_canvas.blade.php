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
            <label>Duration (months) *
                <input type="number" name="duration" required min="1" max="99" step="1" inputmode="numeric" placeholder="e.g. 12">
            </label>
            <label>Study mode *
                <input type="text" name="mode" data-live="line" required data-word-max="{{ $c['word_max'] }}" minlength="{{ $c['mode_min'] }}" maxlength="{{ $c['mode_max'] }}" placeholder="e.g. Full-time">
            </label>
        </div>
        @php($b = config('banner.program_banner'))
        @php($bi = $b['image'])
        <h4 style="margin: 18px 0 6px;">Detail page banner <small style="font-weight:400;color:#7a8594">(optional)</small></h4>
        <div class="spec">
            <ul>
                <li>Shown at the top of this program's detail page (opened from "Enroll Now").</li>
                <li>Banner image: {{ $bi['recommended'] }} &middot; min {{ $bi['min_width'] }} x {{ $bi['min_height'] }} px &middot; max {{ round($bi['max_kb'] / 1024) }} MB</li>
            </ul>
        </div>
        <label>Banner image
            <input type="file" name="banner_image" id="programBannerImage" accept="image/jpeg,image/png,image/webp"
                   data-max-kb="{{ $bi['max_kb'] }}" data-min-w="{{ $bi['min_width'] }}" data-min-h="{{ $bi['min_height'] }}"
                   data-max-w="{{ $bi['max_width'] }}" data-max-h="{{ $bi['max_height'] }}">
        </label>
        <div class="media-current-banner"></div>
        <label>Banner title
            <input type="text" name="banner_title" data-live="line" data-word-max="{{ $b['word_max'] }}" minlength="{{ $b['title_min'] }}" maxlength="{{ $b['title_max'] }}" placeholder="Leave blank to use the program title">
        </label>
        <label>Banner description
            <textarea name="banner_description" rows="3" data-live="line" data-word-max="{{ $b['word_max'] }}" minlength="{{ $b['description_min'] }}" maxlength="{{ $b['description_max'] }}" placeholder="{{ $hint }}"></textarea>
        </label>
        <div class="grid2">
            <label>Levels text
                <input type="number" name="banner_levels" min="1" max="99" step="1" inputmode="numeric" placeholder="e.g. 3">
            </label>
            <label>Timeline text
                <input type="text" name="banner_timeline" data-live="line" data-word-max="{{ $b['word_max'] }}" minlength="{{ $b['timeline_min'] }}" maxlength="{{ $b['timeline_max'] }}" placeholder="e.g. Month 1 through Months 4-5">
            </label>
        </div>

        @php($ab = config('banner.program_about'))
        <label>About this program (detail page)
            <textarea name="about_text" rows="8" data-live="line" data-word-max="{{ $ab['word_max'] }}" data-max-words="{{ $ab['max_words'] }}" minlength="{{ $ab['min'] }}" placeholder="{{ $hint }} - up to {{ $ab['max_words'] }} words"></textarea>
        </label>

        @php($lvc = config('banner.program_levels'))
        <h4 style="margin: 18px 0 6px;">Program curriculum <small style="font-weight:400;color:#7a8594">(optional, up to {{ $lvc['max_levels'] }} levels)</small></h4>
        <label>Curriculum points (for programs without levels)
            <textarea name="curriculum_points" data-live="points" rows="4" data-min="{{ $lvc['point_min'] }}" data-max="{{ $lvc['point_max'] }}" data-max-items="{{ $lvc['points_max'] }}" data-word-max="{{ $lvc['word_max'] }}" placeholder="One point per line - shown as a plain list, not as levels"></textarea>
        </label>
        <p class="hint">Add levels only if this program has them. Each level has an optional title, an optional period (month number, e.g. 1) and its points &mdash; one point per line, {{ $lvc['point_min'] }}-{{ $lvc['point_max'] }} characters each.</p>
        <div id="levelsList" data-max="{{ $lvc['max_levels'] }}" data-title-min="{{ $lvc['title_min'] }}" data-title-max="{{ $lvc['title_max'] }}" data-word-max="{{ $lvc['word_max'] }}" data-point-min="{{ $lvc['point_min'] }}" data-point-max="{{ $lvc['point_max'] }}" data-points-max="{{ $lvc['points_max'] }}"></div>
        <button type="button" class="btn ghost" id="addLevelBtn">+ Add level</button>

        @php($ex = config('banner.program_extras'))
        <h4 style="margin: 18px 0 6px;">Tools &amp; Digital Topics <small style="font-weight:400;color:#7a8594">(optional)</small></h4>
        <div id="toolsList" data-max="{{ $ex['tools_max'] }}" data-word-max="{{ $ex['word_max'] }}" data-name-min="{{ $ex['tool_min'] }}" data-name-max="{{ $ex['tool_max'] }}"></div>
        <button type="button" class="btn ghost" id="addToolBtn">+ Add tool</button>
        <p class="hint">Up to {{ $ex['tools_max'] }} tools. Icons are optional: {{ $ex['icon']['recommended'] }} &middot; min {{ $ex['icon']['min_width'] }} x {{ $ex['icon']['min_height'] }} px &middot; max {{ round($ex['icon']['max_kb'] / 1024) }} MB. Without an icon a default one is shown.</p>

        <h4 style="margin: 18px 0 6px;">Learning Format <small style="font-weight:400;color:#7a8594">(optional, up to {{ $ex['format_max'] }} cards)</small></h4>
        <div id="formatsList" data-max="{{ $ex['format_max'] }}" data-word-max="{{ $ex['word_max'] }}" data-title-min="{{ $ex['format_title_min'] }}" data-title-max="{{ $ex['format_title_max'] }}" data-desc-min="{{ $ex['format_desc_min'] }}" data-desc-max="{{ $ex['format_desc_max'] }}"></div>
        <button type="button" class="btn ghost" id="addFormatBtn">+ Add format</button>

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
