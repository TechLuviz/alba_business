@extends('layout.app')

@section('title', 'Sitemap | Alba Business')

@section('content')
    <section class="about-banner privacy-banner">
        <div class="container about-banner-inner">
            <h1><span>Sitemap</span></h1>
            <p>Every page of the website in one place.</p>
        </div>
    </section>

    <section class="privacy-body sitemap-body">
        <div class="container">
            <div class="sitemap-grid">
                @foreach([['Pages', $pages], ['Programs', $programs], ['Blogs', $articles], ['Legal', $legal]] as [$heading, $links])
                    @continue(empty($links))
                    <nav class="sitemap-group" aria-label="{{ $heading }}">
                        <h2>{{ $heading }}</h2>
                        <ul>
                            @foreach($links as $link)
                                <li><a href="{{ $link['url'] }}">{{ $link['label'] }}</a></li>
                            @endforeach
                        </ul>
                    </nav>
                @endforeach
            </div>
        </div>
    </section>
@endsection
