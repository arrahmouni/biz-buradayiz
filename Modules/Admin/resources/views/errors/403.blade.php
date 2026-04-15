@extends('admin::errors.layouts.custom')

@section('title', trans('admin::error.403_page.title'))

@section('content')
    @include('admin::components.other.image', [
        'options' => [
            'class' => 'illustration',
            'src'   => config('admin.frontend.error_pages.403'),
            'alt'   => '403 forbidden',
        ]
    ])
    <h1>
        @lang('admin::error.403_page.header')
    </h1>
    <p>
        @lang('admin::error.403_page.message')
    </p>
    <p>{!! trans('admin::error.footer') !!}</p>
@endsection
