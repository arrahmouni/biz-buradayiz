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

<body @class(['bg-gray-50', 'text-gray-800', 'app-env-staging' => isStaging()])>

    <span id="front-submit-processing-label" class="hidden">{{ __('front::auth.submit_processing') }}</span>

    @include('partials.staging-environment-banner')
    @include('partials.page-loader')

    @include('front::includes.navbar')
    @yield('content')
    @include('front::includes.footer')

    @include('front::includes.scripts')
    @stack('script')
</body>
</html>
