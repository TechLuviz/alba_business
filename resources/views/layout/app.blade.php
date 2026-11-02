<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php($seo = $seoOverride ?? \App\Models\SeoSetting::where('page', request()->route()?->getName())->first())
    <title>{{ $seo?->meta_title ?: trim($__env->yieldContent('title', config('app.name'))) }}</title>
    @if($seo?->meta_description)
        <meta name="description" content="{{ $seo->meta_description }}">
    @endif
    @include('layout.favicon')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v={{ filemtime(public_path('assets/css/style.css')) }}">
    @stack('styles')
</head>
<body>
    @include('layout.header')

    <main>
        @yield('content')
    </main>

    @include('layout.footer')

    @include('layout.quick-contact')

    @stack('scripts')
</body>
</html>