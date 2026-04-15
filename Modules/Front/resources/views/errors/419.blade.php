@extends('front::errors.layouts.custom')

@section('error_page_title', trans('front::error.419_page.title'))

@section('error_body')
    @include('front::errors.partials.shell', [
        'icon' => 'fa-clock-rotate-left',
        'code' => '419',
        'titleKey' => 'front::error.419_page.header',
        'messageKey' => 'front::error.419_page.message',
    ])
@endsection
