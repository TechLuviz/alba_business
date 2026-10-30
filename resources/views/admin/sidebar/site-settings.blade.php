{{-- Site Settings > Logo & Brand --}}
<details class="menu-group" @if(request()->routeIs('admin.brand.*', 'admin.quick.*', 'admin.newsletter.*', 'admin.seo.*')) open @endif>
    <summary>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/></svg>
        <span>Site Settings</span>
    </summary>
    <div class="submenu">
        <a href="{{ route('admin.brand.edit') }}" class="{{ request()->routeIs('admin.brand.*') ? 'active' : '' }}">Logo &amp; Brand</a>
        <a href="{{ route('admin.quick.edit') }}" class="{{ request()->routeIs('admin.quick.*') ? 'active' : '' }}">Quick Contact</a>
        <a href="{{ route('admin.newsletter.index') }}" class="{{ request()->routeIs('admin.newsletter.*') ? 'active' : '' }}">Newsletter</a>
        <a href="{{ route('admin.seo.edit') }}" class="{{ request()->routeIs('admin.seo.*') ? 'active' : '' }}">SEO Settings</a>
    </div>
</details>