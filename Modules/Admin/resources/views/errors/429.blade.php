@extends('admin::errors.layouts.custom')

@section('title', trans('admin::error.429_page.title'))

@section('content')
    @include('admin::components.other.image', [
        'options' => [
            'class' => 'illustration',
            'src'   => config('admin.frontend.error_pages.429'),
            'alt'   => '429 too many requests',
        ]
    ])
    <h1>
        @lang('admin::error.429_page.header')
    </h1>
    <p>
        @lang('admin::error.429_page.message')
    </p>
    <p>{!! trans('admin::error.footer') !!}</p>
@endsection
