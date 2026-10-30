@extends('admin.layout')

@section('title', 'Our Team Section')
@section('page_title', 'About Page - Our Team')
@section('breadcrumbs')<a href="{{ route('admin.banners.index') }}">Home</a> <span>&rsaquo;</span> About Page Settings <span>&rsaquo;</span> Our Team Section @endsection

@section('content')
    @php($c = config('banner.about_team'))
    @php($hint = "Letters, numbers, spaces and . , ' - & ! ? only")
    <p class="hint">The members shown in this carousel are the ones managed under <a href="{{ route('admin.team.index') }}"><strong>Page Settings &rarr; Home Page &rarr; Team Section</strong></a> ({{ $memberCount }} active member{{ $memberCount === 1 ? '' : 's' }} right now). Add, edit or hide members there and both pages update. Here you only edit this section's heading. All fields marked * are mandatory.</p>

    <form method="POST" action="{{ route('admin.aboutpage.team.update') }}" class="card form-card" data-live-validate novalidate>
        @csrf
        @method('PUT')

        <h3>Heading</h3>
        <label>Section label (eyebrow) *
            <input type="text" name="eyebrow" value="{{ old('eyebrow', $team->eyebrow) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                   minlength="{{ $c['eyebrow_min'] }}" maxlength="{{ $c['eyebrow_max'] }}" placeholder="{{ $hint }}">
        </label>
        <label>Heading (white line) *
            <input type="text" name="heading" value="{{ old('heading', $team->heading) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                   minlength="{{ $c['heading_min'] }}" maxlength="{{ $c['heading_max'] }}" placeholder="{{ $hint }}">
        </label>
        <label>Heading accent (gold italic line) *
            <input type="text" name="heading_accent" value="{{ old('heading_accent', $team->heading_accent) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                   minlength="{{ $c['accent_min'] }}" maxlength="{{ $c['accent_max'] }}" placeholder="{{ $hint }}">
        </label>

        <h3>Visibility</h3>
        <div class="check end">
            <span>Show this section on the About page</span>
            <label class="switch">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $team->is_active))>
                <i></i>
            </label>
        </div>

        <button type="submit" class="btn">Save changes</button>
    </form>
@endsection
