@extends('layout.app')

@section('title', 'Home | Alba Business')

@section('content')
    @include('sections.home-hero')
    @include('sections.home-text')
    @include('sections.home-about')
    @include('sections.home-audience')
    @include('sections.home-problem')
    @include('sections.home-programs')
    @include('sections.home-team')
    @include('sections.home-insights')
    @include('sections.home-content')
    @include('sections.home-reels')
@endsection
