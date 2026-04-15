@extends('front::layouts.master')

@section('content')
    @include('front::home.sections.hero')
    @include('front::home.sections.how-it-works')
    @include('front::home.sections.services')
    @include('front::home.sections.cta')
@endsection
