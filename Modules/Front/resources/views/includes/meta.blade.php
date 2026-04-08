@php
    $title = isset($title) ? config('app.name') . ' - ' .  $title : trans('front::home.page_title');
    $description = View::getSection('meta_description') ?? trans('front::home.page_description');
    $keywords = View::getSection('meta_keywords') ?? trans('front::home.page_keywords');
@endphp

<title>
    {{$title}}
</title>
<meta charset="utf-8" />
<meta name="description" content="{{ $description }}"/>
<meta name="keywords" content="{{ $keywords }}" />
<link rel="canonical" href="{{ request()->url() }}">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="csrf-token" content="{{ csrf_token() }}">

<!--begin::shortcut icon-->
<link rel="shortcut icon" href="{{ getSetting('app_favicon', asset('images/default/logos/favicon.png')) }}" />
<!--end::shortcut icon-->
