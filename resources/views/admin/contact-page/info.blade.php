@extends('admin.layout')

@section('title', 'Get In Touch Section')
@section('page_title', 'Contact Page - Get In Touch')
@section('breadcrumbs')<a href="{{ route('admin.banners.index') }}">Home</a> <span>&rsaquo;</span> Page Settings <span>&rsaquo;</span> Contact Page <span>&rsaquo;</span> Get In Touch Section @endsection

@section('content')
    @php($c = config('banner.contact_info'))
    @php($hint = "Letters, numbers, spaces and . , ' - & ! ? only")
    <p class="hint">These details are shown beside the contact form on the Contact page. Messages sent through the form appear under <a href="{{ route('admin.contactpage.messages.index') }}"><strong>Contact Page &rarr; Enquiries</strong></a>. All fields marked * are mandatory.</p>

    <form method="POST" action="{{ route('admin.contactpage.info.update') }}" class="card form-card" data-live-validate novalidate>
        @csrf
        @method('PUT')

        <h3>Heading</h3>
        <label>Heading (white line) *
            <input type="text" name="heading" value="{{ old('heading', $info->heading) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                   minlength="{{ $c['heading_min'] }}" maxlength="{{ $c['heading_max'] }}" placeholder="{{ $hint }}">
        </label>
        <label>Heading accent (gold italic line) *
            <input type="text" name="heading_accent" value="{{ old('heading_accent', $info->heading_accent) }}" data-live="line" required data-word-max="{{ $c['word_max'] }}"
                   minlength="{{ $c['accent_min'] }}" maxlength="{{ $c['accent_max'] }}" placeholder="{{ $hint }}">
        </label>
        <label>Description *
            <textarea name="description" rows="3" data-live="line" required data-word-max="{{ $c['long_word_max'] }}"
                      minlength="{{ $c['description_min'] }}" maxlength="{{ $c['description_max'] }}" placeholder="{{ $hint }}">{{ old('description', $info->description) }}</textarea>
        </label>

        <h3>Company contact details</h3>
        <label>Email address *
            <input type="text" name="email" value="{{ old('email', $info->email) }}" data-live="email" required maxlength="{{ $c['email_max'] }}" placeholder="name@example.com" inputmode="email">
        </label>
        <label>Phone number *
            <input type="text" name="phone" value="{{ old('phone', $info->phone) }}" data-live="phone" required maxlength="{{ $c['phone_max'] }}" placeholder="+91 98765 43210" inputmode="tel">
        </label>
        <label>Address *
            <textarea name="address" rows="2" data-live="address" required minlength="{{ $c['address_min'] }}" maxlength="{{ $c['address_max'] }}" placeholder="Street, area, city, state, PIN, country (letters, numbers, spaces and . , ' &amp; # / - only)">{{ old('address', $info->address) }}</textarea>
        </label>

        <h3>Visibility</h3>
        <div class="check end">
            <span>Show this section on the Contact page</span>
            <label class="switch">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $info->is_active))>
                <i></i>
            </label>
        </div>

        <button type="submit" class="btn">Save changes</button>
    </form>
@endsection
