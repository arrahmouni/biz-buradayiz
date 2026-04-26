@extends('front::layouts.master')

@php
    use Modules\Zms\Models\City;
    use Modules\Zms\Models\State;

    $user = $providerUser;

    $accountOldStateId = (string) old('state_id', '');
    $accountOldCityId = (string) old('city_id', '');
    $accountOldStateName = '';
    $accountOldCityName = '';

    if ($accountOldCityId !== '') {
        $accountOldCity = City::query()->with(['state'])->find($accountOldCityId);
        if ($accountOldCity) {
            $accountOldCityName = (string) $accountOldCity->name;
            $accountOldStateId = (string) $accountOldCity->state_id;
            $accountOldStateName = (string) ($accountOldCity->state?->name ?? '');
        }
    } elseif ($accountOldStateId !== '') {
        $accountOldState = State::query()->find($accountOldStateId);
        $accountOldStateName = (string) ($accountOldState?->name ?? '');
    } elseif ($user->city_id) {
        $user->loadMissing('city.state');
        $accountOldCityId = (string) $user->city_id;
        $accountOldCityName = (string) ($user->city?->name ?? $user->city?->native_name ?? '');
        $accountOldStateId = (string) ($user->city?->state_id ?? '');
        $accountOldStateName = (string) ($user->city?->state?->name ?? $user->city?->state?->native_name ?? '');
    }
@endphp

@section('content')
    <div class="bg-gradient-to-br from-red-900 via-red-800 to-red-900 text-white">
        <div class="container mx-auto px-4 py-10 md:py-14 lg:px-8">
            <div class="flex flex-col gap-6 md:flex-row md:items-start md:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-red-200/90">{{ __('front::provider_account.page_kicker') }}</p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight md:text-4xl">{{ __('front::provider_account.page_title') }}</h1>
                    <p class="mt-2 text-red-100/95">{{ __('front::provider_account.page_subtitle') }}</p>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
                    <a href="{{ route('front.provider.dashboard') }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-white/70 bg-white/10 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-red-800">
                        <i class="fas fa-tachometer-alt" aria-hidden="true"></i> {{ __('front::auth.dashboard_title') }}
                    </a>
                    @if (filled($user->profile_slug))
                        <a href="{{ route('front.provider.show', $user->profile_slug) }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-white/70 bg-white/10 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-red-800">
                            <i class="fas fa-external-link-alt" aria-hidden="true"></i> {{ __('front::provider_dashboard.nav_public_profile') }}
                        </a>
                    @endif
                    <a href="{{ route('front.index') }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-white/70 bg-white/10 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-red-800">
                        <i class="fas fa-home" aria-hidden="true"></i> {{ __('front::provider_dashboard.nav_home') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="provider-account-page container mx-auto px-4 py-10 lg:px-8 space-y-10">
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900" role="status">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-4 text-red-950 sm:px-5" role="alert" aria-live="assertive">
                <div class="flex gap-3">
                    <i class="fas fa-exclamation-triangle mt-0.5 shrink-0 text-red-600" aria-hidden="true"></i>
                    <div class="min-w-0 flex-1 space-y-2">
                        <p class="text-sm font-semibold text-red-900">{{ __('front::provider_account.validation_alert_title') }}</p>
                        <p class="text-sm text-red-800/95">{{ __('front::provider_account.validation_alert_intro') }}</p>
                        <ul class="list-inside list-disc space-y-1 text-sm text-red-900">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm ring-1 ring-black/5" aria-labelledby="profile-section-heading">
            <div class="border-l-4 border-red-600 bg-gradient-to-br from-white via-white to-gray-50/60 px-5 py-6 sm:px-7 sm:py-7">
                <h2 id="profile-section-heading" class="text-xl font-bold tracking-tight text-gray-900">{{ __('front::provider_account.section_profile') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('front::provider_account.section_profile_intro') }}</p>

                <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-amber-950 sm:px-5" role="alert">
                    <div class="flex gap-3">
                        <i class="fas fa-exclamation-circle mt-0.5 shrink-0 text-amber-600" aria-hidden="true"></i>
                        <p class="text-sm leading-relaxed">{{ __('front::provider_account.public_profile_link_notice') }}</p>
                    </div>
                </div>

                <form method="post" action="{{ route('front.provider.account.update') }}" class="mt-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-x-8 gap-y-4 lg:grid-cols-2 lg:gap-y-5 lg:items-start">
                        <div>
                            <label for="account_first_name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('front::auth.first_name') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400 text-sm" aria-hidden="true"></i>
                                </div>
                                <input id="account_first_name" name="first_name" type="text" value="{{ old('first_name', $user->first_name) }}" autocomplete="given-name" required
                                    class="appearance-none block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-gray-900 outline-none focus:border-red-400 focus:ring-2 focus:ring-red-200 sm:text-sm transition">
                            </div>
                            @error('first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="account_service_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('front::auth.service') }}</label>
                            <select name="service_id" id="account_service_id" required
                                class="front-auth-select2 block w-full py-2 px-3 border border-gray-300 rounded-lg text-gray-900 sm:text-sm outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-200"
                                data-placeholder="{{ __('front::auth.service') }}">
                                <option value=""></option>
                                @foreach ($registerServices as $svc)
                                    <option value="{{ $svc['id'] }}" @selected(old('service_id', $user->service_id) == $svc['id'])>{{ $svc['name'] }}</option>
                                @endforeach
                            </select>
                            @error('service_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="account_last_name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('front::auth.last_name') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400 text-sm" aria-hidden="true"></i>
                                </div>
                                <input id="account_last_name" name="last_name" type="text" value="{{ old('last_name', $user->last_name) }}" autocomplete="family-name" required
                                    class="appearance-none block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-gray-900 outline-none focus:border-red-400 focus:ring-2 focus:ring-red-200 sm:text-sm transition">
                            </div>
                            @error('last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div
                            class="js-provider-location-search account-provider-location-search grid grid-cols-1 gap-4 sm:grid-cols-2"
                            data-states-list-url="{{ route('zms.states.list') }}"
                            data-cities-list-url="{{ route('zms.cities.list') }}"
                            data-default-country-id="{{ $frontSearchDefaultCountryId }}"
                            data-locale="{{ app()->getLocale() }}"
                            data-selected-state-id="{{ $accountOldStateId }}"
                            data-selected-state-name="{{ $accountOldStateName }}"
                            data-selected-city-id="{{ $accountOldCityId }}"
                            data-selected-city-name="{{ $accountOldCityName }}"
                        >
                            <div>
                                <label for="account_state_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('front::auth.state') }}</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                        <i class="fas fa-map text-gray-400 text-sm" aria-hidden="true"></i>
                                    </div>
                                    <select name="state_id" id="account_state_id" required
                                        class="js-pls-state block w-full py-2 pl-10 pr-3 border border-gray-300 rounded-lg text-gray-900 sm:text-sm outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-200">
                                        <option value="">{{ __('front::home.state_placeholder') }}</option>
                                    </select>
                                </div>
                                @error('state_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="account_city_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('front::auth.city') }}</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                        <i class="fas fa-map-marker-alt text-gray-400 text-sm" aria-hidden="true"></i>
                                    </div>
                                    <select name="city_id" id="account_city_id" required
                                        class="js-pls-city block w-full py-2 pl-10 pr-3 border border-gray-300 rounded-lg text-gray-900 sm:text-sm outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-200"
                                        @disabled($accountOldStateId === '')>
                                        <option value="">{{ __('front::home.city_placeholder') }}</option>
                                    </select>
                                </div>
                                @error('city_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="lg:col-span-2">
                            <label for="account_email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('front::auth.email') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400 text-sm" aria-hidden="true"></i>
                                </div>
                                <input id="account_email" name="email" type="email" value="{{ old('email', $user->email) }}" autocomplete="email" required
                                    class="appearance-none block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-gray-900 outline-none focus:border-red-400 focus:ring-2 focus:ring-red-200 sm:text-sm transition">
                            </div>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-gray-50/80 px-4 py-4 sm:px-5">
                        <h3 class="text-sm font-semibold text-gray-900">{{ __('front::provider_account.readonly_phones_title') }}</h3>
                        <p class="mt-1 text-sm text-gray-600">{{ __('front::provider_account.readonly_phones_help') }}</p>
                        <dl class="mt-3 grid gap-3 sm:grid-cols-2">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('front::auth.phone_number') }}</dt>
                                <dd class="mt-1 text-sm font-medium text-gray-900">{{ $user->phone_number ?: __('front::provider_dashboard.value_emdash') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('front::provider_account.central_phone_label') }}</dt>
                                <dd class="mt-1 text-sm font-medium text-gray-900">{{ filled($user->central_phone) ? $user->central_phone : __('front::provider_dashboard.value_emdash') }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-full bg-red-600 px-6 py-2.5 text-sm font-bold text-white shadow transition hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
                            <i class="fas fa-save" aria-hidden="true"></i> {{ __('front::provider_account.save_profile') }}
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm ring-1 ring-black/5" aria-labelledby="password-section-heading">
            <div class="border-l-4 border-amber-500 bg-gradient-to-br from-amber-50/40 via-white to-white px-5 py-6 sm:px-7 sm:py-7">
                <h2 id="password-section-heading" class="text-xl font-bold tracking-tight text-gray-900">{{ __('front::provider_account.section_password') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('front::provider_account.section_password_intro') }}</p>

                <form method="post" action="{{ route('front.provider.account.password') }}" class="mt-6 space-y-4 max-w-xl">
                    @csrf
                    <div>
                        <label for="old_password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('front::provider_account.old_password') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400 text-sm" aria-hidden="true"></i>
                            </div>
                            <input id="old_password" name="old_password" type="password" autocomplete="current-password" required
                                class="appearance-none block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg text-gray-900 outline-none focus:border-red-400 focus:ring-2 focus:ring-red-200 sm:text-sm transition"
                                value="{{ old('old_password') }}">
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600" data-auth-password-toggle aria-controls="old_password">
                                <i class="fas fa-eye text-sm" aria-hidden="true"></i>
                            </button>
                        </div>
                        @error('old_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('front::provider_account.new_password') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400 text-sm" aria-hidden="true"></i>
                            </div>
                            <input id="new_password" name="new_password" type="password" autocomplete="new-password" required
                                class="appearance-none block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg text-gray-900 outline-none focus:border-red-400 focus:ring-2 focus:ring-red-200 sm:text-sm transition">
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600" data-auth-password-toggle aria-controls="new_password">
                                <i class="fas fa-eye text-sm" aria-hidden="true"></i>
                            </button>
                        </div>
                        @error('new_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">{{ __('front::auth.password_confirmation') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400 text-sm" aria-hidden="true"></i>
                            </div>
                            <input id="new_password_confirmation" name="new_password_confirmation" type="password" autocomplete="new-password" required
                                class="appearance-none block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg text-gray-900 outline-none focus:border-red-400 focus:ring-2 focus:ring-red-200 sm:text-sm transition">
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600" data-auth-password-toggle aria-controls="new_password_confirmation">
                                <i class="fas fa-eye text-sm" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-full bg-amber-600 px-6 py-2.5 text-sm font-bold text-white shadow transition hover:bg-amber-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2">
                        <i class="fas fa-key" aria-hidden="true"></i> {{ __('front::provider_account.save_password') }}
                    </button>
                </form>
            </div>
        </section>
    </div>
@endsection

@push('script')
    <script src="{{ asset('modules/front/js/front-auth.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.jQuery && jQuery.fn.select2) {
                jQuery('.front-auth-select2').select2({
                    width: '100%',
                    dropdownParent: jQuery(document.body),
                });
            }
        });
    </script>
@endpush
