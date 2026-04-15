@php
    $footerAppStoreUrl = trim((string) (getSetting('app_store', '') ?? ''));
    $footerGooglePlayUrl = trim((string) (getSetting('google_play', '') ?? ''));
    $footerHasDownloadLinks = $footerAppStoreUrl !== '' || $footerGooglePlayUrl !== '';

    $footerSocial = array_values(array_filter([
        [
            'url' => trim((string) (getSetting('facebook', '') ?? '')),
            'icon' => 'fab fa-facebook-f',
            'label' => __('front::home.footer_social_facebook'),
        ],
        [
            'url' => trim((string) (getSetting('twitter', '') ?? '')),
            'icon' => 'fab fa-twitter',
            'label' => __('front::home.footer_social_twitter'),
        ],
        [
            'url' => trim((string) (getSetting('instagram', '') ?? '')),
            'icon' => 'fab fa-instagram',
            'label' => __('front::home.footer_social_instagram'),
        ],
        [
            'url' => trim((string) (getSetting('linkedin', '') ?? '')),
            'icon' => 'fab fa-linkedin-in',
            'label' => __('front::home.footer_social_linkedin'),
        ],
        [
            'url' => trim((string) (getSetting('youtube', '') ?? '')),
            'icon' => 'fab fa-youtube',
            'label' => __('front::home.footer_social_youtube'),
        ],
        [
            'url' => trim((string) (getSetting('tiktok', '') ?? '')),
            'icon' => 'fab fa-tiktok',
            'label' => __('front::home.footer_social_tiktok'),
        ],
    ], static fn (array $item): bool => $item['url'] !== ''));

    $footerHasSocial = $footerSocial !== [];
    $footerShowConnectColumn = $footerHasDownloadLinks || $footerHasSocial;

    $footerPagesCollection = $footerPages ?? collect();
    $footerHasPages = $footerPagesCollection->isNotEmpty();

    $footerColumnCount = 3;
    if ($footerHasPages) {
        $footerColumnCount++;
    }
    if ($footerShowConnectColumn) {
        $footerColumnCount++;
    }

    $footerGridClass = match ($footerColumnCount) {
        3 => 'md:grid-cols-3 lg:grid-cols-3',
        4 => 'md:grid-cols-2 lg:grid-cols-4',
        5 => 'md:grid-cols-2 lg:grid-cols-5',
        default => 'md:grid-cols-2 lg:grid-cols-4',
    };
@endphp

<footer class="bg-gradient-to-br from-slate-900 via-gray-900 to-slate-950 text-gray-300 pt-12 pb-6">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 gap-8 {{ $footerGridClass }}">
            <div>
                <a
                    href="{{ route('front.index') }}"
                    class="inline-flex items-center space-x-2 mb-4 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-900 rounded"
                >
                    <img src="{{ getSetting('web_logo', asset('images/default/logos/web_logo.svg')) }}" alt="{{ __('front::home.brand') }}" class="h-10 w-auto md:h-12 brightness-0 invert">
                </a>
                <p class="text-sm text-gray-400">{{ __('front::home.footer_desc') }}</p>
            </div>
            <div>
                <h4 class="font-semibold text-white mb-4">{{ __('front::home.footer_quick') }}</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('front.index') }}" class="hover:text-red-400 transition">{{ __('front::home.nav_home') }}</a></li>
                    <li><a href="{{ route('front.contact.show') }}" class="hover:text-red-400 transition">{{ __('front::home.nav_contact') }}</a></li>
                    <li><a href="{{ route('front.provider.login') }}" class="hover:text-red-400 transition">{{ __('front::home.provider_login') }}</a></li>
                    <li><a href="{{ route('front.provider.register') }}" class="hover:text-red-400 transition">{{ __('front::home.provider_register') }}</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-white mb-4">{{ __('front::home.footer_support') }}</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('front.blog.index') }}" class="hover:text-red-400 transition">{{ __('front::home.footer_blog') }}</a></li>
                    <li><a href="{{ route('front.page.faq') }}" class="hover:text-red-400 transition">{{ __('front::home.footer_faq') }}</a></li>
                </ul>
            </div>
            @if ($footerHasPages)
                <div>
                    <h4 class="font-semibold text-white mb-4">{{ __('front::home.footer_pages') }}</h4>
                    <ul class="space-y-2 text-sm">
                        @foreach ($footerPagesCollection as $footerPage)
                            <li>
                                <a href="{{ $footerPage['url'] }}" class="hover:text-red-400 transition">{{ $footerPage['title'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if ($footerShowConnectColumn)
                <div class="space-y-8">
                    @if ($footerHasDownloadLinks)
                        <div>
                            <h4 class="font-semibold text-white mb-4">{{ __('front::home.footer_get_app') }}</h4>
                            <ul class="space-y-2 text-sm">
                                @if (!empty($footerAppStoreUrl))
                                    <li>
                                        <a href="{{ $footerAppStoreUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 hover:text-red-400 transition">
                                            <i class="fab fa-app-store-ios text-lg w-5 text-center shrink-0" aria-hidden="true"></i>
                                            <span>{{ __('front::home.footer_app_store') }}</span>
                                        </a>
                                    </li>
                                @endif
                                @if (!empty($footerGooglePlayUrl))
                                    <li>
                                        <a href="{{ $footerGooglePlayUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 hover:text-red-400 transition">
                                            <i class="fab fa-google-play text-lg w-5 text-center shrink-0" aria-hidden="true"></i>
                                            <span>{{ __('front::home.footer_google_play') }}</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    @endif
                    @if ($footerHasSocial)
                        <div>
                            <h4 class="font-semibold text-white mb-4">{{ __('front::home.footer_follow') }}</h4>
                            <div class="flex flex-wrap gap-4 text-2xl">
                                @foreach ($footerSocial as $item)
                                    <a href="{{ $item['url'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-300 hover:text-red-400 transition" aria-label="{{ $item['label'] }}">
                                        <i class="{{ $item['icon'] }}" aria-hidden="true"></i>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
        <div class="border-t border-gray-800/80 mt-10 pt-6 text-center text-sm text-gray-500">
            &copy; {{ now()->year }} {{ __('front::home.brand') }} - {{ __('front::home.copyright_text') }}
        </div>
    </div>
</footer>
