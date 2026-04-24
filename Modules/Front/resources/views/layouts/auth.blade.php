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

<body @class(['min-h-screen', 'text-gray-900', 'front-auth-page', 'front-auth-page--pattern-a7', 'app-env-staging' => isStaging()])>

    <span id="front-submit-processing-label" class="hidden">{{ __('front::auth.submit_processing') }}</span>

    @include('partials.staging-environment-banner')
    @include('partials.page-loader')

    @yield('content')
    @include('front::includes.scripts')
    <script src="{{ asset('modules/front/js/front-auth.js') }}?v={{$_STYLE_VER_}}"></script>
    @stack('script')
</body>
</html>
