@extends('layout.app')

@section('title', 'Insights | Alba Business')

@section('content')
    @include('sections.insights-banner')
    @include('sections.insights-featured')
    @include('sections.insights-articles')
@endsection
