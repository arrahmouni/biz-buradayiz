<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    @include('front::includes.meta')
    @stack('meta')

    @include('front::includes.font')
    @stack('font')

    @include('front::includes.style')
    @stack('style')
</head>

<body class="min-h-screen text-gray-900 front-auth-page front-auth-page--pattern-a7">

    <span id="front-auth-submit-processing" class="hidden">{{ __('front::auth.submit_processing') }}</span>

    @include('partials.page-loader')

    @yield('content')
    @include('front::includes.scripts')
    <script src="{{ asset('modules/front/js/front-auth.js') }}?v={{$_STYLE_VER_}}"></script>
    @stack('script')
</body>
</html>
