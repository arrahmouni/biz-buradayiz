<section id="how-it-works" class="py-16 md:py-24 bg-white">
    <div class="container mx-auto px-5 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <span class="text-red-600 font-semibold text-sm uppercase tracking-wider">{{ __('front::home.how_title_badge') }}</span>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-2">{{ __('front::home.how_main_title') }}</h2>
            <p class="text-gray-600 mt-4">{{ __('front::home.how_subtitle') }}</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 lg:gap-12">
            <div class="text-center p-6 rounded-2xl bg-gray-50 shadow-sm card-hover transition-smooth">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-5">
                    <i class="fas fa-map-marked-alt text-red-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800">{{ __('front::home.step1_title') }}</h3>
                <p class="text-gray-600 mt-2">{{ __('front::home.step1_desc') }}</p>
            </div>
            <div class="text-center p-6 rounded-2xl bg-gray-50 shadow-sm card-hover transition-smooth">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-5">
                    <i class="fas fa-handshake text-red-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800">{{ __('front::home.step2_title') }}</h3>
                <p class="text-gray-600 mt-2">{{ __('front::home.step2_desc') }}</p>
            </div>
            <div class="text-center p-6 rounded-2xl bg-gray-50 shadow-sm card-hover transition-smooth">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-5">
                    <i class="fas fa-car-crash text-red-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800">{{ __('front::home.step3_title') }}</h3>
                <p class="text-gray-600 mt-2">{{ __('front::home.step3_desc') }}</p>
            </div>
        </div>
    </div>
</section>
