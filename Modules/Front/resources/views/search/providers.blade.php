@extends('front::layouts.master')

@php
    $heroServiceLabel = __('front::home.search_results_hero_any_service');
    $selectedService = null;
    $selectedServiceHiddenFromFilters = false;
    if (! empty($filters['service_id'])) {
        $selectedService = collect($frontPublicServices)->firstWhere('id', (int) $filters['service_id']);
        $matchedService = collect($frontPublicFilterServices)->firstWhere('id', (int) $filters['service_id']);
        if ($matchedService && filled($matchedService['name'] ?? null)) {
            $heroServiceLabel = $matchedService['name'];
        } elseif ($selectedService && filled($selectedService['name'] ?? null)) {
            $heroServiceLabel = $selectedService['name'];
        }

        $selectedServiceHiddenFromFilters = $selectedService !== null && $matchedService === null;
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

    $emergencyContactNumber = getSetting(\Modules\Config\Constatnt::EMERGENCY_CONTACT_NUMBER);
    $hasEmergencyLine = filled(trim((string) ($emergencyContactNumber ?? '')));
    $appStoreUrl = trim((string) (getSetting(\Modules\Config\Constatnt::APP_STORE, '') ?? ''));
    $googlePlayUrl = trim((string) (getSetting(\Modules\Config\Constatnt::GOOGLE_PLAY, '') ?? ''));
    $hasAppLinks = $appStoreUrl !== '' || $googlePlayUrl !== '';
@endphp

@section('content')
    <div class="hidden sm:block">
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
    </div>

    <section class="bg-gray-50 py-8 md:py-12">
        <div class="container mx-auto px-5 lg:px-8">
            @if ($selectedServiceHiddenFromFilters)
                <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 md:p-5">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-mobile-alt mt-0.5 text-amber-600" aria-hidden="true"></i>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-amber-900">
                                {{ __('front::home.search_hidden_service_notice_title', ['service' => data_get($selectedService, 'name', __('front::home.search_results_hero_any_service'))]) }}
                            </p>
                            <p class="mt-1 text-sm text-amber-800">
                                {{ __('front::home.search_hidden_service_notice_body') }}
                            </p>
                            @if ($hasAppLinks)
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @if ($appStoreUrl !== '')
                                        <a
                                            href="{{ $appStoreUrl }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center gap-2.5 rounded-full bg-gray-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-800"
                                        >
                                            <i class="fab fa-app-store-ios text-base mr-1" aria-hidden="true"></i>
                                            <span>{{ __('front::home.footer_app_store') }}</span>
                                        </a>
                                    @endif
                                    @if ($googlePlayUrl !== '')
                                        <a
                                            href="{{ $googlePlayUrl }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center gap-2.5 rounded-full bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800"
                                        >
                                            <i class="fab fa-google-play text-sm mr-1" aria-hidden="true"></i>
                                            <span>{{ __('front::home.footer_google_play') }}</span>
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <div class="mb-8">
                <button
                    type="button"
                    id="providerSearchFiltersToggle"
                    class="mb-4 flex w-full items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-800 shadow-sm transition hover:bg-gray-50 lg:hidden"
                    aria-expanded="false"
                    aria-controls="providerSearchFiltersPanel"
                >
                    <i class="fas fa-sliders-h text-red-600" aria-hidden="true"></i>
                    <span>{{ __('front::home.search_filters_toggle') }}</span>
                </button>
                <div
                    id="providerSearchFiltersPanel"
                    class="grid grid-rows-[0fr] transition-[grid-template-rows] duration-300 ease-in-out motion-reduce:transition-none lg:grid-rows-[1fr] data-[open=true]:grid-rows-[1fr]"
                    data-open="false"
                    role="region"
                    aria-label="{{ __('front::home.search_filters_toggle') }}"
                >
                    <div id="providerSearchFiltersPanelInner" class="min-h-0 overflow-hidden bg-white rounded-2xl shadow-md ">
                        <div class="p-5 md:p-6">
                            <x-front::provider-location-search-form
                                form-id="providerSearchFiltersForm"
                                form-class="provider-search-filters flex flex-col lg:flex-row gap-4 items-end"
                                :selected-service-id="$filters['service_id'] ?? null"
                                :selected-state="$selectedState"
                                :selected-city="$selectedCity"
                            />
                        </div>
                    </div>
                </div>
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
                    @if ($providers->isEmpty() && $featuredProviders->isEmpty())
                        @include('front::includes.empty-state', [
                            'text' => __('front::home.search_results_empty'),
                            'icon' => 'fas fa-hard-hat',
                        ])
                    @else
                        <div class="flex flex-wrap justify-between items-center gap-3 mb-4 hidden sm:block">
                            <p class="text-gray-600 text-sm">
                                <i class="fas fa-list-ul text-red-500" aria-hidden="true"></i>
                                {{ trans_choice('front::home.search_results_count', $providers->total() + $featuredProviders->count(), ['count' => $providers->total() + $featuredProviders->count()]) }}
                            </p>
                        </div>

                        @if ($featuredProviders->isNotEmpty())
                            <div class="mb-6">
                                <h3 class="text-sm font-semibold text-red-600 uppercase tracking-wide mb-3 hidden sm:block">
                                    <i class="fas fa-award" aria-hidden="true"></i>
                                    {{ __('front::home.search_featured_title') }}
                                </h3>
                                <div class="space-y-3 sm:space-y-5">
                                    @foreach ($featuredProviders as $provider)
                                        @include('front::search._provider-card', ['provider' => $provider, 'isFeatured' => true])
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="space-y-3 sm:space-y-5">
                            @foreach ($providers as $provider)
                                @include('front::search._provider-card', ['provider' => $provider, 'isFeatured' => false])
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

@push('script')
    <script>
        (function () {
            var panel = document.getElementById('providerSearchFiltersPanel');
            var inner = document.getElementById('providerSearchFiltersPanelInner');
            var toggle = document.getElementById('providerSearchFiltersToggle');
            if (!panel || !inner || !toggle) {
                return;
            }

            var narrowMq = window.matchMedia('(max-width: 1023px)');

            function syncInert() {
                if (!narrowMq.matches) {
                    inner.removeAttribute('inert');
                    return;
                }
                if (panel.getAttribute('data-open') === 'true') {
                    inner.removeAttribute('inert');
                } else {
                    inner.setAttribute('inert', '');
                }
            }

            toggle.addEventListener('click', function () {
                var nextOpen = panel.getAttribute('data-open') !== 'true';
                panel.setAttribute('data-open', nextOpen ? 'true' : 'false');
                toggle.setAttribute('aria-expanded', nextOpen ? 'true' : 'false');
                syncInert();
            });

            narrowMq.addEventListener('change', syncInert);
            syncInert();
        })();
    </script>
@endpush
