@extends('front::errors.layouts.custom')

@section('error_page_title', trans('front::error.404_page.title'))

@section('error_body')
    @include('front::errors.partials.shell', [
        'icon' => 'fa-map-signs',
        'code' => '404',
        'titleKey' => 'front::error.404_page.header',
        'messageKey' => 'front::error.404_page.message',
    ])
@endsection
