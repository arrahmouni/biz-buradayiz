@include('front::home.sections.cta')
<footer class="bg-gray-900 text-gray-300 pt-12 pb-6">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <div class="flex items-center space-x-2 mb-4">
                    <img src="{{ getSetting('web_logo', asset('images/default/logos/web_logo.svg')) }}" alt="{{ __('front::home.brand') }}" class="h-10 w-auto md:h-12 brightness-0 invert">
                </div>
                <p class="text-sm text-gray-400">{{ __('front::home.footer_desc') }}</p>
            </div>
            <div>
                <h4 class="font-semibold text-white mb-4">{{ __('front::home.footer_quick') }}</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#home" class="hover:text-red-400 transition">{{ __('front::home.nav_home') }}</a></li>
                    <li><a href="#services" class="hover:text-red-400 transition">{{ __('front::home.nav_services') }}</a></li>
                    <li><a href="#how-it-works" class="hover:text-red-400 transition">{{ __('front::home.nav_how_it_works') }}</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-white mb-4">{{ __('front::home.footer_support') }}</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="hover:text-red-400 transition">{{ __('front::home.footer_faq') }}</a></li>
                    <li><a href="#contact" class="hover:text-red-400 transition">{{ __('front::home.nav_contact') }}</a></li>
                    <li><a href="#" class="hover:text-red-400 transition">{{ __('front::home.footer_terms') }}</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-white mb-4">{{ __('front::home.footer_follow') }}</h4>
                <div class="flex space-x-4 text-2xl">
                    <i class="fab fa-twitter hover:text-red-400 cursor-pointer transition"></i>
                    <i class="fab fa-instagram hover:text-red-400 cursor-pointer transition"></i>
                    <i class="fab fa-facebook hover:text-red-400 cursor-pointer transition"></i>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-800 mt-10 pt-6 text-center text-sm text-gray-500">
            &copy; {{ now()->year }} {{ __('front::home.brand') }} - {{ __('front::home.copyright_text') }}
        </div>
    </div>
</footer>
