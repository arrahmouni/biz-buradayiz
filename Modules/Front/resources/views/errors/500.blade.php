@extends('front::errors.layouts.custom')

@section('error_page_title', trans('front::error.500_page.title'))

@section('error_body')
    @include('front::errors.partials.shell', [
        'icon' => 'fa-triangle-exclamation',
        'code' => '500',
        'titleKey' => 'front::error.500_page.header',
        'messageKey' => 'front::error.500_page.message',
    ])
@endsection
