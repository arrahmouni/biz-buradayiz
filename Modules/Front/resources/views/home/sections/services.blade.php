@if(! empty($frontPublicServices))
    <section id="services" class="py-16 md:py-24 bg-gray-50">
        <div class="container mx-auto px-5 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">{{ __('front::home.popular_services_title') }}</h2>
                <p class="text-gray-500 mt-3">{{ __('front::home.popular_services_sub') }}</p>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
                @foreach($frontPublicServices as $service)
                    <div class="bg-white rounded-2xl p-5 text-center shadow-sm hover:shadow-xl transition service-card">
                        <i class="{{ $service['icon'] }} text-4xl text-red-600 mb-3"></i>
                        <h3 class="font-bold text-gray-800">{{ $service['name'] }}</h3>
                        @if(! empty($service['description']))
                            <p class="text-xs text-gray-400 mt-1">{{ $service['description'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
