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
                <x-front::provider-location-search-form
                    form-id="heroSearchForm"
                    form-class="hero-search-form flex flex-col md:flex-row gap-4 items-end"
                    theme="hero"
                />
            </div>

            <div class="mt-6 text-sm flex flex-wrap gap-4 justify-center">
                <span><i class="fas fa-check-circle text-green-400"></i> {{ __('front::home.hero_badge_arrival') }}</span>
                <span><i class="fas fa-check-circle text-green-400"></i> {{ __('front::home.hero_badge_licensed') }}</span>
                <span><i class="fas fa-check-circle text-green-400"></i> {{ __('front::home.hero_badge_no_fees') }}</span>
            </div>
            <div class="mt-4">
                @php($emergencyContactNumber = getSetting('emergency_contact_number'))
                @if (filled(trim((string) ($emergencyContactNumber ?? ''))))
                    <a href="{{ phoneToTelHref(trim((string) $emergencyContactNumber)) }}" class="inline-block bg-white/20 hover:bg-white/30 backdrop-blur-sm px-5 py-2 rounded-full text-sm font-semibold transition">
                        <i class="fas fa-phone-alt mr-1"></i>
                        {{ __('front::home.emergency_call_with_phone', ['phone' => trim((string) $emergencyContactNumber)]) }}
                    </a>
                @endif

            </div>
        </div>
    </div>
</section>
