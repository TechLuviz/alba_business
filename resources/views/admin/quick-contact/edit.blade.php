@extends('admin.layout')

@section('title', 'Quick Contact')
@section('page_title', 'Quick Contact')
@section('breadcrumbs')<a href="{{ route('admin.banners.index') }}">Home</a> <span>&rsaquo;</span> Site Settings <span>&rsaquo;</span> Quick Contact @endsection

@section('content')
    <form method="POST" action="{{ route('admin.quick.update') }}" class="card form-card">
        @csrf
        @method('PUT')

        <p style="color:#6b7280;margin-bottom:6px">These show as quick-access icons on the right side of every website page. Leave a field empty to hide its icon.</p>

        <label>Email
            <input type="email" name="email" value="{{ old('email', $quick->email) }}" maxlength="120" placeholder="admissions@example.com">
            @error('email')<small style="color:#c0392b">{{ $message }}</small>@enderror
        </label>

        <label>Contact number
            <input type="tel" name="phone" value="{{ old('phone', $quick->phone) }}" maxlength="30" placeholder="+91 98765 43210">
            @error('phone')<small style="color:#c0392b">{{ $message }}</small>@enderror
        </label>

        <label>WhatsApp number
            <input type="tel" name="whatsapp" value="{{ old('whatsapp', $quick->whatsapp) }}" maxlength="30" placeholder="+91 98765 43210 (with country code)">
            @error('whatsapp')<small style="color:#c0392b">{{ $message }}</small>@enderror
        </label>

        <label style="display:flex;align-items:center;gap:10px">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $quick->is_active ?? true)) style="width:auto">
            Show quick contact icons on the website
        </label>

        <button type="submit" class="btn">Save changes</button>
    </form>
@endsection
