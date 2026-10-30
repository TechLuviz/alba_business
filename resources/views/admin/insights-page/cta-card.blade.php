@extends('admin.layout')

@section('title', 'Insight Detail Card')
@section('page_title', 'Insight Detail Page Card')
@section('breadcrumbs')<a href="{{ route('admin.banners.index') }}">Home</a> <span>&rsaquo;</span> Page Settings <span>&rsaquo;</span> Insights Page <span>&rsaquo;</span> Insight Detail Card @endsection

@section('content')
    @php($c = config('banner.insight_cta'))
    @php($hint = "Letters, numbers, spaces and . , ' - & ! ? only")
    <form method="POST" action="{{ route('admin.insightpage.cta.update') }}" class="card form-card" data-live-validate novalidate>
        @csrf
        @method('PUT')

        <p class="hint">This card appears on every insight detail page, above the "Share this article" icons. The button is fixed in the website code.</p>

        <h3>Card text</h3>
        <label>Heading *
            <input type="text" name="heading" value="{{ old('heading', $card->heading) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                   minlength="{{ $c['heading_min'] }}" maxlength="{{ $c['heading_max'] }}" placeholder="{{ $hint }}">
        </label>
        <label>Highlighted heading (shown in gold) *
            <input type="text" name="heading_accent" value="{{ old('heading_accent', $card->heading_accent) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                   minlength="{{ $c['heading_min'] }}" maxlength="{{ $c['heading_max'] }}" placeholder="{{ $hint }}">
        </label>
        <label>Description *
            <textarea name="description" rows="4" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                      minlength="{{ $c['description_min'] }}" maxlength="{{ $c['description_max'] }}" placeholder="{{ $hint }}">{{ old('description', $card->description) }}</textarea>
        </label>

        <h3>Visibility</h3>
        <div class="check end">
            <span>Show this card on insight detail pages</span>
            <label class="switch">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $card->is_active))>
                <i></i>
            </label>
        </div>

        <button type="submit" class="btn">Save changes</button>
    </form>
@endsection
