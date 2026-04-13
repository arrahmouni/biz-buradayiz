@extends('front::layouts.auth')

@section('content')
    <div class="flex items-center justify-center min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="front-auth-card-panel max-w-md w-full space-y-8 bg-white rounded-2xl shadow-xl p-8 md:p-10">
            @include('front::provider.auth.partials.card-header', [
                'title' => __('front::auth.dashboard_title'),
                'subtitle' => __('front::auth.dashboard_subtitle'),
            ])

            <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-4 text-center">
                <p class="text-sm text-gray-500">{{ __('front::auth.signed_in_as') }}</p>
                <p class="text-lg font-semibold text-gray-900 mt-1">{{ $providerUser->full_name }}</p>
            </div>

            @php($slug = $providerUser->frontProfileSlug())
            @if ($slug !== '')
                <a href="{{ route('front.provider.show', ['provider' => $slug]) }}" target="_blank" rel="noopener noreferrer"
                    class="flex justify-center items-center w-full py-2.5 px-4 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">
                    <i class="fas fa-external-link-alt mr-2 text-gray-500" aria-hidden="true"></i>
                    {{ __('front::auth.view_public_profile') }}
                </a>
            @endif

            <form method="post" action="{{ route('front.provider.logout') }}">
                @csrf
                <button type="submit" class="group relative w-full flex justify-center items-center py-2.5 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition shadow-md">
                    <i class="fas fa-sign-out-alt mr-2" aria-hidden="true"></i>
                    {{ __('front::auth.logout') }}
                </button>
            </form>

            <div class="text-center text-sm">
                <a href="{{ route('front.index') }}" class="font-medium text-gray-500 hover:text-gray-700 transition">{{ __('front::auth.link_home') }}</a>
            </div>
        </div>
    </div>
@endsection
