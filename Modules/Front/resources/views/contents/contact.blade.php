@extends('front::layouts.master')

@php
    $contactFieldClass = 'w-full border border-gray-300 rounded-lg px-4 py-2 transition hover:border-gray-400 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-400/30';
@endphp

@section('content')
    <x-front::page-hero
        :heading="__('front::home.contact_hero_title')"
        :breadcrumb-label="__('front::home.contact_breadcrumb_label')"
    >
        <x-slot name="breadcrumb">
            <a href="{{ route('front.index') }}" class="hover:text-red-400 transition">{{ __('front::home.nav_home') }}</a>
            <span class="mx-2">/</span>
            <span class="text-white font-medium">{{ __('front::home.contact_heading') }}</span>
        </x-slot>
        <x-slot name="belowDivider">
            <p class="text-gray-300 mt-4 text-lg max-w-2xl">
                {{ __('front::home.contact_intro') }}
            </p>
        </x-slot>
    </x-front::page-hero>

    <section class="bg-gray-50 py-12 md:py-16">
        <div class="container mx-auto px-5 lg:px-8">
            @if ($errors->has('form'))
                <div class="mb-8 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800" role="alert">
                    {{ $errors->first('form') }}
                </div>
            @endif

            <div class="grid lg:grid-cols-2 gap-12">
                <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ __('front::home.contact_form_title') }}</h2>

                    @if (session('success'))
                        <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="status">
                            <i class="fas fa-check-circle mr-2" aria-hidden="true"></i>{{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('front.contact.store') }}" method="POST">
                        @csrf
                        <div class="grid md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="first_name" class="block text-gray-700 font-semibold mb-1">
                                    {{ __('front::home.contact_first_name') }} <span class="text-red-600">{{ __('front::home.contact_required_mark') }}</span>
                                </label>
                                <input
                                    type="text"
                                    name="first_name"
                                    id="first_name"
                                    value="{{ old('first_name') }}"
                                    required
                                    autocomplete="given-name"
                                    class="{{ $contactFieldClass }} @error('first_name') border-red-500 @enderror"
                                >
                                @error('first_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="last_name" class="block text-gray-700 font-semibold mb-1">
                                    {{ __('front::home.contact_last_name') }} <span class="text-red-600">{{ __('front::home.contact_required_mark') }}</span>
                                </label>
                                <input
                                    type="text"
                                    name="last_name"
                                    id="last_name"
                                    value="{{ old('last_name') }}"
                                    required
                                    autocomplete="family-name"
                                    class="{{ $contactFieldClass }} @error('last_name') border-red-500 @enderror"
                                >
                                @error('last_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 font-semibold mb-1">
                                {{ __('front::home.contact_email') }} <span class="text-red-600">{{ __('front::home.contact_required_mark') }}</span>
                            </label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="email"
                                class="{{ $contactFieldClass }} @error('email') border-red-500 @enderror"
                            >
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="phone" class="block text-gray-700 font-semibold mb-1">
                                {{ __('front::home.contact_phone') }} <span class="text-red-600">{{ __('front::home.contact_required_mark') }}</span>
                            </label>
                            <input
                                type="tel"
                                name="phone"
                                id="phone"
                                value="{{ old('phone') }}"
                                required
                                autocomplete="tel"
                                class="{{ $contactFieldClass }} @error('phone') border-red-500 @enderror"
                            >
                            @error('phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="message" class="block text-gray-700 font-semibold mb-1">
                                {{ __('front::home.contact_message') }} <span class="text-red-600">{{ __('front::home.contact_required_mark') }}</span>
                            </label>
                            <textarea
                                name="message"
                                id="message"
                                rows="5"
                                required
                                class="{{ $contactFieldClass }} resize-y @error('message') border-red-500 @enderror"
                            >{{ old('message') }}</textarea>
                            @error('message')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition shadow-md hover:shadow-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2">
                            <i class="fas fa-paper-plane mr-2" aria-hidden="true"></i>{{ __('front::home.contact_submit') }}
                        </button>
                    </form>
                </div>

                @php
                    $sitePhone = trim((string) (getSetting('phone') ?? ''));
                    $siteEmail = trim((string) (getSetting('email') ?? ''));
                    $siteAddress = trim((string) (getSetting('address') ?? ''));
                    $emergencyContactNumber = trim((string) (getSetting('emergency_contact_number') ?? ''));
                    $contactMapEmbedUrl = trim((string) (getSetting('contact_map_embed_url') ?? ''));
                    $hqHasContact = $sitePhone !== '' || $siteEmail !== '' || $siteAddress !== '' || $emergencyContactNumber !== '';
                @endphp

                <div class="space-y-6">
                    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-map-marker-alt text-red-600" aria-hidden="true"></i>
                            {{ __('front::home.contact_headquarters_title') }}
                        </h3>
                        <div class="space-y-3 text-gray-600">
                            <p class="flex items-start gap-2">
                                <i class="fas fa-building w-5 text-red-500 shrink-0 mt-0.5" aria-hidden="true"></i>
                                <span>{{ __('front::home.brand') }}</span>
                            </p>
                            @if ($siteAddress !== '')
                                <p class="flex items-start gap-2">
                                    <i class="fas fa-location-dot w-5 text-red-500 shrink-0 mt-0.5" aria-hidden="true"></i>
                                    <span class="whitespace-pre-line">{{ $siteAddress }}</span>
                                </p>
                            @endif
                            @if ($emergencyContactNumber !== '')
                                <p class="flex items-start gap-2">
                                    <i class="fas fa-phone w-5 text-red-500 shrink-0 mt-0.5" aria-hidden="true"></i>
                                    <span>
                                        {{ __('front::home.contact_hq_emergency_label') }}:
                                        <a href="{{ phoneToTelHref($emergencyContactNumber) }}" class="text-red-600 font-semibold hover:text-red-700 transition">{{ $emergencyContactNumber }}</a>
                                    </span>
                                </p>
                            @endif
                            @if ($sitePhone !== '' && $sitePhone !== $emergencyContactNumber)
                                <p class="flex items-start gap-2">
                                    <i class="fas fa-phone-volume w-5 text-red-500 shrink-0 mt-0.5" aria-hidden="true"></i>
                                    <span>
                                        {{ __('front::home.contact_details_phone') }}:
                                        <a href="{{ phoneToTelHref($sitePhone) }}" class="text-red-600 font-semibold hover:text-red-700 transition">{{ $sitePhone }}</a>
                                    </span>
                                </p>
                            @elseif ($sitePhone !== '' && $emergencyContactNumber === '')
                                <p class="flex items-start gap-2">
                                    <i class="fas fa-phone w-5 text-red-500 shrink-0 mt-0.5" aria-hidden="true"></i>
                                    <a href="{{ phoneToTelHref($sitePhone) }}" class="text-red-600 font-semibold hover:text-red-700 transition">{{ $sitePhone }}</a>
                                </p>
                            @endif
                            @if ($siteEmail !== '')
                                <p class="flex items-start gap-2">
                                    <i class="fas fa-envelope w-5 text-red-500 shrink-0 mt-0.5" aria-hidden="true"></i>
                                    <a href="mailto:{{ $siteEmail }}" class="text-red-600 hover:text-red-700 transition break-all">{{ $siteEmail }}</a>
                                </p>
                            @endif
                            {{-- @if (! $hqHasContact)
                                <p class="text-sm text-gray-500">{{ __('front::home.contact_details_empty') }}</p>
                            @endif --}}
                        </div>
                    </div>

                    {{-- <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-clock text-red-600" aria-hidden="true"></i>
                            {{ __('front::home.contact_support_hours_title') }}
                        </h3>
                        <ul class="space-y-2 text-gray-600">
                            <li class="flex justify-between gap-4">
                                <span>{{ __('front::home.contact_hours_emergency_label') }}</span>
                                <span class="font-semibold shrink-0">{{ __('front::home.contact_hours_emergency_value') }}</span>
                            </li>
                            <li class="flex justify-between gap-4">
                                <span>{{ __('front::home.contact_hours_customer_label') }}</span>
                                <span class="shrink-0 text-right">{{ __('front::home.contact_hours_customer_value') }}</span>
                            </li>
                            <li class="flex justify-between gap-4">
                                <span>{{ __('front::home.contact_hours_chat_label') }}</span>
                                <span class="font-semibold shrink-0">{{ __('front::home.contact_hours_chat_value') }}</span>
                            </li>
                        </ul>
                    </div> --}}

                    @if ($contactMapEmbedUrl !== '')
                        <div class="rounded-2xl overflow-hidden shadow-lg h-64 bg-gray-200">
                            <iframe
                                class="w-full h-full border-0"
                                src="{{ $contactMapEmbedUrl }}"
                                title="{{ __('front::home.contact_headquarters_title') }}"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                            ></iframe>
                        </div>
                    @else
                        <div class="bg-gray-300 rounded-2xl overflow-hidden shadow-lg h-64 flex items-center justify-center text-gray-500">
                            <div class="text-center px-4">
                                <i class="fas fa-map-marked-alt text-3xl mb-2" aria-hidden="true"></i>
                                <p>{{ __('front::home.contact_map_placeholder') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
