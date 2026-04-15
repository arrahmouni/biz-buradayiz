<article class="bg-white rounded-2xl shadow-md hover:shadow-lg transition p-4 sm:p-5 border {{ $isFeatured ?? false ? 'border-red-200 ring-1 ring-red-100' : 'border-gray-100' }}">
    @if ($isFeatured ?? false)
        <div class="mb-2">
            <span class="featured-provider-badge">
                <i class="fas fa-award" aria-hidden="true"></i> {{ __('front::home.search_featured_badge') }}
            </span>
        </div>
    @endif
    <div class="flex flex-row gap-3 sm:gap-5">
        <div class="h-14 w-14 sm:h-32 sm:w-32 bg-gray-100 rounded-full sm:rounded-xl overflow-hidden flex-shrink-0">
            <img src="{{ $provider->image_url }}" alt="{{ $provider->full_name }}" class="w-full h-full object-cover">
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap justify-between items-start gap-2">
                <h2 class="text-base sm:text-xl font-bold text-gray-800 leading-snug">
                    <a href="{{ route('front.provider.show', ['provider' => $provider->frontProfileSlug()]) }}" class="text-gray-800 hover:text-red-600 transition">
                        {{ $provider->full_name }}
                    </a>
                </h2>
                @if ($provider->service && filled($provider->service->icon))
                    <span class="text-red-600 shrink-0" aria-hidden="true"><i class="{{ $provider->service->icon }} text-lg sm:text-xl"></i></span>
                @endif
            </div>
            <div class="flex flex-wrap items-center gap-x-2 sm:gap-x-3 gap-y-1 text-xs sm:text-sm text-gray-500 mt-0.5 sm:mt-1">
                @if ((int) $provider->approved_reviews_count > 0)
                    <span>
                        <i class="fas fa-star text-yellow-400" aria-hidden="true"></i>
                        {{ number_format((float) $provider->review_rating_average, 1) }}
                        ({{ trans_choice('front::home.provider_card_reviews', $provider->approved_reviews_count, ['count' => $provider->approved_reviews_count]) }})
                    </span>
                @else
                    <span>{{ __('front::home.provider_card_no_reviews') }}</span>
                @endif
                @if (filled($provider->provider_card_location_line))
                    <span><i class="fas fa-map-marker-alt text-red-400" aria-hidden="true"></i> {{ $provider->provider_card_location_line }}</span>
                @endif
            </div>
            @if (filled($provider->provider_card_service_description))
                <p class="text-gray-600 text-xs sm:text-sm mt-1.5 sm:mt-2 line-clamp-2 sm:line-clamp-none">{{ \Illuminate\Support\Str::limit(strip_tags($provider->provider_card_service_description), 220) }}</p>
            @endif
            <div class="mt-2 sm:mt-3 flex flex-wrap gap-1.5 sm:gap-2 items-center">
                <a href="{{ route('front.provider.show', ['provider' => $provider->frontProfileSlug()]) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-red-600 hover:text-red-700 transition">
                    {{ __('front::home.provider_detail_view_profile') }}
                    <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i>
                </a>
                <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">
                    <i class="fas fa-wrench" aria-hidden="true"></i> {{ $provider->provider_card_service_name }}
                </span>
                @if (filled($provider->email))
                    <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full truncate max-w-full">
                        <i class="fas fa-envelope" aria-hidden="true"></i> {{ $provider->email }}
                    </span>
                @endif
            </div>
        </div>
    </div>
    @if (filled($provider->central_phone))
        @php
            $telHref = phoneToTelHref(trim((string) $provider->central_phone));
        @endphp
        <div class="mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-gray-200">
            <div class="rounded-xl border border-red-100 bg-gradient-to-br from-red-50 via-white to-red-50/60 shadow-sm p-3 sm:p-4 md:p-5 flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-6 sm:justify-between">
                <div class="flex items-start gap-3 sm:gap-4 min-w-0 flex-1">
                    <div class="flex h-10 w-10 sm:h-12 sm:w-12 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600 ring-2 sm:ring-4 ring-white shadow-sm" aria-hidden="true">
                        <i class="fas fa-phone-alt text-base sm:text-lg"></i>
                    </div>
                    <div class="min-w-0 pt-0.5">
                        <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-red-700/90">{{ __('front::home.provider_card_call_now') }}</p>
                        <a href="{{ $telHref }}" class="mt-0.5 sm:mt-1 block text-lg sm:text-2xl md:text-3xl font-bold text-gray-900 hover:text-red-700 tracking-tight break-all leading-snug transition-colors">
                            {{ trim((string) $provider->central_phone) }}
                        </a>
                    </div>
                </div>
                <a href="{{ $telHref }}" class="inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 sm:px-6 sm:py-3 rounded-full text-xs sm:text-sm font-semibold transition shadow-md hover:shadow-lg shrink-0 sm:min-w-[10rem]">
                    <i class="fas fa-phone-alt" aria-hidden="true"></i> {{ __('front::home.provider_card_call_provider') }}
                </a>
            </div>
        </div>
    @endif
</article>
