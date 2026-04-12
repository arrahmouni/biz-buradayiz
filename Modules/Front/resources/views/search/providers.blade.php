@extends('front::layouts.master')

@php
    $heroServiceLabel = __('front::home.search_results_hero_any_service');
    if (! empty($filters['service_id'])) {
        $matchedService = collect($frontPublicFilterServices)->firstWhere('id', (int) $filters['service_id']);
        if ($matchedService && filled($matchedService['name'] ?? null)) {
            $heroServiceLabel = $matchedService['name'];
        }
    }

    $heroLocationLabel = __('front::home.search_results_hero_any_location');
    if ($selectedCity && filled(data_get($selectedCity, 'name'))) {
        $heroLocationLabel = $selectedCity['name'];
        if ($selectedState && filled(data_get($selectedState, 'name'))) {
            $heroLocationLabel .= ', '.$selectedState['name'];
        }
    } elseif ($selectedState && filled(data_get($selectedState, 'name'))) {
        $heroLocationLabel = $selectedState['name'];
    }

    $emergencyContactNumber = getSetting('emergency_contact_number');
    $hasEmergencyLine = filled(trim((string) ($emergencyContactNumber ?? '')));
@endphp

@section('content')
    <x-front::page-hero
        :heading="__('front::home.search_results_title')"
        :breadcrumb-label="__('front::home.search_results_breadcrumb_label')"
    >
        <x-slot name="breadcrumb">
            <a href="{{ route('front.index') }}" class="hover:text-red-400 transition">{{ __('front::home.nav_home') }}</a>
            <span class="mx-2">/</span>
            <span class="text-white font-medium">{{ __('front::home.search_results_title') }}</span>
        </x-slot>
        <x-slot name="belowDivider">
            <p class="text-gray-300 mt-4 text-base md:text-lg">
                {{ __('front::home.search_results_hero_showing') }}
                <strong class="text-white">{{ $heroServiceLabel }}</strong>
                {{ __('front::home.search_results_hero_in') }}
                <strong class="text-white">{{ $heroLocationLabel }}</strong>
            </p>
        </x-slot>
    </x-front::page-hero>

    <section class="bg-gray-50 py-8 md:py-12">
        <div class="container mx-auto px-5 lg:px-8">
            <div class="bg-white rounded-2xl shadow-md p-5 md:p-6 mb-8">
                <x-front::provider-location-search-form
                    form-id="providerSearchFiltersForm"
                    form-class="provider-search-filters flex flex-col lg:flex-row gap-4 items-end"
                    :selected-service-id="$filters['service_id'] ?? null"
                    :selected-state="$selectedState"
                    :selected-city="$selectedCity"
                />
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                @if ($hasEmergencyLine)
                    <aside class="lg:w-1/4 space-y-6 order-2 lg:order-1">
                        <div class="bg-red-50 rounded-2xl p-5 border border-red-100">
                            <i class="fas fa-headset text-red-500 text-2xl mb-2" aria-hidden="true"></i>
                            <h2 class="font-bold text-gray-800">{{ __('front::home.search_sidebar_help_title') }}</h2>
                            <p class="text-sm text-gray-600 mt-1">{{ __('front::home.search_sidebar_help_text') }}</p>
                            <a href="{{ phoneToTelHref(trim((string) $emergencyContactNumber)) }}" class="inline-block mt-2 text-red-600 font-semibold text-sm hover:text-red-700">
                                {{ trim((string) $emergencyContactNumber) }}
                            </a>
                        </div>
                    </aside>
                @endif

                <div class="{{ $hasEmergencyLine ? 'lg:w-3/4' : 'lg:w-full' }} order-1 lg:order-2 min-w-0">
                    @if ($providers->isEmpty())
                        @include('front::includes.empty-state', [
                            'text' => __('front::home.search_results_empty'),
                            'icon' => 'fas fa-hard-hat',
                        ])
                    @else
                        <div class="flex flex-wrap justify-between items-center gap-3 mb-4">
                            <p class="text-gray-600 text-sm">
                                <i class="fas fa-list-ul text-red-500" aria-hidden="true"></i>
                                {{ trans_choice('front::home.search_results_count', $providers->total(), ['count' => $providers->total()]) }}
                            </p>
                        </div>

                        <div class="space-y-3 sm:space-y-5">
                            @foreach ($providers as $provider)
                                <article class="bg-white rounded-2xl shadow-md hover:shadow-lg transition p-4 sm:p-5 border border-gray-100">
                                    <div class="flex flex-row gap-3 sm:gap-5">
                                        <div class="h-14 w-14 sm:h-32 sm:w-32 bg-gray-100 rounded-full sm:rounded-xl overflow-hidden flex-shrink-0">
                                            <img src="{{ $provider->image_url }}" alt="{{ $provider->full_name }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-wrap justify-between items-start gap-2">
                                                <h2 class="text-base sm:text-xl font-bold text-gray-800 leading-snug">
                                                    <a href="{{ route('front.provider.show', ['provider' => $provider->frontProfileSlug()]) }}" class="text-gray-800 hover:text-red-600 transition">
                                                        {{ $provider->full_name }}
                                                    </a>
                                                </h2>
                                                @if ($provider->service && filled($provider->service->icon))
                                                    <span class="text-red-600 shrink-0" aria-hidden="true"><i class="{{ $provider->service->icon }} text-lg sm:text-xl"></i></span>
                                                @endif
                                            </div>
                                            <div class="flex flex-wrap items-center gap-x-2 sm:gap-x-3 gap-y-1 text-xs sm:text-sm text-gray-500 mt-0.5 sm:mt-1">
                                                @if ((int) $provider->approved_reviews_count > 0)
                                                    <span>
                                                        <i class="fas fa-star text-yellow-400" aria-hidden="true"></i>
                                                        {{ number_format((float) $provider->review_rating_average, 1) }}
                                                        ({{ trans_choice('front::home.provider_card_reviews', $provider->approved_reviews_count, ['count' => $provider->approved_reviews_count]) }})
                                                    </span>
                                                @else
                                                    <span>{{ __('front::home.provider_card_no_reviews') }}</span>
                                                @endif
                                                @if (filled($provider->provider_card_location_line))
                                                    <span><i class="fas fa-map-marker-alt text-red-400" aria-hidden="true"></i> {{ $provider->provider_card_location_line }}</span>
                                                @endif
                                            </div>
                                            @if (filled($provider->provider_card_service_description))
                                                <p class="text-gray-600 text-xs sm:text-sm mt-1.5 sm:mt-2 line-clamp-2 sm:line-clamp-none">{{ \Illuminate\Support\Str::limit(strip_tags($provider->provider_card_service_description), 220) }}</p>
                                            @endif
                                            <div class="mt-2 sm:mt-3 flex flex-wrap gap-1.5 sm:gap-2 items-center">
                                                <a href="{{ route('front.provider.show', ['provider' => $provider->frontProfileSlug()]) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-red-600 hover:text-red-700 transition">
                                                    {{ __('front::home.provider_detail_view_profile') }}
                                                    <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i>
                                                </a>
                                                <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">
                                                    <i class="fas fa-wrench" aria-hidden="true"></i> {{ $provider->provider_card_service_name }}
                                                </span>
                                                @if (filled($provider->email))
                                                    <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full truncate max-w-full">
                                                        <i class="fas fa-envelope" aria-hidden="true"></i> {{ $provider->email }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @if (filled($provider->central_phone))
                                        @php
                                            $telHref = phoneToTelHref(trim((string) $provider->central_phone));
                                        @endphp
                                        <div class="mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-gray-200">
                                            <div class="rounded-xl border border-red-100 bg-gradient-to-br from-red-50 via-white to-red-50/60 shadow-sm p-3 sm:p-4 md:p-5 flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-6 sm:justify-between">
                                                <div class="flex items-start gap-3 sm:gap-4 min-w-0 flex-1">
                                                    <div class="flex h-10 w-10 sm:h-12 sm:w-12 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600 ring-2 sm:ring-4 ring-white shadow-sm" aria-hidden="true">
                                                        <i class="fas fa-phone-alt text-base sm:text-lg"></i>
                                                    </div>
                                                    <div class="min-w-0 pt-0.5">
                                                        <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-red-700/90">{{ __('front::home.provider_card_call_now') }}</p>
                                                        <a href="{{ $telHref }}" class="mt-0.5 sm:mt-1 block text-lg sm:text-2xl md:text-3xl font-bold text-gray-900 hover:text-red-700 tracking-tight break-all leading-snug transition-colors">
                                                            {{ trim((string) $provider->central_phone) }}
                                                        </a>
                                                    </div>
                                                </div>
                                                <a href="{{ $telHref }}" class="inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 sm:px-6 sm:py-3 rounded-full text-xs sm:text-sm font-semibold transition shadow-md hover:shadow-lg shrink-0 sm:min-w-[10rem]">
                                                    <i class="fas fa-phone-alt" aria-hidden="true"></i> {{ __('front::home.provider_card_call_provider') }}
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </article>
                            @endforeach
                        </div>

                        @if ($providers->hasPages())
                            @php
                                $start = max($providers->currentPage() - 2, 1);
                                $end = min($start + 4, $providers->lastPage());
                                $start = max($end - 4, 1);
                            @endphp
                            <div class="mt-8 flex justify-center">
                                <nav class="flex flex-wrap items-center gap-2" aria-label="{{ __('front::home.search_pagination_label') }}">
                                    @if (! $providers->onFirstPage())
                                        <a href="{{ $providers->url($providers->currentPage() - 1) }}" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
                                            {{ __('front::home.blog_pagination_prev') }}
                                        </a>
                                    @endif
                                    @for ($page = $start; $page <= $end; $page++)
                                        @if ($page === $providers->currentPage())
                                            <span class="px-3 py-2 bg-red-600 text-white rounded-md">{{ $page }}</span>
                                        @else
                                            <a href="{{ $providers->url($page) }}" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">{{ $page }}</a>
                                        @endif
                                    @endfor
                                    @if ($providers->hasMorePages())
                                        <a href="{{ $providers->url($providers->currentPage() + 1) }}" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
                                            {{ __('front::home.blog_pagination_next') }}
                                        </a>
                                    @endif
                                </nav>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
