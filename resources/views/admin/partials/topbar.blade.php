<header class="topbar">
    <a href="{{ route('home') }}" target="_blank" class="topbar-link">View Website &nearr;</a>
    <div class="user">
        <span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
        <span>{{ auth()->user()->name }}</span>
    </div>
</header>