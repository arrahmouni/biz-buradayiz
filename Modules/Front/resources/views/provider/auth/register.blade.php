@extends('front::layouts.auth')

@section('content')
    <div class="flex min-h-screen items-start justify-center py-8 px-4 sm:px-6 lg:items-center lg:py-10 lg:px-8">
        <div class="front-auth-card-panel front-auth-register-card w-full max-w-5xl space-y-6 bg-white rounded-2xl shadow-xl p-6 sm:p-8 lg:p-10">
            @include('front::provider.auth.partials.card-header', [
                'title' => __('front::auth.register_title'),
                'subtitle' => __('front::auth.register_subtitle'),
            ])

            <p class="text-center text-sm">
                <a href="{{ route('front.provider.register') }}" class="font-medium text-red-600 hover:text-red-500 transition">
                    {{ __('front::provider_register.form_back_to_landing') }}
                </a>
            </p>

            @php
                $registerPhoneDisplay = '';
                $registerOldPhone = old('phone_number');
                if ($registerOldPhone !== null && $registerOldPhone !== '') {
                    $digits = preg_replace('/\D+/', '', (string) $registerOldPhone) ?? '';
                    if (str_starts_with($digits, '90') && strlen($digits) >= 12) {
                        $registerPhoneDisplay = '0'.substr($digits, 2);
                    } elseif (str_starts_with($digits, '0')) {
                        $registerPhoneDisplay = $digits;
                    } elseif (str_starts_with($digits, '5') && strlen($digits) === 10) {
                        $registerPhoneDisplay = '0'.$digits;
                    } else {
                        $registerPhoneDisplay = $digits;
                    }
                }

                $registerOldStateId = (string) old('state_id', '');
                $registerOldCityId = (string) old('city_id', '');
                $registerOldStateName = '';
                $registerOldCityName = '';
                if ($registerOldCityId !== '') {
                    $registerOldCity = \Modules\Zms\Models\City::query()->with(['state'])->find($registerOldCityId);
                    if ($registerOldCity) {
                        $registerOldCityName = (string) $registerOldCity->name;
                        $registerOldStateId = (string) $registerOldCity->state_id;
                        $registerOldStateName = (string) ($registerOldCity->state?->name ?? '');
                    }
                } elseif ($registerOldStateId !== '') {
                    $registerOldState = \Modules\Zms\Models\State::query()->find($registerOldStateId);
                    $registerOldStateName = (string) ($registerOldState?->name ?? '');
                }
            @endphp

            <form id="front-provider-register-form" class="mt-6" method="post" action="{{ route('front.provider.register.store') }}" enctype="multipart/form-data">
                @csrf
                {{-- Row-based grid: each row is [left field | right field] on lg so labels and inputs align horizontally --}}
                <div class="grid grid-cols-1 gap-x-8 gap-y-4 lg:grid-cols-2 lg:gap-y-5 lg:items-start">
                    <div class="front-auth-register-field">
                        <label for="personal_photo" class="block text-sm font-medium text-gray-700 mb-1">{{ __('front::auth.personal_photo') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                <i class="fas fa-camera text-gray-400 text-sm" aria-hidden="true"></i>
                            </div>
                            <input id="personal_photo" name="personal_photo" type="file" accept="{{ getImageTypes(allowSvg: false) }}" required
                                class="front-auth-file-input">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">{{ __('front::auth.photo_types_hint', ['size' => config('base.file.image.max_size')]) }}</p>
                        @error('personal_photo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="front-auth-register-field">
                        <label for="service_image" class="block text-sm font-medium text-gray-700 mb-1">{{ __('front::auth.service_image') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                <i class="fas fa-images text-gray-400 text-sm" aria-hidden="true"></i>
                            </div>
                            <input id="service_image" name="service_image" type="file" accept="{{ getImageTypes(allowSvg: false) }}" required
                                class="front-auth-file-input">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">{{ __('front::auth.photo_types_hint', ['size' => config('base.file.image.max_size')]) }}</p>
                        @error('service_image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="front-auth-register-field lg:col-span-2">
                        <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('front::auth.company_name') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-building text-gray-400 text-sm" aria-hidden="true"></i>
                            </div>
                            <input id="company_name" name="company_name" type="text" value="{{ old('company_name') }}" autocomplete="organization" required
                                class="appearance-none block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-gray-900 outline-none focus:border-red-400 focus:ring-2 focus:ring-red-200 sm:text-sm transition">
                        </div>
                        @error('company_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="front-auth-register-field">
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('front::auth.first_name') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400 text-sm" aria-hidden="true"></i>
                            </div>
                            <input id="first_name" name="first_name" type="text" value="{{ old('first_name') }}" autocomplete="given-name" required
                                class="appearance-none block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-gray-900 outline-none focus:border-red-400 focus:ring-2 focus:ring-red-200 sm:text-sm transition">
                        </div>
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="front-auth-register-field">
                        <label for="service_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('front::auth.service') }}</label>
                        <select name="service_id" id="service_id" required
                            class="front-auth-select2 block w-full py-2 px-3 border border-gray-300 rounded-lg text-gray-900 sm:text-sm outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-200"
                            data-placeholder="{{ __('front::auth.service') }}">
                            <option value=""></option>
                            @foreach ($registerServices as $svc)
                                <option value="{{ $svc['id'] }}" @selected(old('service_id') == $svc['id'])>{{ $svc['name'] }}</option>
                            @endforeach
                        </select>
                        @error('service_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="front-auth-register-field">
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('front::auth.last_name') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400 text-sm" aria-hidden="true"></i>
                            </div>
                            <input id="last_name" name="last_name" type="text" value="{{ old('last_name') }}" autocomplete="family-name" required
                                class="appearance-none block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-gray-900 outline-none focus:border-red-400 focus:ring-2 focus:ring-red-200 sm:text-sm transition">
                        </div>
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div
                        class="js-provider-location-search front-auth-register-field grid grid-cols-1 gap-4 sm:grid-cols-2"
                        data-states-list-url="{{ route('zms.states.list') }}"
                        data-cities-list-url="{{ route('zms.cities.list') }}"
                        data-default-country-id="{{ $frontSearchDefaultCountryId }}"
                        data-locale="{{ app()->getLocale() }}"
                        data-selected-state-id="{{ $registerOldStateId }}"
                        data-selected-state-name="{{ $registerOldStateName }}"
                        data-selected-city-id="{{ $registerOldCityId }}"
                        data-selected-city-name="{{ $registerOldCityName }}"
                    >
                        <div>
                            <label for="register_state_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('front::auth.state') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                    <i class="fas fa-map text-gray-400 text-sm" aria-hidden="true"></i>
                                </div>
                                <select name="state_id" id="register_state_id" required
                                    class="js-pls-state block w-full py-2 pl-10 pr-3 border border-gray-300 rounded-lg text-gray-900 sm:text-sm outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-200">
                                    <option value="">{{ __('front::home.state_placeholder') }}</option>
                                </select>
                            </div>
                            @error('state_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="register_city_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('front::auth.city') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                    <i class="fas fa-map-marker-alt text-gray-400 text-sm" aria-hidden="true"></i>
                                </div>
                                <select name="city_id" id="register_city_id" required
                                    class="js-pls-city block w-full py-2 pl-10 pr-3 border border-gray-300 rounded-lg text-gray-900 sm:text-sm outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-200"
                                    @disabled($registerOldStateId === '')>
                                    <option value="">{{ __('front::home.city_placeholder') }}</option>
                                </select>
                            </div>
                            @error('city_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="front-auth-register-field">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('front::auth.email') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400 text-sm" aria-hidden="true"></i>
                            </div>
                            <input id="email" name="email" type="text" value="{{ old('email') }}" autocomplete="email" required
                                inputmode="email" autocapitalize="none" spellcheck="false"
                                class="js-front-provider-register-email-mask appearance-none block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-gray-900 outline-none focus:border-red-400 focus:ring-2 focus:ring-red-200 sm:text-sm transition">
                        </div>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="front-auth-register-field">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('front::auth.password') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400 text-sm" aria-hidden="true"></i>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="new-password" required
                                class="appearance-none block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg text-gray-900 outline-none focus:border-red-400 focus:ring-2 focus:ring-red-200 sm:text-sm transition">
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600" data-auth-password-toggle aria-controls="password">
                                <i class="fas fa-eye text-sm" aria-hidden="true"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="front-auth-register-field">
                        <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">{{ __('front::auth.phone_number') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-phone text-gray-400 text-sm" aria-hidden="true"></i>
                            </div>
                            <input id="phone_number" name="phone_number" type="tel" value="{{ $registerPhoneDisplay }}" autocomplete="tel" required
                                class="js-front-provider-register-phone-tr appearance-none block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-gray-900 outline-none focus:border-red-400 focus:ring-2 focus:ring-red-200 sm:text-sm transition">
                        </div>
                        @error('phone_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="front-auth-register-field">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">{{ __('front::auth.password_confirmation') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400 text-sm" aria-hidden="true"></i>
                            </div>
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                                class="appearance-none block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg text-gray-900 outline-none focus:border-red-400 focus:ring-2 focus:ring-red-200 sm:text-sm transition">
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600" data-auth-password-toggle aria-controls="password_confirmation">
                                <i class="fas fa-eye text-sm" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="group relative mt-8 w-full flex justify-center items-center py-2.5 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition shadow-md">
                    <i class="fas fa-user-plus mr-2" aria-hidden="true"></i>
                    {{ __('front::auth.submit_register') }}
                </button>
            </form>

            <div class="text-center space-y-3 text-sm pt-2">
                <p class="text-gray-600">
                    {{ __('front::auth.login_prompt') }}
                    <a href="{{ route('front.provider.login') }}" class="font-medium text-red-600 hover:text-red-500 transition">{{ __('front::auth.link_login') }}</a>
                </p>
                <span>
                    <a href="{{ route('front.index') }}" class="font-medium text-gray-500 hover:text-gray-700 transition">{{ __('front::auth.link_home') }}</a>
                </span>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.9/dist/inputmask.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.jQuery && jQuery.fn.select2) {
                jQuery('.front-auth-select2').select2({
                    width: '100%',
                    dropdownParent: jQuery(document.body),
                });
            }

            if (typeof Inputmask === 'undefined') {
                return;
            }

            var emailEl = document.getElementById('email');
            if (emailEl) {
                Inputmask({
                    mask: '*{1,20}[.*{1,20}][.*{1,20}][.*{1,20}]@*{1,20}[.*{2,6}][.*{1,2}]',
                    greedy: false,
                    onBeforePaste: function (pastedValue) {
                        pastedValue = String(pastedValue).toLowerCase();
                        return pastedValue.replace(/^mailto:/i, '');
                    },
                    definitions: {
                        '*': {
                            validator: '[0-9A-Za-z!#$%&"*+/=?^_`{|}~-]',
                            cardinality: 1,
                            casing: 'lower',
                        },
                    },
                }).mask(emailEl);
            }

            var phoneEl = document.getElementById('phone_number');
            if (phoneEl) {
                Inputmask({
                    mask: '0 (599) 999 99 99',
                    placeholder: '_',
                    clearIncomplete: true,
                    onBeforePaste: function (pastedValue) {
                        var digits = String(pastedValue).replace(/\D/g, '');
                        if (digits.startsWith('90') && digits.length >= 12) {
                            return '0' + digits.slice(2);
                        }
                        if (digits.startsWith('5') && digits.length === 10) {
                            return '0' + digits;
                        }
                        return pastedValue;
                    },
                }).mask(phoneEl);
            }
        });
    </script>
@endpush
