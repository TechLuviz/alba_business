@extends('layout.app')

@section('title', $policy->title . ' | Alba Business')

@section('content')
    <section class="about-banner privacy-banner">
        <div class="container about-banner-inner">
            <h1><span>{{ $policy->title }}</span></h1>
            <p>Last updated {{ $policy->updated_at?->format('F j, Y') }}</p>
        </div>
    </section>

    <section class="privacy-body">
        <div class="container">
            <article class="privacy-content">{!! $policy->content !!}</article>
        </div>
    </section>
@endsection
