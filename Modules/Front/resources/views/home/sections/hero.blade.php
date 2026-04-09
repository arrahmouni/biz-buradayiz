<section id="home" class="hero-bg text-white py-24 md:py-32">
    <div class="container mx-auto px-5 lg:px-8">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-4xl md:text-6xl font-extrabold leading-tight">
                {{ __('front::home.hero_title_top') }} <br>
                <span class="text-red-400">{{ __('front::home.hero_title_bottom') }}</span>
            </h1>
            <p class="text-lg md:text-xl mt-4 opacity-90">
                {{ __('front::home.hero_subtitle') }}
            </p>

            <div class="mt-8 bg-white/10 backdrop-blur-md rounded-2xl p-5 md:p-6 shadow-lg">
                <form id="heroSearchForm"
                      action="#"
                      method="GET"
                      class="hero-search-form flex flex-col md:flex-row gap-4 items-end"
                      data-states-list-url="{{ route('zms.states.list') }}"
                      data-cities-list-url="{{ route('zms.cities.list') }}"
                      data-default-country-id="{{ $frontSearchDefaultCountryId }}"
                      data-locale="{{ app()->getLocale() }}">
                    <div class="flex-1 text-left w-full">
                        <label class="block text-sm font-semibold mb-1 text-gray-200 uppercase tracking-wide">
                            <i class="fas fa-wrench text-red-400 mr-1"></i> {{ __('front::home.service_label') }}
                        </label>
                        <select name="service_id" class="w-full px-4 py-2 rounded-lg text-gray-800 bg-white focus:ring-2 focus:ring-red-500">
                            <option value="">{{ __('front::home.service_placeholder') }}</option>
                            @foreach ($frontPublicFilterServices as $service)
                                <option value="{{ $service['id'] }}">{{ $service['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1 text-left w-full">
                        <label class="block text-sm font-semibold mb-1 text-gray-200 uppercase tracking-wide" for="hero_state_id">
                            <i class="fas fa-map text-red-400 mr-1"></i> {{ __('front::home.state_label') }}
                        </label>
                        <select id="hero_state_id" name="state_id" class="hero-select2-state w-full px-4 py-2 rounded-lg text-gray-800 bg-white focus:ring-2 focus:ring-red-500">
                            <option value="">{{ __('front::home.state_placeholder') }}</option>
                        </select>
                    </div>
                    <div class="flex-1 text-left w-full">
                        <label class="block text-sm font-semibold mb-1 text-gray-200 uppercase tracking-wide" for="hero_city_id">
                            <i class="fas fa-map-marker-alt text-red-400 mr-1"></i> {{ __('front::home.city_label') }}
                        </label>
                        <select id="hero_city_id" name="city_id" class="hero-select2-city w-full px-4 py-2 rounded-lg text-gray-800 bg-white focus:ring-2 focus:ring-red-500" disabled>
                            <option value="">{{ __('front::home.city_placeholder') }}</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 px-6 py-2 rounded-lg font-bold transition flex items-center gap-2 justify-center md:justify-center w-full md:w-auto">
                        <i class="fas fa-search"></i> {{ __('front::home.search_button') }}
                    </button>
                </form>
                <p class="text-xs text-gray-300 mt-3">{{ __('front::home.filter_hint') }}</p>
            </div>

            <div class="mt-6 text-sm flex flex-wrap gap-4 justify-center">
                <span><i class="fas fa-check-circle text-green-400"></i> {{ __('front::home.hero_badge_arrival') }}</span>
                <span><i class="fas fa-check-circle text-green-400"></i> {{ __('front::home.hero_badge_licensed') }}</span>
                <span><i class="fas fa-check-circle text-green-400"></i> {{ __('front::home.hero_badge_no_fees') }}</span>
            </div>
            <div class="mt-4">
                @if ($frontEmergencyFromSettings)
                    <a href="{{ $frontEmergencyTelHref }}" class="inline-block bg-white/20 hover:bg-white/30 backdrop-blur-sm px-5 py-2 rounded-full text-sm font-semibold transition">
                        <i class="fas fa-phone-alt mr-1"></i>
                            {{ __('front::home.emergency_call_with_phone', ['phone' => $frontEmergencyDisplay]) }}
                    </a>
                @endif

            </div>
        </div>
    </div>
</section>
