<!DOCTYPE html>
<html lang="{{$_LOCALE_}}" dir="{{$_DIR_}}" >
<head>
    <meta charset="utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="shortcut icon" href="{{ getSetting(\Modules\Config\Constatnt::APP_FAVICON, asset('images/default/logos/favicon.png')) }}" />

    <title>
        @yield('title')
    </title>

    @include('admin::includes.font')

    @include('admin::errors.layouts.style')
</head>
<body @class(['app-env-staging' => isStaging()])>
    @include('partials.staging-environment-banner')
    <div class="container">
        @yield('content')
    </div>
</body>
</html>
