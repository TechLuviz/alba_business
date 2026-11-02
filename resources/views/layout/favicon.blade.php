@php($__favicon = \App\Models\HeroBanner::current()->brand_favicon)
@if($__favicon)
    <link rel="icon" href="{{ asset($__favicon) }}?v={{ @filemtime(public_path($__favicon)) }}">
    <link rel="shortcut icon" href="{{ asset($__favicon) }}?v={{ @filemtime(public_path($__favicon)) }}">
@endif
