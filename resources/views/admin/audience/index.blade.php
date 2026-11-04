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
    <p class="hint">Manage the Target Audience section text, autoplay behavior, and dynamic audience cards shown on the public homepage.</p>

    <div class="split" style="grid-template-columns: minmax(0, 1.15fr) minmax(0, 1fr); align-items: start;">

        {{-- ==================== 1. Main Section Settings ==================== --}}
        <section class="card form-card" style="max-width: 100%;">
            <form method="POST" action="{{ route('admin.audience.settings.update') }}">
                @csrf
                @method('PUT')

                <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 8px;">
                    <h2 style="font-size: 1.15rem; font-weight: 700;">Section Settings &amp; Appearance</h2>
                    <div class="check" style="white-space: nowrap;">
                        <span style="font-weight: 600; font-size: .85rem;">Section Active</span>
                        <label class="switch">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $audience->is_active))>
                            <i></i>
                        </label>
                    </div>
                </div>

                <h3 style="font-size: .95rem; color: var(--muted); border-bottom: 1px solid var(--line); padding-bottom: 6px; margin: 6px 0 10px;">Typography &amp; Content</h3>

                <div class="spec" style="margin-bottom: 10px; padding: 8px 12px; font-size: .78rem;">
                    <strong>Text:</strong> letters, spaces and <code>. , ' - &amp; ! ?</code> only &mdash; no numbers, emojis or symbols.
                </div>

                <label>Section Eyebrow Text
                    <input type="text" name="eyebrow" value="{{ old('eyebrow', $audience->eyebrow) }}" maxlength="{{ config('banner.audience.eyebrow_max') }}" placeholder="OUR EXPECTED AUDIENCE" data-clean-text>
                </label>

                <label>Main Heading *
                    <input type="text" name="heading" value="{{ old('heading', $audience->heading) }}" required minlength="{{ config('banner.audience.heading_min') }}" maxlength="{{ config('banner.audience.heading_max') }}" placeholder="Who Is Alba Business School For?" data-clean-text>
                </label>

                <label>Highlighted Heading Text (Optional)
                    <input type="text" name="highlight_text" value="{{ old('highlight_text', $audience->highlight_text) }}" maxlength="{{ config('banner.audience.highlight_max') }}" placeholder="e.g. Alba Business School" data-clean-text>
                    <small style="color: var(--muted); font-weight: 400; font-size: .72rem;">Shown in italic accent font where it appears in the main heading.</small>
                </label>

                <label>Supporting Description
                    <textarea name="description" rows="2" minlength="{{ config('banner.audience.description_min') }}" maxlength="{{ config('banner.audience.description_max') }}" placeholder="Built for ambitious minds ready to learn, build, lead, and create meaningful impact." data-clean-text>{{ old('description', $audience->description) }}</textarea>
                </label>

                <h3 style="font-size: .95rem; color: var(--muted); border-bottom: 1px solid var(--line); padding-bottom: 6px; margin: 10px 0 8px;">Carousel Behavior</h3>

                <div class="grid2" style="margin-top: 0; align-items: end;">
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

                <div style="margin-top: 12px;">
                    <button type="submit" class="btn red">Save Section Settings</button>
                </div>
            </form>
        </section>

        {{-- ==================== 2. Dynamic Audience Cards ==================== --}}
        <section class="card panel" style="min-width: 0; overflow: hidden;">
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

            <div style="overflow-x: auto; max-width: 100%;">
            <table class="tbl" style="min-width: 560px;">
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
            </div>
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
        // letters, spaces and . , ' - & ! ? only: numbers, symbols, emojis and line breaks are removed as typed or pasted
        const disallowedPattern = /[^A-Za-z .,'&!?-]/g;

        function sanitizeCleanFields(scope = document) {
            scope.querySelectorAll('[data-clean-text]').forEach(el => {
                const clean = () => {
                    const cleaned = el.value.replace(disallowedPattern, '').replace(/^ +/, '').replace(/ {2,}/g, ' ');
                    if (cleaned !== el.value) {
                        el.value = cleaned;
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

        // saved values may still contain old padding spaces: tidy them as soon as the page loads
        document.querySelectorAll('[data-clean-text]').forEach(el => {
            const cleaned = el.value.replace(disallowedPattern, '').replace(/^ +/, '').replace(/ {2,}/g, ' ');
            if (cleaned !== el.value) el.value = cleaned;
            if (typeof el.counterUpdate === 'function') el.counterUpdate();
        });
    })();
</script>
@endpush
