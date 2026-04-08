<nav class="sticky top-0 z-50 w-full min-w-0 max-w-full bg-white shadow-md">
    <div class="container mx-auto flex w-full min-w-0 max-w-full items-center justify-between gap-2 px-5 py-4 max-[410px]:gap-1.5 max-[410px]:px-3 max-[410px]:py-3 lg:gap-3 lg:px-8">
        <div class="min-w-0 shrink text-2xl font-extrabold tracking-tight">
            <img src="{{ getSetting('web_logo', asset('images/default/logos/web_logo.svg')) }}" alt="{{ __('front::home.brand') }}" class="h-10 w-auto max-w-full object-contain object-left max-[410px]:h-8 lg:h-12">
        </div>

        <div class="hidden min-w-0 flex-1 justify-center lg:flex">
            <div class="flex flex-nowrap items-center gap-6 xl:gap-8 font-medium">
                <a href="#home" class="whitespace-nowrap hover:text-red-600 transition">{{ __('front::home.nav_home') }}</a>
                <a href="#how-it-works" class="whitespace-nowrap hover:text-red-600 transition">{{ __('front::home.nav_how_it_works') }}</a>
                <a href="#services" class="whitespace-nowrap hover:text-red-600 transition">{{ __('front::home.nav_services') }}</a>
                <a href="#contact" class="whitespace-nowrap hover:text-red-600 transition">{{ __('front::home.nav_contact') }}</a>
            </div>
        </div>

        <div class="flex shrink-0 items-center gap-2 sm:gap-3 lg:gap-4 max-[410px]:gap-1">
            <nav class="relative" aria-label="{{ __('front::home.language') }}">
                <details id="langDropdown" class="lang-dropdown">
                    <summary
                        class="lang-dropdown__summary flex cursor-pointer items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-semibold text-gray-800 shadow-sm transition hover:border-gray-300 hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2 max-[410px]:gap-1 max-[410px]:px-2 max-[410px]:py-1.5"
                        aria-label="{{ __('front::home.language') }}: {{ data_get($_ALL_LOCALE_, $_LOCALE_.'.native', strtoupper($_LOCALE_)) }}"
                    >
                        @isset($_LOCALE_FLAG_URLS_[$_LOCALE_])
                            <img src="{{ $_LOCALE_FLAG_URLS_[$_LOCALE_] }}" alt="" decoding="async" class="lang-dropdown__flag shrink-0">
                        @else
                            <i class="fas fa-globe text-gray-500 lang-dropdown__flag-icon" aria-hidden="true"></i>
                        @endisset
                        <span class="max-w-[7rem] truncate sm:max-w-none max-[410px]:hidden">{{ data_get($_ALL_LOCALE_, $_LOCALE_.'.native', strtoupper($_LOCALE_)) }}</span>
                        <i class="fas fa-chevron-down lang-dropdown__caret text-xs text-gray-500" aria-hidden="true"></i>
                    </summary>
                    <div class="lang-dropdown__panel absolute right-0 z-[60] mt-2 min-w-[12rem] overflow-hidden rounded-lg border border-gray-100 bg-white py-1 shadow-lg">
                        @foreach ($_ALL_LOCALE_ as $localeCode => $properties)
                            @if ($_LOCALE_ === $localeCode)
                                <span class="flex cursor-default items-center gap-3 px-4 py-2.5 text-sm font-medium text-red-700 bg-red-50" aria-current="true">
                                    @isset($_LOCALE_FLAG_URLS_[$localeCode])
                                        <img src="{{ $_LOCALE_FLAG_URLS_[$localeCode] }}" alt="" decoding="async" class="lang-dropdown__flag shrink-0">
                                    @else
                                        <i class="fas fa-globe text-gray-400 lang-dropdown__flag-icon" aria-hidden="true"></i>
                                    @endisset
                                    <span class="flex-1">{{ $properties['native'] ?? strtoupper($localeCode) }}</span>
                                    <i class="fas fa-check text-xs text-red-600" aria-hidden="true"></i>
                                </span>
                            @else
                                <a
                                    href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($localeCode) }}"
                                    hreflang="{{ $localeCode }}"
                                    rel="alternate"
                                    class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 hover:text-red-600"
                                >
                                    @isset($_LOCALE_FLAG_URLS_[$localeCode])
                                        <img src="{{ $_LOCALE_FLAG_URLS_[$localeCode] }}" alt="" decoding="async" class="lang-dropdown__flag shrink-0">
                                    @else
                                        <i class="fas fa-globe text-gray-400 lang-dropdown__flag-icon" aria-hidden="true"></i>
                                    @endisset
                                    <span>{{ $properties['native'] ?? strtoupper($localeCode) }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </details>
            </nav>

            <a href="{{ route('admin.auth.login') }}" class="hidden lg:inline-flex border-2 border-red-600 text-red-600 hover:bg-red-50 px-4 py-2 xl:px-5 rounded-full text-sm font-semibold transition items-center gap-2 xl:text-base">
                <i class="fas fa-building"></i> {{ __('front::home.company_login') }}
            </a>

            <button
                type="button"
                id="menuBtn"
                class="lg:hidden inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-md text-2xl text-gray-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2 max-[410px]:h-9 max-[410px]:w-9 max-[410px]:text-xl"
                aria-label="{{ __('front::home.menu_toggle') }}"
                aria-expanded="false"
                aria-controls="mobileMenuWrapper"
            >
                <i class="fas fa-bars menu-btn-icon menu-btn-icon--bars" aria-hidden="true"></i>
                <i class="fas fa-times menu-btn-icon menu-btn-icon--close hidden" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    <div id="mobileMenuWrapper" class="mobile-menu-panel lg:hidden">
        <div class="mobile-menu-panel__inner">
            <div id="mobileMenu" class="bg-white border-t py-4 px-5 flex flex-col space-y-3 font-medium">
                <a href="#home" class="hover:text-red-600 py-1">{{ __('front::home.nav_home') }}</a>
                <a href="#how-it-works" class="hover:text-red-600 py-1">{{ __('front::home.nav_how_it_works') }}</a>
                <a href="#services" class="hover:text-red-600 py-1">{{ __('front::home.nav_services') }}</a>
                <a href="#contact" class="hover:text-red-600 py-1">{{ __('front::home.nav_contact') }}</a>
                <a href="{{ route('admin.auth.login') }}" class="border-2 border-red-600 text-red-600 hover:bg-red-50 text-center py-2 rounded-full mt-2 font-semibold flex items-center justify-center gap-2">
                    <i class="fas fa-building"></i> {{ __('front::home.company_login') }}
                </a>
            </div>
        </div>
    </div>
</nav>
