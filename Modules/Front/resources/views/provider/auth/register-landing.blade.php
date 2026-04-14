@extends('front::layouts.master')

@section('content')
    <!-- Hero Section with subtle pattern -->
    <section class="relative bg-gradient-to-br from-red-900 via-red-800 to-red-900 text-white overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0 bg-[length:24px_24px] bg-[radial-gradient(circle_at_20%_40%,rgba(255,255,255,0.2)_2px,transparent_2px)]"></div>
        </div>
        <div class="container mx-auto px-4 py-16 md:py-24 relative z-10">
            <div class="max-w-3xl">
                <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight leading-tight">
                    {{ __('front::provider_register.hero_title') }}
                </h1>
                <p class="mt-4 text-lg md:text-xl text-red-100 leading-relaxed">
                    {{ __('front::provider_register.hero_subtitle') }}
                </p>
                <div class="mt-8 flex flex-col sm:flex-row gap-4 sm:items-center">
                    <a href="{{ route('front.provider.register.form') }}"
                       class="inline-flex items-center justify-center gap-2 bg-white text-red-600 px-8 py-3.5 rounded-full font-bold text-base shadow-lg hover:bg-gray-50 hover:scale-105 transition-transform duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-red-700">
                        <i class="fas fa-user-plus"></i> {{ __('front::provider_register.cta_apply') }}
                    </a>
                    <a href="{{ route('front.provider.login') }}"
                       class="inline-flex items-center justify-center gap-2 text-white font-semibold border-2 border-white/80 hover:bg-white/10 px-8 py-3.5 rounded-full transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-red-700">
                        {{ __('front::provider_register.cta_secondary_login') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="container mx-auto px-5 py-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
            <div>
                <span class="text-2xl font-bold text-red-600">{{ __('front::provider_register.stats_providers_value') }}</span>
                <p class="text-gray-600 text-sm">{{ __('front::provider_register.stats_providers_label') }}</p>
            </div>
            <div>
                <span class="text-2xl font-bold text-red-600">{{ __('front::provider_register.stats_rescues_value') }}</span>
                <p class="text-gray-600 text-sm">{{ __('front::provider_register.stats_rescues_label') }}</p>
            </div>
            <div>
                <span class="text-2xl font-bold text-red-600">{{ __('front::provider_register.stats_rating_value') }}</span>
                <p class="text-gray-600 text-sm">{{ __('front::provider_register.stats_rating_label') }}</p>
            </div>
            <div>
                <span class="text-2xl font-bold text-red-600">{{ __('front::provider_register.stats_support_value') }}</span>
                <p class="text-gray-600 text-sm">{{ __('front::provider_register.stats_support_label') }}</p>
            </div>
        </div>
    </div>

    <!-- Packages Section (improved) -->
    <section class="bg-gray-50 py-16 md:py-20">
        <div class="container mx-auto px-5 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-red-600 font-semibold uppercase tracking-wide">{{ __('front::provider_register.packages_kicker') }}</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-2">{{ __('front::provider_register.packages_heading') }}</h2>
                <p class="text-gray-600 mt-3">{{ __('front::provider_register.packages_intro') }}</p>
            </div>

            @if ($servicesWithPackages->isEmpty())
                <p class="text-center text-gray-600 max-w-xl mx-auto">{{ __('front::provider_register.packages_empty') }}</p>
                <div class="mt-8 text-center">
                    <a href="{{ route('front.provider.register.form') }}" class="inline-flex items-center gap-2 font-semibold text-red-600 hover:text-red-700 transition">
                        {{ __('front::provider_register.cta_apply') }} <i class="fas fa-arrow-right text-sm"></i>
                    </a>
                </div>
            @else
                <div class="js-provider-register-landing-tabs max-w-6xl mx-auto">
                    <!-- Tabs with better styling -->
                    <div class="flex flex-wrap justify-center gap-2 border-b border-gray-200 mb-10" role="tablist">
                        @foreach ($servicesWithPackages as $index => $service)
                            @php($serviceName = $service->smartTrans('name') ?: (string) $service->id)
                            <button type="button"
                                class="js-prl-tab group px-5 py-2.5 text-sm md:text-base font-semibold border-b-2 transition-all duration-200 hover:text-red-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 rounded-t-lg {{ $index === 0 ? 'border-red-500 text-red-600' : 'border-transparent text-gray-600 hover:border-gray-300' }}"
                                role="tab"
                                aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                                data-prl-tab="{{ $index }}">
                                @if (filled($service->icon))
                                    <i class="{{ $service->icon }} mr-2" aria-hidden="true"></i>
                                @endif
                                {{ $serviceName }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Tab Panels with smooth transition -->
                    <div class="relative">
                        @foreach ($servicesWithPackages as $index => $service)
                            <div class="js-prl-panel transition-opacity duration-300 {{ $index === 0 ? '' : 'hidden opacity-0' }}" role="tabpanel" data-prl-panel="{{ $index }}" @if ($index !== 0) hidden @endif>
                                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    @foreach ($service->packages as $package)
                                        @php($billingKey = 'front::provider_register.billing_'.$package->billing_period->value)
                                        @php($currencySymbol = getCurrencySymbol($package->currency))
                                        @php($featureLines = $package->features_segments)
                                        @php($isPopular = (bool) $package->is_popular)
                                        @php($centerLonePopular = $isPopular && $service->packages->count() === 1)
                                        <div @class([
                                            'bg-white rounded-2xl shadow-lg border-2 transition-all duration-300 flex flex-col hover:shadow-xl hover:-translate-y-0.5',
                                            'border-red-200 ring-2 ring-red-500 scale-105 md:scale-100 z-10 hover:border-red-500 hover:ring-red-600 order-first md:order-none' => $isPopular,
                                            'border-transparent hover:border-red-400' => ! $isPopular,
                                            'lg:col-start-2' => $centerLonePopular,
                                        ])>
                                            @if ($isPopular)
                                                <div class="relative">
                                                    <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-red-600 text-white text-xs font-bold px-4 py-1 rounded-full shadow-md">
                                                        {{ __('front::provider_register.package_popular') }}
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="p-6 flex-1 flex flex-col {{ $isPopular ? 'pt-8' : '' }}">
                                                <h3 class="text-xl font-bold text-gray-800">{{ $package->smartTrans('name') ?: ('#'.$package->id) }}</h3>
                                                <div class="mt-4 flex items-baseline flex-wrap gap-x-1">
                                                    <span class="text-4xl font-extrabold text-gray-900">{{ $currencySymbol }}{{ number_format((float) $package->price, 2) }}</span>
                                                    <span class="text-gray-500 text-sm font-semibold">/</span>
                                                    <span class="text-gray-500 text-sm">{{ __($billingKey) }}</span>
                                                </div>
                                                @if (filled($package->smartTrans('description')))
                                                    <p class="text-gray-500 text-sm mt-2">{{ $package->smartTrans('description') }}</p>
                                                @endif
                                                <p class="text-gray-600 text-sm mt-2 flex items-center gap-1"><i class="fas fa-exchange-alt text-red-400"></i> {{ __('front::provider_register.connections', ['count' => $package->connections_count]) }}</p>
                                                @if ($featureLines !== [])
                                                    <ul class="mt-6 space-y-3 flex-1">
                                                        @foreach ($featureLines as $line)
                                                            <li class="flex items-start gap-2 text-gray-600 text-sm">
                                                                <i class="fas fa-check-circle text-green-500 mt-0.5 shrink-0"></i>
                                                                <span>{{ $line }}</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                            <div class="px-6 pb-6">
                                                <a href="{{ route('front.provider.register.form') }}" class="block w-full text-center bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-lg transition-colors duration-200">
                                                    {{ __('front::provider_register.package_choose_plan') }}
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Bottom CTA after packages -->
                <div class="mt-12 text-center">
                    <a href="{{ route('front.provider.register.form') }}" class="inline-flex items-center justify-center gap-2 bg-red-600 text-white px-8 py-3.5 rounded-full font-bold shadow-md hover:bg-red-700 hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-user-plus"></i> {{ __('front::provider_register.cta_apply') }}
                    </a>
                </div>
            @endif
        </div>
    </section>

    <section class="bg-white py-16">
        <div class="container mx-auto px-5 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-red-600 font-semibold uppercase tracking-wide">{{ __('front::provider_register.benefits_kicker') }}</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-2">{{ __('front::provider_register.benefits_heading') }}</h2>
                <p class="text-gray-600 mt-3">{{ __('front::provider_register.benefits_intro') }}</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="flex flex-col items-center text-center p-6 rounded-xl bg-gray-50 hover:shadow-md transition">
                    <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mb-4"><i class="fas fa-chart-line text-red-600 text-xl" aria-hidden="true"></i></div>
                    <h3 class="text-xl font-bold text-gray-800">{{ __('front::provider_register.benefits_revenue_title') }}</h3>
                    <p class="text-gray-600 mt-2">{{ __('front::provider_register.benefits_revenue_text') }}</p>
                </div>
                <div class="flex flex-col items-center text-center p-6 rounded-xl bg-gray-50 hover:shadow-md transition">
                    <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mb-4"><i class="fas fa-mobile-alt text-red-600 text-xl" aria-hidden="true"></i></div>
                    <h3 class="text-xl font-bold text-gray-800">{{ __('front::provider_register.benefits_tools_title') }}</h3>
                    <p class="text-gray-600 mt-2">{{ __('front::provider_register.benefits_tools_text') }}</p>
                </div>
                <div class="flex flex-col items-center text-center p-6 rounded-xl bg-gray-50 hover:shadow-md transition">
                    <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mb-4"><i class="fas fa-shield-alt text-red-600 text-xl" aria-hidden="true"></i></div>
                    <h3 class="text-xl font-bold text-gray-800">{{ __('front::provider_register.benefits_payments_title') }}</h3>
                    <p class="text-gray-600 mt-2">{{ __('front::provider_register.benefits_payments_text') }}</p>
                </div>
            </div>
        </div>

    </section>

    @php($providerRegisterYoutubeRaw = trim((string) (getSetting('provider_register_landing_youtube_url') ?? '')))
    @php($providerRegisterYoutubeEmbed = youtubeEmbedSrcFromUrl($providerRegisterYoutubeRaw))
    @if ($providerRegisterYoutubeEmbed)
        <section class="bg-gray-100 py-16 md:py-20 border-t border-gray-200">
            <div class="container mx-auto px-5 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-10">
                    <span class="text-red-600 font-semibold uppercase tracking-wide">{{ __('front::provider_register.video_kicker') }}</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-2">{{ __('front::provider_register.video_heading') }}</h2>
                    <p class="text-gray-600 mt-3">{{ __('front::provider_register.video_intro') }}</p>
                </div>
                <div class="relative w-full max-w-4xl mx-auto aspect-video rounded-2xl overflow-hidden shadow-xl bg-black">
                    <iframe
                        class="absolute inset-0 w-full h-full border-0"
                        src="{{ $providerRegisterYoutubeEmbed }}"
                        title="{{ __('front::provider_register.video_embed_title') }}"
                        loading="lazy"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin"
                        allowfullscreen
                    ></iframe>
                </div>
            </div>
            <div class="text-center mt-10">
                <a href="{{ route('front.provider.register.form') }}" class="inline-flex items-center gap-2 font-semibold text-red-600 hover:text-red-700 transition">
                    {{ __('front::provider_register.benefits_cta_link') }} <i class="fas fa-arrow-right text-sm" aria-hidden="true"></i>
                </a>
            </div>
        </section>
    @endif

@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const root = document.querySelector('.js-provider-register-landing-tabs');
            if (!root) return;

            const tabs = root.querySelectorAll('.js-prl-tab');
            const panels = root.querySelectorAll('.js-prl-panel');

            function activateTab(index) {
                tabs.forEach((tab, i) => {
                    const isActive = i === parseInt(index);
                    tab.setAttribute('aria-selected', isActive);
                    if (isActive) {
                        tab.classList.add('border-red-500', 'text-red-600');
                        tab.classList.remove('border-transparent', 'text-gray-600');
                    } else {
                        tab.classList.remove('border-red-500', 'text-red-600');
                        tab.classList.add('border-transparent', 'text-gray-600');
                    }
                });
                panels.forEach((panel, i) => {
                    if (i === parseInt(index)) {
                        panel.classList.remove('hidden', 'opacity-0');
                        panel.removeAttribute('hidden');
                    } else {
                        panel.classList.add('hidden', 'opacity-0');
                        panel.setAttribute('hidden', 'hidden');
                    }
                });
            }

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const idx = tab.getAttribute('data-prl-tab');
                    if (idx !== null) activateTab(idx);
                });
            });
        });
    </script>
@endpush
