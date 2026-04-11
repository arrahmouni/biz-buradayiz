@extends('front::errors.layouts.custom')

@section('error_page_title', trans('front::error.503_page.title'))

@section('error_body')
    @include('front::errors.partials.shell', [
        'icon' => 'fa-screwdriver-wrench',
        'code' => '503',
        'titleKey' => 'front::error.503_page.header',
        'messageKey' => 'front::error.503_page.message',
    ])
@endsection
