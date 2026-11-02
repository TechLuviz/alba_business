@extends('layout.app')

@section('title', ($card->banner_title ?: $card->title) . ' | Alba Business')

@section('content')
    <section class="program-banner" @if($card->banner_image) style="background-image: url('{{ asset($card->banner_image) }}')" @endif>
        <div class="container program-banner-inner">
            <p class="program-banner-eyebrow">Program</p>
            <h1>{{ $card->banner_title ?: $card->title }}</h1>
            @if($card->banner_description)<p class="program-banner-desc">{{ $card->banner_description }}</p>@endif

            @if($card->banner_levels || $card->banner_timeline)
                <ul class="program-banner-meta">
                    @if($card->banner_levels)
                        <li>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="3" width="16" height="6" rx="2"/><rect x="3" y="9" width="18" height="12" rx="3"/></svg>
                            <span>@if(ctype_digit((string) $card->banner_levels)){{ (int) $card->banner_levels }} {{ (int) $card->banner_levels === 1 ? 'level' : 'levels' }}@else{{ $card->banner_levels }}@endif</span>
                        </li>
                    @endif
                    @if($card->banner_timeline)
                        <li>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M3 11h18M8 3v4M16 3v4"/></svg>
                            <span>{{ $card->banner_timeline }}</span>
                        </li>
                    @endif
                </ul>
            @endif
        </div>
    </section>

    <section class="program-detail">
        @php($hasHelp = $help->help_active && ($help->help_heading || $help->help_image))
        <div class="container program-layout {{ $hasHelp ? 'has-side' : '' }}">
            <div class="program-main">
                @if($card->about_text)
                    <h2>About This Program</h2>
                    <p class="program-about">{{ $card->about_text }}</p>
                @endif

                <h2>What You&rsquo;ll Learn</h2>
                <ul class="program-detail-features">
                    @foreach($card->features() as $feature)<li><span>{{ $feature }}</span></li>@endforeach
                </ul>
                @php($levels = $card->levels ?? [])
                @php($currPoints = $card->curriculumPoints())
                @if(count($levels) || count($currPoints))
                    <div class="program-curr-head">
                        <h2>Program Curriculum</h2>
                        @if(count($levels))<p>{{ count($levels) }} {{ count($levels) === 1 ? 'Level' : 'Levels' }}@if($card->banner_timeline) <span>|</span> {{ $card->banner_timeline }}@endif</p>@elseif($card->banner_timeline)<p>{{ $card->banner_timeline }}</p>@endif
                    </div>
                    @if(count($currPoints))
                        <ul class="program-curr-points">@foreach($currPoints as $pt)<li>{{ $pt }}</li>@endforeach</ul>
                    @endif
                    @if(count($levels))
                    <div class="program-curr" id="programCurr">
                        @foreach($levels as $i => $lvl)
                            <details class="curr-item" @if($i === 0) open @endif>
                                <summary>
                                    <span class="curr-num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                    <span class="curr-text">
                                        <small>Level {{ $i + 1 }}@if(!empty($lvl['period'])) | {{ ctype_digit((string) $lvl['period']) ? 'Month ' . $lvl['period'] : $lvl['period'] }}@endif</small>
                                        @if(!empty($lvl['title']))<strong>{{ $lvl['title'] }}</strong>@endif
                                    </span>
                                    <i class="curr-chev" aria-hidden="true"></i>
                                </summary>
                                <ul>@foreach($lvl['points'] as $pt)<li>{{ $pt }}</li>@endforeach</ul>
                            </details>
                        @endforeach
                    </div>
                    <script>
                        document.querySelectorAll('#programCurr details').forEach(function (d, _, all) {
                            d.addEventListener('toggle', function () { if (d.open) all.forEach(function (o) { if (o !== d) o.open = false; }); });
                        });
                    </script>
                    @endif
                @endif
                @php($tools = $card->tool_items ?? [])
                @if(count($tools))
                    <h2 class="program-sub-h">Tools &amp; Digital Topics</h2>
                    <ul class="program-tools">
                        @foreach($tools as $tool)
                            <li><i aria-hidden="true">@if(!empty($tool['icon']))<img src="{{ asset($tool['icon']) }}" alt="" loading="lazy">@else<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"><path d="M12 3l8 4.5v9L12 21l-8-4.5v-9z"/><path d="M4 7.5l8 4.5 8-4.5M12 12v9"/></svg>@endif</i><span>{{ $tool['name'] }}</span></li>
                        @endforeach
                    </ul>
                @endif

                @php($formats = $card->formats ?? [])
                @if(count($formats))
                    <h2 class="program-sub-h">Learning Format</h2>
                    <div class="program-formats">
                        @foreach($formats as $f)
                            <div class="program-format">
                                <i aria-hidden="true">@if(!empty($f['icon']))<img src="{{ asset($f['icon']) }}" alt="" loading="lazy">@else<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M3 11h18M8 3v4M16 3v4"/></svg>@endif</i>
                                <div>
                                    <strong>{{ $f['title'] }}</strong>
                                    @if(!empty($f['description']))<p>{{ $f['description'] }}</p>@endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                <p class="program-detail-meta"><strong>{{ $card->duration }}</strong> &middot; {{ $card->mode }}</p>
            </div>

            @if($hasHelp)
                <aside class="program-help">
                    @if($help->help_image)<img src="{{ asset($help->help_image) }}" alt="" loading="lazy">@endif
                    <div class="program-help-body">
                        <i class="program-help-rule" aria-hidden="true"></i>
                        @if($help->help_heading)<h3>{{ $help->help_heading }}</h3>@endif
                        @if($help->help_description)<p>{{ $help->help_description }}</p>@endif
                        {{-- Static button: always opens the Contact page --}}
                        <a href="{{ route('contact') }}" class="program-help-btn">Contact Us <span aria-hidden="true">&rarr;</span></a>
                    </div>
                </aside>
            @endif
        </div>
    </section>
@endsection
