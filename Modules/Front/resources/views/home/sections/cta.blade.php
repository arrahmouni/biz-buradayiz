<section id="contact" class="relative py-16 md:py-24 overflow-hidden bg-gradient-to-br from-red-700 via-red-600 to-red-800 text-white">
    <!-- Animated background pattern (optional) -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 20% 40%, rgba(255,255,255,0.2) 2px, transparent 2px); background-size: 24px 24px;"></div>
    </div>

    <div class="container mx-auto px-4 text-center relative z-10">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full mb-6 shadow-lg">
            <i class="fas fa-headset text-4xl"></i>
        </div>
        <h2 class="text-3xl md:text-5xl font-extrabold tracking-tight">
            {{ __('front::home.cta_title') }}
        </h2>
        <p class="text-red-100 text-lg md:text-xl mt-3 max-w-2xl mx-auto">
            {{ __('front::home.cta_subtitle') }}
        </p>

        @php($emergencyContactNumber = getSetting('emergency_contact_number'))
        @if (filled(trim((string) ($emergencyContactNumber ?? ''))))
            <a href="{{ phoneToTelHref(trim((string) $emergencyContactNumber)) }}"
               class="mt-8 inline-flex items-center gap-3 bg-white text-red-600 px-8 py-4 rounded-full font-bold text-lg shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-white/50">
                <i class="fas fa-phone-alt text-xl"></i>
                {{ __('front::home.cta_button_with_phone', ['phone' => trim((string) $emergencyContactNumber)]) }}
                <i class="fas fa-arrow-right text-sm transition-transform duration-200 group-hover:translate-x-1"></i>
            </a>
        @else
            <span class="mt-8 inline-flex items-center gap-3 bg-white text-red-600 px-8 py-4 rounded-full font-bold text-lg shadow-xl">
                <i class="fas fa-phone-alt"></i>
                {{ __('front::home.cta_button') }}
            </span>
        @endif

        <p class="text-red-100 text-sm mt-6 flex items-center justify-center gap-2">
            <i class="fas fa-clock"></i> {{ __('front::home.cta_24_7_emergency_service') }}
        </p>
    </div>
</section>
