@extends('front::errors.layouts.custom')

@section('error_page_title', trans('front::error.401_page.title'))

@section('error_body')
    @include('front::errors.partials.shell', [
        'icon' => 'fa-user-lock',
        'code' => '401',
        'titleKey' => 'front::error.401_page.header',
        'messageKey' => 'front::error.401_page.message',
    ])
@endsection
