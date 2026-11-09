<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') | Albahub</title>
    @include('layout.favicon')
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}?v={{ filemtime(public_path('assets/css/admin.css')) }}">
</head>
<body>
    <div class="admin-wrap">
        @include('admin.sidebar.sidebar')

        <div class="main">
            @include('admin.partials.topbar')

            <div class="content">
                <div class="page-head">
                    <div>
                        <h1>@yield('page_title')</h1>
                        <nav class="crumbs">@yield('breadcrumbs')</nav>
                    </div>
                    <div class="page-actions">
                        <a href="{{ url()->current() }}" class="icon-btn" title="Refresh">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-3-6.7L21 8M21 3v5h-5"/></svg>
                        </a>
                    </div>
                </div>

                @if(session('success'))<div class="alert success">{{ session('success') }}</div>@endif
                @if($errors->any())
                    <div class="alert error"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <div class="backdrop" id="backdrop"></div>
    <script src="{{ asset('assets/js/admin.js') }}?v={{ filemtime(public_path('assets/js/admin.js')) }}"></script>
    <script src="{{ asset('assets/js/admin-select.js') }}?v={{ filemtime(public_path('assets/js/admin-select.js')) }}"></script>
    <script>
        // Stop the mouse wheel from silently changing a focused number field while scrolling the page/panel.
        document.addEventListener('wheel', function (e) {
            var el = document.activeElement;
            if (el && el.type === 'number' && el === e.target) {
                el.blur();
            }
        }, { passive: true });
    </script>
    @stack('scripts')
</body>
</html>