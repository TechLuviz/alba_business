{{--
    Admin sidebar. Every top-level menu lives in its own file in this folder.
    To add a new menu: create resources/views/admin/sidebar/<name>.blade.php and @include it below.
--}}
<aside class="sidebar">
    <div class="brand">ALBA<span>HUB</span><small>Admin Panel</small></div>

    <nav class="menu">
        @include('admin.sidebar.page-settings')
        @include('admin.sidebar.site-settings')
    </nav>

    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit" class="logout">Logout</button>
    </form>
</aside>