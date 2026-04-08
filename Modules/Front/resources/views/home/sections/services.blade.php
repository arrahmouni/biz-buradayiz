<section id="services" class="py-16 md:py-24 bg-gray-50">
    <div class="container mx-auto px-5 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800">{{ __('front::home.popular_services_title') }}</h2>
            <p class="text-gray-500 mt-3">{{ __('front::home.popular_services_sub') }}</p>
        </div>
        <div class="grid md:grid-cols-4 gap-8 lg:gap-12">
            <div class="bg-white rounded-2xl p-5 text-center shadow-sm hover:shadow-xl transition service-card">
                <i class="fas fa-truck text-4xl text-red-600 mb-3"></i>
                <h3 class="font-bold text-gray-800">{{ __('front::home.service_towing') }}</h3>
                <p class="text-xs text-gray-400 mt-1">{{ __('front::home.service_towing_desc') }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 text-center shadow-sm hover:shadow-xl transition service-card">
                <i class="fas fa-car-side text-4xl text-red-600 mb-3"></i>
                <h3 class="font-bold text-gray-800">{{ __('front::home.service_tire') }}</h3>
                <p class="text-xs text-gray-400 mt-1">{{ __('front::home.service_tire_desc') }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 text-center shadow-sm hover:shadow-xl transition service-card">
                <i class="fas fa-car-battery text-4xl text-red-600 mb-3"></i>
                <h3 class="font-bold text-gray-800">{{ __('front::home.service_battery') }}</h3>
                <p class="text-xs text-gray-400 mt-1">{{ __('front::home.service_battery_desc') }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 text-center shadow-sm hover:shadow-xl transition service-card">
                <i class="fas fa-gas-pump text-4xl text-red-600 mb-3"></i>
                <h3 class="font-bold text-gray-800">{{ __('front::home.service_fuel') }}</h3>
                <p class="text-xs text-gray-400 mt-1">{{ __('front::home.service_fuel_desc') }}</p>
            </div>
        </div>
    </div>
</section>
