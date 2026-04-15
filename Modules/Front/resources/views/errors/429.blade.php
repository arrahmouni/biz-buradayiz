@extends('front::errors.layouts.custom')

@section('error_page_title', trans('front::error.429_page.title'))

@section('error_body')
    @include('front::errors.partials.shell', [
        'icon' => 'fa-gauge-high',
        'code' => '429',
        'titleKey' => 'front::error.429_page.header',
        'messageKey' => 'front::error.429_page.message',
    ])
@endsection
