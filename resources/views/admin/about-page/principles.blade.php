@extends('admin.layout')

@section('title', 'Guiding Principles')
@section('page_title', 'About Page - Guiding Principles')
@section('breadcrumbs')<a href="{{ route('admin.banners.index') }}">Home</a> <span>&rsaquo;</span> About Page Settings <span>&rsaquo;</span> Guiding Principles @endsection

@section('content')
    @php($c = config('banner.principles'))
    @php($img = $c['image'])
    @php($hint = "Letters, numbers, spaces and . , ' - & ! ? only")
    <p class="hint">Set the section image and label, then edit the two principles (Mission and Vision). All fields marked * are mandatory.</p>

    <section class="card form-card" style="max-width: 100%;">
        <form method="POST" action="{{ route('admin.aboutpage.principles.settings.update') }}" enctype="multipart/form-data" data-live-validate novalidate>
            @csrf
            @method('PUT')
            <h3>Section</h3>
            <label>Section label (eyebrow) *
                <input type="text" name="eyebrow" value="{{ old('eyebrow', $section->eyebrow) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                       minlength="{{ $c['eyebrow_min'] }}" maxlength="{{ $c['eyebrow_max'] }}" placeholder="{{ $hint }}">
            </label>
            <div class="spec">
                <strong>Image requirements</strong>
                <ul>
                    <li>Recommended: {{ $img['recommended'] }}</li>
                    <li>Min {{ $img['min_width'] }} x {{ $img['min_height'] }} px &middot; max {{ $img['max_width'] }} x {{ $img['max_height'] }} px</li>
                    <li>JPG, PNG or WEBP &middot; max {{ round($img['max_kb'] / 1024) }} MB &middot; it fills the left half of the section and is cropped to fit.</li>
                </ul>
            </div>
            <label>Section image *
                <input type="file" name="image" id="principlesImage" accept="image/png,image/jpeg,image/webp" @if(!$section->image) required @endif
                       data-max-kb="{{ $img['max_kb'] }}" data-min-w="{{ $img['min_width'] }}" data-min-h="{{ $img['min_height'] }}"
                       data-max-w="{{ $img['max_width'] }}" data-max-h="{{ $img['max_height'] }}">
            </label>
            <div class="preview">
                <img id="principlesPreview" src="{{ $section->image ? asset($section->image) : '' }}" alt="" style="max-height:160px; border-radius:8px; @if(!$section->image) display:none @endif">
            </div>
            <div class="check end" style="margin: 8px 0 16px;">
                <span>Show this section on the About page</span>
                <label class="switch">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $section->is_active))>
                    <i></i>
                </label>
            </div>
            <button type="submit" class="btn">Save section</button>
        </form>
    </section>

    <section class="card panel" style="margin-top: 20px;">
        <div class="panel-head">
            <h2>Principles</h2>
        </div>

        <table class="tbl">
            <thead>
                <tr><th>S.N.</th><th>Principle</th><th class="r">Action</th></tr>
            </thead>
            <tbody>
            @foreach($items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        <strong class="strong ellipsis" style="display:block;">{{ $item->heading }} {{ $item->heading_accent }} <span class="tag">{{ $item->label }}</span></strong>
                        <small class="ellipsis" style="color: var(--muted); display:block; max-width: 520px;">{{ $item->description }}</small>
                    </td>
                    <td class="r actions">
                        <button type="button" class="act" title="Edit" data-edit-principle="{{ json_encode(['id' => $item->id, 'label' => $item->label, 'heading' => $item->heading, 'heading_accent' => $item->heading_accent, 'description' => $item->description]) }}">@include('admin.partials.icon-edit')</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </section>
    @include('admin.about-page._principle-canvas')
@endsection

@push('scripts')
<script>
    (function () {
        document.querySelectorAll('[data-edit-principle]').forEach(b => b.onclick = () => {
            const d = JSON.parse(b.dataset.editPrinciple);
            Admin.openForm('canvas-principle', { title: 'Edit Principle', id: d.id, data: d });
        });

        const input = document.getElementById('principlesImage'), img = document.getElementById('principlesPreview');
        input.addEventListener('rejected', () => { img.style.display = 'none'; });
        input.addEventListener('change', () => setTimeout(() => {
            const f = input.files[0];
            if (!f) return;
            img.src = URL.createObjectURL(f); img.style.display = 'block';
        }, 0));
    })();
</script>
@endpush
