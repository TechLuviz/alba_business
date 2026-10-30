@extends('admin.layout')

@section('title', 'Target Audience Section')
@section('page_title', 'Target Audience Section')
@section('breadcrumbs')
    <a href="{{ route('admin.banners.index') }}">Home</a>
    <span>&rsaquo;</span> Page Settings
    <span>&rsaquo;</span> Home Page
    <span>&rsaquo;</span> Target Audience Section
@endsection

@section('content')
    <p class="hint">Manage the Target Audience section appearance, typography, colors, autoplay behavior, and dynamic audience cards shown on the public homepage.</p>

    <div class="split" style="grid-template-columns: 1.15fr 1fr; align-items: start;">

        {{-- ==================== 1. Main Section Settings ==================== --}}
        <section class="card form-card" style="max-width: 100%;">
            <form method="POST" action="{{ route('admin.audience.settings.update') }}">
                @csrf
                @method('PUT')

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <h2 style="font-size: 1.15rem; font-weight: 700;">Section Settings &amp; Appearance</h2>
                    <div class="check">
                        <span style="font-weight: 600; font-size: .85rem;">Section Active</span>
                        <label class="switch">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $audience->is_active))>
                            <i></i>
                        </label>
                    </div>
                </div>

                <h3 style="font-size: .95rem; color: var(--muted); border-bottom: 1px solid var(--line); padding-bottom: 6px; margin: 10px 0 14px;">Typography &amp; Content</h3>

                <div class="spec" style="margin-bottom: 14px;">
                    <strong>Text requirements:</strong> English letters, numbers, spaces and standard punctuation (<code>&amp; , . ' " - ? ! ( ) : ; /</code>) only &mdash; emojis, unicode and special symbols are strictly prevented.
                </div>

                <label>Section Eyebrow Text
                    <input type="text" name="eyebrow" value="{{ old('eyebrow', $audience->eyebrow) }}" maxlength="{{ config('banner.audience.eyebrow_max') }}" placeholder="OUR EXPECTED AUDIENCE" data-clean-text>
                </label>

                <label>Main Heading *
                    <input type="text" name="heading" value="{{ old('heading', $audience->heading) }}" required minlength="{{ config('banner.audience.heading_min') }}" maxlength="{{ config('banner.audience.heading_max') }}" placeholder="Who Is Alba Business School For?" data-clean-text>
                </label>

                <label>Highlighted Heading Text (Optional)
                    <input type="text" name="highlight_text" value="{{ old('highlight_text', $audience->highlight_text) }}" maxlength="{{ config('banner.audience.highlight_max') }}" placeholder="e.g. Alba Business School" data-clean-text>
                    <small style="color: var(--muted); font-weight: 400; font-size: .75rem;">Text matching this phrase inside the main heading will be styled in italic accent font.</small>
                </label>

                <label>Supporting Description
                    <textarea name="description" rows="3" minlength="{{ config('banner.audience.description_min') }}" maxlength="{{ config('banner.audience.description_max') }}" placeholder="Built for ambitious minds ready to learn, build, lead, and create meaningful impact." data-clean-text>{{ old('description', $audience->description) }}</textarea>
                </label>

                <div class="grid2">
                    <label>Heading Alignment
                        <select name="heading_align" style="padding: 10px 12px; border: 1px solid #cbd3df; border-radius: 6px; font: inherit;">
                            @foreach(['left' => 'Left Aligned (Recommended)', 'center' => 'Center Aligned', 'right' => 'Right Aligned'] as $val => $txt)
                                <option value="{{ $val }}" @selected(old('heading_align', $audience->heading_align) === $val)>{{ $txt }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label>Accent Line Color
                        <div class="color-input-wrap">
                            <input type="color" class="color-swatch" value="{{ old('accent_line_color', $audience->accent_line_color ?? '#C8914A') }}" data-sync-hex="accent_line_color">
                            <input type="text" name="accent_line_color" id="accent_line_color" value="{{ old('accent_line_color', $audience->accent_line_color ?? '#C8914A') }}" pattern="^#([A-Fa-f0-9]{3,8})$" required>
                        </div>
                    </label>
                </div>

                <h3 style="font-size: .95rem; color: var(--muted); border-bottom: 1px solid var(--line); padding-bottom: 6px; margin: 18px 0 14px;">Color Theme</h3>

                <div class="grid2">
                    <label>Background Color
                        <div class="color-input-wrap">
                            <input type="color" class="color-swatch" value="{{ old('bg_color', $audience->bg_color ?? '#FBF9F5') }}" data-sync-hex="bg_color">
                            <input type="text" name="bg_color" id="bg_color" value="{{ old('bg_color', $audience->bg_color ?? '#FBF9F5') }}" pattern="^#([A-Fa-f0-9]{3,8})$" required>
                        </div>
                    </label>

                    <label>Heading Color
                        <div class="color-input-wrap">
                            <input type="color" class="color-swatch" value="{{ old('heading_color', $audience->heading_color ?? '#0A1628') }}" data-sync-hex="heading_color">
                            <input type="text" name="heading_color" id="heading_color" value="{{ old('heading_color', $audience->heading_color ?? '#0A1628') }}" pattern="^#([A-Fa-f0-9]{3,8})$" required>
                        </div>
                    </label>

                    <label>Highlight Accent Color
                        <div class="color-input-wrap">
                            <input type="color" class="color-swatch" value="{{ old('highlight_color', $audience->highlight_color ?? '#C8914A') }}" data-sync-hex="highlight_color">
                            <input type="text" name="highlight_color" id="highlight_color" value="{{ old('highlight_color', $audience->highlight_color ?? '#C8914A') }}" pattern="^#([A-Fa-f0-9]{3,8})$" required>
                        </div>
                    </label>

                    <label>Description Text Color
                        <div class="color-input-wrap">
                            <input type="color" class="color-swatch" value="{{ old('description_color', $audience->description_color ?? '#525866') }}" data-sync-hex="description_color">
                            <input type="text" name="description_color" id="description_color" value="{{ old('description_color', $audience->description_color ?? '#525866') }}" pattern="^#([A-Fa-f0-9]{3,8})$" required>
                        </div>
                    </label>
                </div>

                <h3 style="font-size: .95rem; color: var(--muted); border-bottom: 1px solid var(--line); padding-bottom: 6px; margin: 18px 0 14px;">Spacing &amp; Carousel Behavior</h3>

                <div class="grid2">
                    <label>Top Spacing (px)
                        <input type="number" name="padding_top" min="0" max="240" value="{{ old('padding_top', $audience->padding_top ?? 90) }}" required>
                    </label>

                    <label>Bottom Spacing (px)
                        <input type="number" name="padding_bottom" min="0" max="240" value="{{ old('padding_bottom', $audience->padding_bottom ?? 90) }}" required>
                    </label>
                </div>

                <div class="grid2" style="margin-top: 10px;">
                    <div class="check end">
                        <span>Autoplay Carousel</span>
                        <label class="switch">
                            <input type="checkbox" name="autoplay" value="1" @checked(old('autoplay', $audience->autoplay))>
                            <i></i>
                        </label>
                    </div>

                    <label>Autoplay Interval (2 - 30 seconds)
                        <input type="number" name="autoplay_interval" min="2" max="30" value="{{ old('autoplay_interval', $audience->autoplay_interval ?? 5) }}" required>
                    </label>
                </div>

                <div style="margin-top: 20px;">
                    <button type="submit" class="btn red">Save Section Settings</button>
                </div>
            </form>
        </section>

        {{-- ==================== 2. Dynamic Audience Cards ==================== --}}
        <section class="card panel">
            <div class="panel-head">
                <h2>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#e5321b" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M7 7h10M7 12h10M7 17h6"/></svg>
                    Audience Cards ({{ $cards->count() }})
                </h2>
                <div class="panel-tools">
                    <label class="toggle-all">
                        <span class="switch">
                            <input type="checkbox" data-toggle-all="{{ route('admin.audience.cards.toggle-all') }}" data-group="cards" @checked($cards->isNotEmpty() && $cards->every->is_active)>
                            <i></i>
                        </span> Toggle All
                    </label>
                    <button type="button" class="btn red" id="createCardBtn">+ Add Card</button>
                </div>
            </div>

            <table class="tbl">
                <thead>
                    <tr>
                        <th>S.N.</th>
                        <th>Preview</th>
                        <th>Audience Title</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th class="r">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($cards as $i => $card)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            @if($card->image)
                                <img class="thumb" src="{{ asset($card->image) }}" alt="{{ $card->image_alt }}" onerror="this.classList.add('missing')">
                            @else
                                <span class="thumb" style="display:inline-flex; align-items:center; justify-content:center; color:#999; font-size:.7rem; background:#f0f2f5;">No image</span>
                            @endif
                        </td>
                        <td>
                            <strong class="strong ellipsis" style="display:block;">{{ $card->title }}</strong>
                            <small class="ellipsis" style="color: var(--muted); display:block; max-width: 200px;">{{ $card->description ?: 'No description' }}</small>
                        </td>
                        <td><span class="badge-gold">#{{ $card->sort_order }}</span></td>
                        <td>
                            <label class="switch">
                                <input type="checkbox" data-toggle="{{ route('admin.audience.cards.toggle', $card) }}" data-group="cards" @checked($card->is_active)>
                                <i></i>
                            </label>
                        </td>
                        <td class="r actions">
                            <button type="button" class="act" title="View Preview"
                                    data-view="{{ json_encode(['type' => 'image', 'src' => $card->image ? asset($card->image) : '', 'title' => $card->title, 'desc' => $card->description]) }}">
                                @include('admin.partials.icon-eye')
                            </button>
                            <button type="button" class="act" title="Edit Card"
                                    data-edit-card="{{ json_encode(['id' => $card->id, 'title' => $card->title, 'description' => $card->description, 'image_alt' => $card->image_alt, 'sort_order' => $card->sort_order, 'is_active' => $card->is_active, 'image_url' => $card->image ? asset($card->image) : null]) }}">
                                @include('admin.partials.icon-edit')
                            </button>
                            <form method="POST" action="{{ route('admin.audience.cards.destroy', $card) }}" data-confirm="Delete this audience card?">
                                @csrf
                                @method('DELETE')
                                <button class="act del" title="Delete">@include('admin.partials.icon-trash')</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty">No audience cards created yet. Click "+ Add Card" to create one.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </section>
    </div>

    {{-- Offcanvas Drawer --}}
    @include('admin.audience._canvas')

    {{-- Preview Modal --}}
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
        // Two-way synchronization between HTML5 color inputs and hex text inputs
        document.querySelectorAll('[data-sync-hex]').forEach(colorInput => {
            const targetId = colorInput.dataset.syncHex;
            const textInput = document.getElementById(targetId);
            if (!textInput) return;

            colorInput.addEventListener('input', () => { textInput.value = colorInput.value.toUpperCase(); });
            textInput.addEventListener('input', () => {
                if (/^#[0-9A-Fa-f]{6}$/.test(textInput.value)) {
                    colorInput.value = textInput.value;
                }
            });
        });

        // Next sort order calculation
        const nextOrder = {{ ($cards->max('sort_order') ?? 0) + 1 }};

        // Open create drawer
        const createBtn = document.getElementById('createCardBtn');
        if (createBtn) {
            createBtn.onclick = () => {
                const previewWrap = document.getElementById('newImagePreviewWrap');
                if (previewWrap) previewWrap.style.display = 'none';
                document.getElementById('cardImageError').textContent = '';

                Admin.openForm('canvas-card', {
                    title: 'Create Audience Card',
                    data: {
                        title: '',
                        description: '',
                        image_alt: '',
                        sort_order: nextOrder,
                        is_active: true
                    },
                    media: '',
                    fileRequired: 'image'
                });
                document.getElementById('imgReqStar').style.display = 'inline';
            };
        }

        // Open edit drawer
        document.querySelectorAll('[data-edit-card]').forEach(b => {
            b.onclick = () => {
                const d = JSON.parse(b.dataset.editCard);
                const previewWrap = document.getElementById('newImagePreviewWrap');
                if (previewWrap) previewWrap.style.display = 'none';
                document.getElementById('cardImageError').textContent = '';

                let mediaHtml = '';
                if (d.image_url) {
                    mediaHtml = `
                        <div style="margin-top: 8px;">
                            <p style="font-size: .78rem; color: var(--muted); margin-bottom: 4px;">Current card image:</p>
                            <img src="${d.image_url}" alt="" style="max-height: 120px; border-radius: 6px; display: block; border: 1px solid #d5dbe5; object-fit: cover;">
                            <label class="check" style="margin-top: 6px;">
                                <input type="checkbox" name="remove_image" value="1"> Remove image
                            </label>
                        </div>
                    `;
                }

                Admin.openForm('canvas-card', {
                    title: 'Edit Audience Card: ' + d.title,
                    id: d.id,
                    data: d,
                    media: mediaHtml,
                    fileRequired: null
                });
                document.getElementById('imgReqStar').style.display = d.image_url ? 'none' : 'inline';
            };
        });

        // Instant preview for chosen file inside drawer
        const cardImgInput = document.getElementById('cardImageInput');
        const newImgPreview = document.getElementById('newImagePreview');
        const newImgPreviewWrap = document.getElementById('newImagePreviewWrap');
        const cardImgErr = document.getElementById('cardImageError');

        if (cardImgInput) {
            cardImgInput.addEventListener('change', () => {
                cardImgErr.textContent = '';
                const file = cardImgInput.files[0];
                if (!file) {
                    if (newImgPreviewWrap) newImgPreviewWrap.style.display = 'none';
                    return;
                }

                if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                    cardImgErr.textContent = 'Only JPG, JPEG, PNG or WEBP images are allowed.';
                    cardImgInput.value = '';
                    if (newImgPreviewWrap) newImgPreviewWrap.style.display = 'none';
                    return;
                }

                const maxKb = parseInt(cardImgInput.dataset.maxKb, 10);
                if (file.size > maxKb * 1024) {
                    cardImgErr.textContent = 'Image cannot exceed ' + Math.round(maxKb / 1024) + ' MB.';
                    cardImgInput.value = '';
                    if (newImgPreviewWrap) newImgPreviewWrap.style.display = 'none';
                    return;
                }

                if (newImgPreview) {
                    newImgPreview.src = URL.createObjectURL(file);
                    newImgPreviewWrap.style.display = 'block';
                }
            });
        }

        // Real-time emoji, unicode, and special character prevention
        const disallowedPattern = /[^A-Za-z0-9\s&,.'"\-?!():;\/]/g;

        function sanitizeCleanFields(scope = document) {
            scope.querySelectorAll('[data-clean-text]').forEach(el => {
                const clean = () => {
                    if (disallowedPattern.test(el.value)) {
                        el.value = el.value.replace(disallowedPattern, '');
                        if (typeof el.counterUpdate === 'function') el.counterUpdate();
                    }
                };

                el.addEventListener('input', clean);
                el.addEventListener('paste', () => setTimeout(clean, 10));
                el.addEventListener('keyup', clean);
                el.addEventListener('blur', () => {
                    el.value = el.value.trim();
                    clean();
                });
            });
        }

        sanitizeCleanFields(document);
    })();
</script>
@endpush
