@extends('front::errors.layouts.custom')

@section('error_page_title', trans('front::error.405_page.title'))

@section('error_body')
    @include('front::errors.partials.shell', [
        'icon' => 'fa-code',
        'code' => '405',
        'titleKey' => 'front::error.405_page.header',
        'messageKey' => 'front::error.405_page.message',
    ])
@endsection
