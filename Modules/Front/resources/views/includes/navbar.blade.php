@php
    use Modules\Auth\Enums\UserType;

    $navWebUser = auth('web')->user();
    $navProviderAuthenticated = $navWebUser && $navWebUser->type === UserType::ServiceProvider;
@endphp
<nav class="site-header sticky top-0 z-50 w-full min-w-0 max-w-full border-b border-gray-200/80 bg-white/90 shadow-sm backdrop-blur-md backdrop-saturate-150 supports-[backdrop-filter]:bg-white/80">
    <div class="container mx-auto flex w-full min-w-0 max-w-full items-center justify-between gap-2 px-5 py-3.5 max-[410px]:gap-1.5 max-[410px]:px-3 max-[410px]:py-3 lg:gap-3 lg:px-8 lg:py-4">
        <a
            href="{{ route('front.index') }}"
            class="site-header__brand inline-block min-w-0 shrink text-2xl font-extrabold tracking-tight rounded transition-opacity hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2"
        >
            <x-front::placeholder-image
                :src="getSetting(\Modules\Config\Constatnt::WEB_LOGO, asset('images/default/logos/web_logo.svg'))"
                :alt="__('front::home.brand')"
                class="h-10 w-auto max-w-full object-contain object-left max-[410px]:h-8 lg:h-12"
            />
        </a>

        <div class="hidden min-w-0 flex-1 justify-center lg:flex">
            <div class="site-nav-desktop flex flex-nowrap items-center gap-1 xl:gap-2 text-[0.9375rem] font-medium leading-snug text-gray-700">
                <a href="{{ route('front.index') }}" class="site-nav-desktop__link">{{ __('front::home.nav_home') }}</a>
                @if(! empty($frontPublicServices))
                    <nav class="relative flex items-center" aria-label="{{ __('front::home.nav_services') }}">
                        <details id="servicesNavDropdown" class="nav-services-dropdown site-nav-services">
                            <summary
                                class="nav-services-dropdown__summary site-nav-services__trigger flex cursor-pointer list-none items-center gap-1.5 whitespace-nowrap rounded-lg px-2.5 py-2 text-gray-700 transition-colors duration-200 hover:bg-gray-100/90 hover:text-red-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2"
                                aria-haspopup="true"
                            >
                                {{ __('front::home.nav_services') }}
                                <i class="fas fa-chevron-down nav-services-dropdown__caret text-[0.65rem] text-gray-500 transition-transform duration-200" aria-hidden="true"></i>
                            </summary>
                            <div class="site-nav-services__shell">
                                <div class="site-nav-services__panel nav-services-dropdown__panel">
                                    <div class="site-nav-services__panel-inner">
                                        <p class="site-nav-services__heading px-4 pt-3 pb-1 text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('front::home.nav_services') }}</p>
                                        <ul class="site-nav-services__list px-2 pb-2">
                                            @foreach($frontPublicServices as $navService)
                                                <li class="site-nav-services__row">
                                                    <a
                                                        href="{{ route('front.search', ['service_id' => $navService['id']]) }}"
                                                        class="site-nav-services__link group gap-3 rounded-lg px-3 py-2.5 text-gray-800 transition-colors duration-200 hover:bg-red-50/90 hover:shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2"
                                                    >
                                                        <span class="site-nav-services__icon flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-red-50 text-red-600 transition-colors duration-200 group-hover:bg-red-100" aria-hidden="true">
                                                            <i class="{{ $navService['icon'] }} text-base"></i>
                                                        </span>
                                                        <span class="min-w-0 flex-1">
                                                            <span class="site-nav-services__name block text-sm font-semibold text-gray-900 transition-colors duration-200 group-hover:text-red-700">{{ $navService['name'] }}</span>
                                                        </span>
                                                        <i class="fas fa-arrow-right site-nav-services__arrow shrink-0 text-xs text-gray-300 opacity-0 transition-all duration-200 group-hover:translate-x-0.5 group-hover:text-red-500 group-hover:opacity-100" aria-hidden="true"></i>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </details>
                    </nav>
                @endif
                <a href="{{ route('front.blog.index') }}" class="site-nav-desktop__link">{{ __('front::home.footer_blog') }}</a>
                <a href="{{ route('front.page.faq') }}" class="site-nav-desktop__link">{{ __('front::home.footer_faq') }}</a>
                <a href="{{ route('front.contact.show') }}" class="site-nav-desktop__link">{{ __('front::home.nav_contact') }}</a>
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
                            <x-front::placeholder-image
                                :src="$_LOCALE_FLAG_URLS_[$_LOCALE_]"
                                alt=""
                                decoding="async"
                                class="lang-dropdown__flag shrink-0"
                            />
                        @else
                            <i class="fas fa-globe text-gray-500 lang-dropdown__flag-icon" aria-hidden="true"></i>
                        @endisset
                        <span class="max-w-[7rem] truncate sm:max-w-none max-[410px]:hidden">{{ data_get($_ALL_LOCALE_, $_LOCALE_.'.native', strtoupper($_LOCALE_)) }}</span>
                        <i class="fas fa-chevron-down lang-dropdown__caret text-xs text-gray-500" aria-hidden="true"></i>
                    </summary>
                    <div class="lang-dropdown__panel absolute right-0 z-[60] mt-2 min-w-[12rem] overflow-hidden rounded-lg border border-gray-100 bg-white py-1">
                        @foreach ($_ALL_LOCALE_ as $localeCode => $properties)
                            @if ($_LOCALE_ === $localeCode)
                                <span class="flex cursor-default items-center gap-3 px-4 py-2.5 text-sm font-medium text-red-700 bg-red-50" aria-current="true">
                                    @isset($_LOCALE_FLAG_URLS_[$localeCode])
                                        <x-front::placeholder-image
                                            :src="$_LOCALE_FLAG_URLS_[$localeCode]"
                                            alt=""
                                            decoding="async"
                                            class="lang-dropdown__flag shrink-0"
                                        />
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
                                        <x-front::placeholder-image
                                            :src="$_LOCALE_FLAG_URLS_[$localeCode]"
                                            alt=""
                                            decoding="async"
                                            class="lang-dropdown__flag shrink-0"
                                        />
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

            <div class="hidden lg:flex items-center gap-2 xl:gap-3">
                @if ($navProviderAuthenticated)
                    <details id="providerAccountNav" class="relative">
                        <summary
                            class="provider-account-nav__summary flex cursor-pointer list-none items-center gap-2 rounded-full border border-gray-200 bg-white px-2 py-1.5 shadow-sm transition hover:border-gray-300 hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2 xl:px-3 [&::-webkit-details-marker]:hidden"
                            aria-label="{{ __('front::home.nav_provider_account_aria') }}"
                        >
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-700">
                                <i class="fas fa-user-circle text-lg" aria-hidden="true"></i>
                            </span>
                            <span class="hidden max-w-[9rem] truncate text-sm font-semibold text-gray-800 xl:inline">{{ $navWebUser->full_name }}</span>
                            <i class="fas fa-chevron-down hidden text-xs text-gray-500 xl:inline" aria-hidden="true"></i>
                        </summary>
                        <div class="provider-account-nav__panel absolute right-0 z-[60] mt-2 min-w-[13rem] overflow-hidden rounded-lg border border-gray-100 bg-white py-1 shadow-lg">
                            <a
                                href="{{ route('front.provider.dashboard') }}"
                                class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 hover:text-red-600 focus:outline-none focus-visible:bg-gray-50"
                            >
                                <i class="fas fa-tachometer-alt w-4 shrink-0 text-center text-red-600" aria-hidden="true"></i>
                                {{ __('front::auth.dashboard_title') }}
                            </a>
                            <a
                                href="{{ route('front.provider.account') }}"
                                class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 hover:text-red-600 focus:outline-none focus-visible:bg-gray-50"
                            >
                                <i class="fas fa-user-cog w-4 shrink-0 text-center text-gray-500" aria-hidden="true"></i>
                                {{ __('front::provider_account.nav_account_settings') }}
                            </a>
                            @if (filled($navWebUser->profile_slug))
                                <a
                                    href="{{ route('front.provider.show', $navWebUser->profile_slug) }}"
                                    class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 hover:text-red-600 focus:outline-none focus-visible:bg-gray-50"
                                >
                                    <i class="fas fa-id-card w-4 shrink-0 text-center text-gray-500" aria-hidden="true"></i>
                                    {{ __('front::home.nav_public_profile') }}
                                </a>
                            @endif
                            <form method="post" action="{{ route('front.provider.logout') }}" class="border-t border-gray-100">
                                @csrf
                                <button
                                    type="submit"
                                    class="flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm font-medium text-gray-700 transition hover:bg-gray-50 hover:text-red-600 focus:outline-none focus-visible:bg-gray-50"
                                >
                                    <i class="fas fa-sign-out-alt w-4 shrink-0 text-center text-gray-500" aria-hidden="true"></i>
                                    {{ __('front::auth.logout') }}
                                </button>
                            </form>
                        </div>
                    </details>
                @else
                    <a href="{{ route('front.provider.login') }}" class="inline-flex border-2 border-red-600 text-red-600 hover:bg-red-50 px-4 py-2 xl:px-5 rounded-full text-sm font-semibold transition items-center gap-2 xl:text-base focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2">
                        <i class="fas fa-sign-in-alt" aria-hidden="true"></i> {{ __('front::home.provider_login') }}
                    </a>
                    <a href="{{ route('front.provider.register') }}" class="inline-flex border-2 border-red-600 bg-red-600 text-white hover:border-red-700 hover:bg-red-700 px-4 py-2 xl:px-5 rounded-full text-sm font-semibold transition items-center gap-2 shadow-sm xl:text-base focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2">
                        <i class="fas fa-user-plus" aria-hidden="true"></i> {{ __('front::home.provider_register') }}
                    </a>
                @endif
            </div>

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
            <div id="mobileMenu" class="mobile-menu-panel__content border-t border-gray-100 bg-white/95 py-4 px-5 font-medium leading-snug text-gray-800">
                <div class="flex flex-col gap-1">
                    <a href="{{ route('front.index') }}" class="site-nav-mobile__link rounded-lg px-2 py-2.5">{{ __('front::home.nav_home') }}</a>
                    @if(! empty($frontPublicServices))
                    <details class="nav-services-dropdown site-nav-services-mobile border-b border-gray-100 pb-2">
                        <summary
                            class="nav-services-dropdown__summary site-nav-services-mobile__summary flex cursor-pointer list-none items-center justify-between rounded-lg px-2 py-2.5 text-gray-800 transition-colors duration-200 hover:bg-gray-50 hover:text-red-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2"
                        >
                            <span class="font-medium">{{ __('front::home.nav_services') }}</span>
                            <i class="fas fa-chevron-down nav-services-dropdown__caret text-xs text-gray-500 transition-transform duration-200" aria-hidden="true"></i>
                        </summary>
                        <div class="site-nav-services-mobile__expand">
                            <div class="site-nav-services-mobile__expand-inner">
                                <ul class="mt-2 flex flex-col gap-1 overflow-y-auto pl-1 pr-0.5">
                                    @foreach($frontPublicServices as $navService)
                                        <li>
                                            <a
                                                href="{{ route('front.search', ['service_id' => $navService['id']]) }}"
                                                class="site-nav-services-mobile__item group flex gap-3 rounded-lg px-2 py-2.5 text-gray-800 transition-colors duration-200 hover:bg-red-50/90 hover:shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2"
                                            >
                                                <span class="site-nav-services-mobile__icon flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-red-50 text-red-600 transition-colors duration-200 group-hover:bg-red-100" aria-hidden="true">
                                                    <i class="{{ $navService['icon'] }} text-sm"></i>
                                                </span>
                                                <span class="min-w-0 flex-1">
                                                    <span class="block text-sm font-semibold text-gray-900 group-hover:text-red-700">{{ $navService['name'] }}</span>

                                                </span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </details>
                    @else
                    <a href="{{ route('front.index') }}#services" class="site-nav-mobile__link rounded-lg px-2 py-2.5">{{ __('front::home.nav_services') }}</a>
                    @endif
                    <a href="{{ route('front.blog.index') }}" class="site-nav-mobile__link rounded-lg px-2 py-2.5">{{ __('front::home.footer_blog') }}</a>
                    <a href="{{ route('front.page.faq') }}" class="site-nav-mobile__link rounded-lg px-2 py-2.5">{{ __('front::home.footer_faq') }}</a>
                    <a href="{{ route('front.contact.show') }}" class="site-nav-mobile__link rounded-lg px-2 py-2.5">{{ __('front::home.nav_contact') }}</a>
                </div>
                @if ($navProviderAuthenticated)
                    <div class="mt-3 flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50/80 px-3 py-3">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-700">
                            <i class="fas fa-user-circle text-xl" aria-hidden="true"></i>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-gray-900">{{ $navWebUser->full_name }}</p>
                            <p class="truncate text-xs text-gray-500">{{ $navWebUser->email }}</p>
                        </div>
                    </div>
                    <a href="{{ route('front.provider.dashboard') }}" class="mt-3 border-2 border-red-600 bg-red-600 text-white hover:border-red-700 hover:bg-red-700 text-center py-2.5 rounded-full font-semibold flex items-center justify-center gap-2 shadow-sm transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2">
                        <i class="fas fa-tachometer-alt" aria-hidden="true"></i> {{ __('front::auth.dashboard_title') }}
                    </a>
                    <a href="{{ route('front.provider.account') }}" class="mt-2 border-2 border-gray-200 bg-white text-gray-800 hover:border-red-200 hover:bg-red-50/50 text-center py-2.5 rounded-full font-semibold flex items-center justify-center gap-2 transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2">
                        <i class="fas fa-user-cog" aria-hidden="true"></i> {{ __('front::provider_account.nav_account_settings') }}
                    </a>
                    @if (filled($navWebUser->profile_slug))
                        <a href="{{ route('front.provider.show', $navWebUser->profile_slug) }}" class="mt-2 border-2 border-gray-200 bg-white text-gray-800 hover:border-red-200 hover:bg-red-50/50 text-center py-2.5 rounded-full font-semibold flex items-center justify-center gap-2 transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2">
                            <i class="fas fa-id-card" aria-hidden="true"></i> {{ __('front::home.nav_public_profile') }}
                        </a>
                    @endif
                    <form method="post" action="{{ route('front.provider.logout') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="w-full border-2 border-gray-200 text-gray-700 hover:bg-gray-50 text-center py-2.5 rounded-full font-semibold flex items-center justify-center gap-2 transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2">
                            <i class="fas fa-sign-out-alt" aria-hidden="true"></i> {{ __('front::auth.logout') }}
                        </button>
                    </form>
                @else
                    <a href="{{ route('front.provider.login') }}" class="mt-3 border-2 border-red-600 text-red-600 hover:bg-red-50 text-center py-2.5 rounded-full font-semibold flex items-center justify-center gap-2 transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2">
                        <i class="fas fa-sign-in-alt" aria-hidden="true"></i> {{ __('front::home.provider_login') }}
                    </a>
                    <a href="{{ route('front.provider.register') }}" class="mt-2 border-2 border-red-600 bg-red-600 text-white hover:border-red-700 hover:bg-red-700 text-center py-2.5 rounded-full font-semibold flex items-center justify-center gap-2 shadow-sm transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2">
                        <i class="fas fa-user-plus" aria-hidden="true"></i> {{ __('front::home.provider_register') }}
                    </a>
                @endif
            </div>
        </div>
    </div>
</nav>
