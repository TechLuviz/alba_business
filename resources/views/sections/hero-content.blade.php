@php($bare = blank($item->heading_line_1) && blank($item->heading_line_2) && blank($item->heading_accent) && blank($item->description))
<div class="container hero-content {{ $bare ? 'is-bare' : '' }}">
    <h2>
        @if($item->heading_line_1)<span class="h-line">{{ $item->heading_line_1 }}</span>@endif
        @if($item->heading_line_2)<span class="h-line">{{ $item->heading_line_2 }}</span>@endif
        @if($item->heading_accent)<em class="h-line">{{ $item->heading_accent }}</em>@endif
    </h2>
    @if($item->description)<p>{{ $item->description }}</p>@endif
    <a href="{{ route('programs.index') }}" class="btn-gold">
        <span>Explore Programs</span>
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 11L11 3M5 3h6v6"/></svg>
    </a>
</div>