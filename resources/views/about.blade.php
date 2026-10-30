@extends('layout.app')

@section('title', 'About | Alba Business')

@section('content')
    @include('sections.about-banner')
    @include('sections.about-story')
    @include('sections.about-principles')
    @include('sections.about-team')
    @include('sections.about-cta')
@endsection
