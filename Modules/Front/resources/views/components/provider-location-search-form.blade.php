@props([
    'formId',
    'formClass' => '',
    'theme' => 'panel',
    'selectedServiceId' => null,
    'selectedState' => null,
    'selectedCity' => null,
    'showHint' => null,
])

@php
    $isHero = $theme === 'hero';
    if ($showHint === null) {
        $showHint = $isHero;
    }
    $idPrefix = $isHero ? 'hero' : 'search';
    $serviceIdAttr = $idPrefix.'_service_id';
    $stateIdAttr = $idPrefix.'_state_id';
    $cityIdAttr = $idPrefix.'_city_id';
    $labelClass = $isHero
        ? 'block text-sm font-semibold mb-1 text-gray-200 uppercase tracking-wide'
        : 'block text-sm font-semibold mb-1 text-gray-700 uppercase tracking-wide';
    $iconClass = $isHero ? 'fas fa-wrench text-red-400 mr-1' : 'fas fa-wrench text-red-600 mr-1';
    $stateIconClass = $isHero ? 'fas fa-map text-red-400 mr-1' : 'fas fa-map text-red-600 mr-1';
    $cityIconClass = $isHero ? 'fas fa-map-marker-alt text-red-400 mr-1' : 'fas fa-map-marker-alt text-red-600 mr-1';
    $serviceSelectClass = $isHero
        ? 'w-full px-4 py-2 rounded-lg text-gray-800 bg-white focus:ring-2 focus:ring-red-500'
        : 'w-full px-4 py-2 rounded-lg text-gray-800 bg-white border border-gray-200 focus:ring-2 focus:ring-red-500 focus:border-red-500';
    $locationSelectClass = $isHero
        ? 'js-pls-state w-full px-4 py-2 rounded-lg text-gray-800 bg-white focus:ring-2 focus:ring-red-500'
        : 'js-pls-state search-select2-state w-full px-4 py-2 rounded-lg text-gray-800 bg-white border border-gray-200 focus:ring-2 focus:ring-red-500 focus:border-red-500';
    $citySelectClass = $isHero
        ? 'js-pls-city w-full px-4 py-2 rounded-lg text-gray-800 bg-white focus:ring-2 focus:ring-red-500'
        : 'js-pls-city search-select2-city w-full px-4 py-2 rounded-lg text-gray-800 bg-white border border-gray-200 focus:ring-2 focus:ring-red-500 focus:border-red-500';
    $cityDisabled = ! $selectedState;
    $buttonClass = $isHero
        ? 'bg-red-600 hover:bg-red-700 px-6 py-2 rounded-lg font-bold transition flex items-center gap-2 justify-center md:justify-center w-full md:w-auto'
        : 'bg-red-600 hover:bg-red-700 px-6 py-2 rounded-lg font-bold text-white transition flex items-center gap-2 justify-center w-full lg:w-auto';
@endphp

<form
    id="{{ $formId }}"
    action="{{ route('front.search') }}"
    method="GET"
    class="js-provider-location-search {{ $formClass }}"
    data-skip-submit-loading
    data-states-list-url="{{ route('zms.states.list') }}"
    data-cities-list-url="{{ route('zms.cities.list') }}"
    data-default-country-id="{{ $frontSearchDefaultCountryId }}"
    data-locale="{{ app()->getLocale() }}"
    data-selected-state-id="{{ data_get($selectedState, 'id', '') }}"
    data-selected-state-name="{{ data_get($selectedState, 'name', '') }}"
    data-selected-city-id="{{ data_get($selectedCity, 'id', '') }}"
    data-selected-city-name="{{ data_get($selectedCity, 'name', '') }}"
>
    <div class="flex-1 text-left w-full">
        <label class="{{ $labelClass }}" for="{{ $serviceIdAttr }}">
            <i class="{{ $iconClass }}" aria-hidden="true"></i> {{ __('front::home.service_label') }}
        </label>
        <select id="{{ $serviceIdAttr }}" name="service_id" class="{{ $serviceSelectClass }}">
            <option value="">{{ __('front::home.service_placeholder') }}</option>
            @foreach ($frontPublicFilterServices as $service)
                <option value="{{ $service['id'] }}" @selected((string) ($selectedServiceId ?? '') === (string) $service['id'])>{{ $service['name'] }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex-1 text-left w-full">
        <label class="{{ $labelClass }}" for="{{ $stateIdAttr }}">
            <i class="{{ $stateIconClass }}" aria-hidden="true"></i> {{ __('front::home.state_label') }}
        </label>
        <select id="{{ $stateIdAttr }}" name="state_id" class="{{ $locationSelectClass }}">
            <option value="">{{ __('front::home.state_placeholder') }}</option>
        </select>
    </div>
    <div class="flex-1 text-left w-full">
        <label class="{{ $labelClass }}" for="{{ $cityIdAttr }}">
            <i class="{{ $cityIconClass }}" aria-hidden="true"></i> {{ __('front::home.city_label') }}
        </label>
        <select id="{{ $cityIdAttr }}" name="city_id" class="{{ $citySelectClass }}" @disabled($cityDisabled)>
            <option value="">{{ __('front::home.city_placeholder') }}</option>
        </select>
    </div>
    <button type="submit" class="cursor-pointer {{ $buttonClass }}">
        <i class="fas fa-search" aria-hidden="true"></i> {{ __('front::home.search_button') }}
    </button>
</form>
@if ($showHint)
    <p class="text-xs text-gray-300 mt-3">{{ __('front::home.filter_hint') }}</p>
@endif
