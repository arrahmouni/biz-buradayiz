@extends('front::errors.layouts.custom')

@section('error_page_title', trans('front::error.403_page.title'))

@section('error_body')
    @include('front::errors.partials.shell', [
        'icon' => 'fa-shield',
        'code' => '403',
        'titleKey' => 'front::error.403_page.header',
        'messageKey' => 'front::error.403_page.message',
    ])
@endsection
