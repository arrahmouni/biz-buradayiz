@extends('front::layouts.auth')

@section('content')
    <div class="flex items-center justify-center min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="front-auth-card-panel max-w-md w-full space-y-8 bg-white rounded-2xl shadow-xl p-8 md:p-10">
            @include('front::provider.auth.partials.card-header', [
                'title' => __('front::auth.login_title'),
                'subtitle' => __('front::auth.login_subtitle'),
            ])

            @if (session('status'))
                <div class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800" role="status">{{ session('status') }}</div>
            @endif

            @if (session('success'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">{{ session('success') }}</div>
            @endif

            @include('front::provider.auth.partials.response-helper-flash')

            <form class="mt-8 space-y-6" method="post" action="{{ route('front.provider.login.store') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('front::auth.email') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400 text-sm" aria-hidden="true"></i>
                            </div>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required
                                class="appearance-none block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg placeholder-gray-400 text-gray-900 outline-none focus:border-red-400 focus:ring-2 focus:ring-red-200 sm:text-sm transition"
                                placeholder="{{ __('front::auth.email') }}">
                        </div>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('front::auth.password') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400 text-sm" aria-hidden="true"></i>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="current-password" required
                                class="appearance-none block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg placeholder-gray-400 text-gray-900 outline-none focus:border-red-400 focus:ring-2 focus:ring-red-200 sm:text-sm transition"
                                placeholder="••••••••">
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600" data-auth-password-toggle aria-controls="password">
                                <i class="fas fa-eye text-sm" aria-hidden="true"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="front-auth-checkbox-wrap flex items-center">
                            <input id="remember" name="remember" type="checkbox" value="1" @checked(old('remember'))
                                class="front-auth-checkbox__input">
                            <label for="remember" class="front-auth-checkbox">
                                <span class="front-auth-checkbox__box">
                                    <svg class="front-auth-checkbox__check" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M2.5 6l2.5 2.5L9.5 3.5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span class="front-auth-checkbox__label">{{ __('front::auth.remember_me') }}</span>
                            </label>
                        </div>
                        <div class="text-sm">
                            <a href="{{ route('front.provider.password.request') }}" class="font-medium text-red-600 hover:text-red-500 transition">{{ __('front::auth.link_forgot') }}</a>
                        </div>
                    </div>
                </div>

                <button type="submit" class="group relative w-full flex justify-center items-center py-2.5 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition shadow-md">
                    <i class="fas fa-sign-in-alt mr-2" aria-hidden="true"></i>
                    {{ __('front::auth.submit_login') }}
                </button>
            </form>

            <div class="text-center space-y-3 text-sm">
                <p class="text-gray-600">
                    {{ __('front::auth.register_prompt') }}
                    <a href="{{ route('front.provider.register.form') }}" class="font-medium text-red-600 hover:text-red-500 transition">{{ __('front::auth.link_register') }}</a>
                </p>
                <a href="{{ route('front.index') }}" class="block font-medium text-gray-500 hover:text-gray-700 transition">{{ __('front::auth.link_home') }}</a>
            </div>
        </div>
    </div>
@endsection
