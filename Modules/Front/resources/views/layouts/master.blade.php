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

<body class="bg-gray-50 text-gray-800">

    @include('front::includes.navbar')
    @yield('content')
    @include('front::includes.footer')
    
    @include('front::includes.scripts')
    @stack('script')
</body>
</html>
