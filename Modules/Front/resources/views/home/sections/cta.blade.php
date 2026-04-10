<section id="contact" class="py-16 md:py-24 bg-gradient-to-r from-red-500 to-red-600 text-white">
    <div class="container mx-auto px-4 text-center">
        <i class="fas fa-phone-alt text-5xl mb-4 opacity-90"></i>
        <h2 class="text-3xl md:text-4xl font-bold">{{ __('front::home.cta_title') }}</h2>
        <p class="text-orange-100 text-lg mt-2 max-w-2xl mx-auto">{{ __('front::home.cta_subtitle') }}</p>
        @php($emergencyContactNumber = getSetting('emergency_contact_number'))
        @if (filled(trim((string) ($emergencyContactNumber ?? ''))))
            <a href="{{ phoneToTelHref(trim((string) $emergencyContactNumber)) }}" class="mt-8 inline-block bg-white text-orange-600 px-8 py-3 rounded-full font-bold shadow-lg hover:shadow-xl transition transform hover:scale-105">
                {{ __('front::home.cta_button_with_phone', ['phone' => trim((string) $emergencyContactNumber)]) }}
            </a>
        @else
            <span class="mt-8 inline-block bg-white text-orange-600 px-8 py-3 rounded-full font-bold shadow-lg">
                {{ __('front::home.cta_button') }}
            </span>
        @endif
    </div>
</section>
