@extends('front::layouts.auth')

@section('content')
    <div class="flex items-center justify-center min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="front-auth-card-panel max-w-md w-full space-y-8 bg-white rounded-2xl shadow-xl p-8 md:p-10">
            @include('front::provider.auth.partials.card-header', [
                'title' => __('front::auth.forgot_title'),
                'subtitle' => __('front::auth.forgot_subtitle'),
            ])

            @if (session('status'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">{{ session('status') }}</div>
            @endif

            <form class="mt-8 space-y-6" method="post" action="{{ route('front.provider.password.email') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('front::auth.email') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400 text-sm" aria-hidden="true"></i>
                            </div>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required
                                class="appearance-none block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg placeholder-gray-400 text-gray-900 outline-none focus:border-red-400 focus:ring-2 focus:ring-red-200 sm:text-sm transition"
                                placeholder="{{ __('front::auth.email') }}">
                        </div>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="group relative w-full flex justify-center items-center py-2.5 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition shadow-md">
                    <i class="fas fa-paper-plane mr-2" aria-hidden="true"></i>
                    {{ __('front::auth.submit_forgot') }}
                </button>
            </form>

            <div class="text-center space-y-3 text-sm">
                <a href="{{ route('front.provider.login') }}" class="font-medium text-red-600 hover:text-red-500 transition">{{ __('front::auth.link_login') }}</a>
                <a href="{{ route('front.index') }}" class="block font-medium text-gray-500 hover:text-gray-700 transition">{{ __('front::auth.link_home') }}</a>
            </div>
        </div>
    </div>
@endsection
