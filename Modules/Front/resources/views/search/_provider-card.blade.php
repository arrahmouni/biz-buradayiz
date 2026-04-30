@php
    $isFeaturedCard = $isFeatured ?? false;
    $isNewCard = $provider->isNewProvider();
    $providerAvatarOnError = 'this.onerror=null;this.src='.(string) \Illuminate\Support\Js::from(provider_avatar_placeholder_url());
    $ratingAvg = (float) $provider->review_rating_average;
    $reviewsCount = (int) $provider->approved_reviews_count;
    $hasReviews = $reviewsCount > 0;
    $fullStars = floor($ratingAvg);
    $halfStar = ($ratingAvg - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
    $locationLine = $provider->provider_card_location_line ?? '';
    $tenureLabel = $provider->provider_card_platform_tenure_label ?? '';
    $serviceName = $provider->provider_card_service_name ?? '';
    $companyName = $provider->company_name ?? $provider->full_name;
    $profileUrl = route('front.provider.show', ['provider' => $provider->frontProfileSlug()]);
    $phoneNumber = trim((string) $provider->central_phone);
    $telHref = phoneToTelHref($phoneNumber);
@endphp

{{-- ========== كارد سطح المكتب (يظهر فقط للشاشات >= 768px) ========== --}}
<article @class([
    'provider-card--desktop bg-white rounded-2xl shadow-md hover:shadow-lg transition p-4 sm:p-5',
    'featured-provider-card border-red-200 border-l-4 border-l-red-600 ring-1 ring-red-100' => $isFeaturedCard,
    'border-gray-100' => ! $isFeaturedCard && ! $isNewCard,
])>
    <div class="flex flex-row gap-3 sm:gap-5">
        <div class="h-14 w-14 sm:h-32 sm:w-32 bg-gray-100 rounded-full sm:rounded-xl overflow-hidden flex-shrink-0">
            <img src="{{ $provider->image_url }}" alt="{{ $provider->full_name }}" class="w-full h-full object-cover" onerror="{{ $providerAvatarOnError }}">
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap justify-between items-start gap-2">
                <h2 class="text-base sm:text-xl font-bold text-gray-800 leading-snug flex flex-wrap items-center gap-2">
                    <a href="{{ $profileUrl }}" class="text-gray-800 hover:text-red-600 transition">{{ $companyName }}</a>
                    @if ($isFeaturedCard)
                        <span class="provider-featured-badge"><i class="fas fa-award"></i> {{ __('front::home.search_featured_badge') }}</span>
                    @endif
                    @if ($isNewCard)
                        <span class="provider-new-badge"><i class="fas fa-rocket"></i> {{ __('front::home.provider_new_badge') }}</span>
                    @endif
                </h2>
                @if ($provider->service && filled($provider->service->icon))
                    <span class="hidden sm:inline text-red-600 shrink-0"><i class="{{ $provider->service->icon }} text-lg sm:text-xl"></i></span>
                @endif
            </div>
            <p class="text-sm text-gray-600 font-medium mt-1">{{ $provider->full_name }}</p>
            <div class="flex flex-wrap items-center gap-x-2 sm:gap-x-3 gap-y-1 text-xs sm:text-sm text-gray-500 mt-0.5 sm:mt-1">
                @if ($hasReviews)
                    <span>
                        <i class="fas fa-star text-yellow-400"></i> {{ number_format($ratingAvg, 1) }}
                        ({{ trans_choice('front::home.provider_card_reviews', $reviewsCount, ['count' => $reviewsCount]) }})
                    </span>
                @else
                    <span>{{ __('front::home.provider_card_no_reviews') }}</span>
                @endif
                @if (filled($locationLine))
                    <span><i class="fas fa-map-marker-alt text-red-400"></i> {{ $locationLine }}</span>
                @endif
            </div>
            @if (filled($tenureLabel))
                <p class="text-gray-600 text-xs sm:text-sm mt-1.5 sm:mt-2"><i class="fas fa-clock text-red-500 mr-1"></i>{{ $tenureLabel }} {{ __('front::home.provider_card_platform_tenure_label') }}</p>
            @endif
            <div class="mt-2 sm:mt-3 flex flex-wrap gap-1.5 sm:gap-2 items-center">
                <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full"><i class="fas fa-wrench"></i> {{ $serviceName }}</span>
            </div>
        </div>
    </div>
    @if (filled($phoneNumber))
        <div class="mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-gray-200">
            <div class="rounded-xl border border-red-100 bg-gradient-to-br from-red-50 via-white to-red-50/60 shadow-sm p-3 sm:p-4 md:p-5 flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-6 sm:justify-between">
                <div class="flex items-start gap-3 sm:gap-4 min-w-0 flex-1">
                    <div class="flex h-10 w-10 sm:h-12 sm:w-12 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600 ring-2 sm:ring-4 ring-white shadow-sm"><i class="fas fa-phone-alt text-base sm:text-lg"></i></div>
                    <div class="min-w-0 pt-0.5">
                        <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-red-700/90">{{ __('front::home.provider_card_call_now') }}</p>
                        <a href="{{ $telHref }}" class="mt-0.5 sm:mt-1 block text-lg sm:text-2xl md:text-3xl font-bold text-gray-900 hover:text-red-700 tracking-tight break-all leading-snug transition-colors">{{ $phoneNumber }}</a>
                    </div>
                </div>
                <a href="{{ $telHref }}" class="hidden sm:inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 sm:px-6 sm:py-3 rounded-full text-xs sm:text-sm font-semibold transition shadow-md hover:shadow-lg shrink-0 sm:min-w-[10rem]"><i class="fas fa-phone-alt"></i> {{ __('front::home.provider_card_call_provider') }}</a>
            </div>
        </div>
    @endif
</article>

{{-- ========== كارد الموبايل المضغوط جداً (يظهر فقط للشاشات < 768px) ========== --}}
<article @class([
    'provider-card--mobile bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-2',
    'featured-provider-card-mobile border-l-3 border-l-red-500' => $isFeaturedCard,
])>
    <div class="p-3 pb-2.5">
        <div class="flex items-start gap-3">
            {{-- الصورة المصغرة --}}
            <div class="flex-shrink-0">
                <img src="{{ $provider->image_url }}" alt="{{ $provider->full_name }}" class="w-10 h-10 rounded-full object-cover border border-gray-200 shadow-sm" onerror="{{ $providerAvatarOnError }}">
            </div>

            {{-- معلومات الاسم والتقييم --}}
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-x-1.5 gap-y-0.5">
                    <a href="{{ $profileUrl }}" class="font-semibold text-gray-900 text-sm truncate max-w-[140px] hover:text-red-600">{{ $companyName }}</a>
                    @if ($isFeaturedCard)
                        <span class="inline-flex items-center text-[9px] font-bold bg-red-100 text-red-700 px-1.5 py-0.5 rounded-full"><i class="fas fa-award text-[8px] mr-0.5"></i>{{ __('front::home.search_featured_badge') }}</span>
                    @endif
                    @if ($isNewCard)
                        <span class="inline-flex items-center text-[9px] font-bold bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded-full"><i class="fas fa-rocket text-[8px] mr-0.5"></i>{{ __('front::home.provider_new_badge') }}</span>
                    @endif
                </div>
                <div class="flex items-center gap-1.5 mt-0.5">
                    @if ($hasReviews)
                        <div class="flex items-center gap-0.5">
                            @for($i=0; $i<$fullStars; $i++) <i class="fas fa-star text-yellow-400 text-[11px]"></i> @endfor
                            @if($halfStar) <i class="fas fa-star-half-alt text-yellow-400 text-[11px]"></i> @endif
                            @for($i=0; $i<$emptyStars; $i++) <i class="far fa-star text-gray-300 text-[11px]"></i> @endfor
                        </div>
                        <span class="text-[11px] font-medium text-gray-700">{{ number_format($ratingAvg, 1) }}</span>
                        <span class="text-[11px] text-gray-500">({{ $reviewsCount }})</span>
                    @else
                        <span class="text-[11px] text-gray-500">{{ __('front::home.provider_card_no_reviews') }}</span>
                    @endif
                </div>
                {{-- الخدمة --}}
                <div class="mt-1">
                    <span class="inline-flex items-center gap-0.5 bg-gray-100 text-gray-700 text-[10px] px-2 py-0.5 rounded-full"><i class="fas fa-wrench text-[9px]"></i> {{ Str::limit($serviceName, 22) }}</span>
                </div>
            </div>
        </div>

        <a href="{{ $telHref }}" class="provider-card-mobile__call-link">
            <span class="provider-card-mobile__call-link-icon">
                <i class="fas fa-phone-alt"></i>
            </span>
            @if (filled($phoneNumber))
                <span class="provider-card-mobile__call-link-body">
                    {{-- <span class="provider-card-mobile__call-link-label">{{ __('front::home.provider_card_call_now') }}</span> --}}
                    <span class="provider-card-mobile__call-link-number">{{ $phoneNumber }}</span>
                </span>
            @else
                <span class="provider-card-mobile__call-link-fallback">{{ __('front::home.provider_card_call_provider') }}</span>
            @endif
        </a>

        {{-- السطر الثاني: الموقع ومدة العضوية --}}
        @if(filled($locationLine) || filled($tenureLabel))
            <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mt-2 pt-1.5 border-t border-gray-100 text-[10px] text-gray-500">
                @if(filled($locationLine))
                    <span class="inline-flex items-center gap-1"><i class="fas fa-map-marker-alt text-red-400 text-[9px]"></i> {{ Str::limit($locationLine, 30) }}</span>
                @endif
                @if(filled($tenureLabel))
                    <span class="inline-flex items-center gap-1"><i class="fas fa-clock text-red-400 text-[9px]"></i> {{ Str::limit($tenureLabel, 25) }}</span>
                @endif
            </div>
        @endif
    </div>
</article>
