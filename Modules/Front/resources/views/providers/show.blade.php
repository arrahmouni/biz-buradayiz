
@php
    $serviceDescription = $provider->provider_card_service_description;
    $metaTitle = $provider->full_name;
    $metaDescription = filled($serviceDescription)
        ? \Illuminate\Support\Str::limit(strip_tags($serviceDescription), 160)
        : __('front::home.provider_detail_meta_fallback', ['name' => $provider->full_name]);
@endphp

@extends('front::layouts.master', ['title' => $metaTitle])

@section('meta_description', $metaDescription)

@section('content')
    <x-front::page-hero
        :heading="__('front::home.provider_detail_hero_heading')"
        heading-tag="h2"
        :breadcrumb-label="__('front::home.provider_detail_breadcrumb_label')"
    >
        <x-slot name="breadcrumb">
            <a href="{{ route('front.index') }}" class="hover:text-red-400 transition">{{ __('front::home.nav_home') }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('front.search') }}" class="hover:text-red-400 transition">{{ __('front::home.search_results_title') }}</a>
            <span class="mx-2">/</span>
            <span class="text-white font-medium">{{ $provider->full_name }}</span>
        </x-slot>
    </x-front::page-hero>

    @if (! empty($ownerPreviewWithoutActiveSubscription))
        <div class="bg-amber-50 border-b border-amber-200">
            <div class="container mx-auto px-5 py-4 lg:px-8">
                <div class="flex gap-3 rounded-xl border border-amber-200 bg-white/80 px-4 py-3 text-amber-950 shadow-sm" role="alert">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700" aria-hidden="true">
                        <i class="fas fa-info-circle"></i>
                    </span>
                    <p class="min-w-0 text-sm font-medium leading-relaxed md:text-base">{{ __('front::home.provider_profile_owner_subscription_required') }}</p>
                </div>
            </div>
        </div>
    @endif

    <section @class([
        'bg-gray-50 pt-8 md:pt-12',
        'pb-8 md:pb-12' => ! filled($provider->central_phone),
        'pb-36 lg:pb-12' => filled($provider->central_phone),
    ])>
        <div class="container mx-auto px-5 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-8 lg:gap-10">
                <div class="lg:w-2/3 space-y-6 min-w-0">
                    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
                        <div class="flex flex-col sm:flex-row gap-6 p-6 md:p-8">
                            <div class="sm:w-40 h-40 bg-gray-100 rounded-2xl overflow-hidden shrink-0 mx-auto sm:mx-0">
                                <img src="{{ $provider->image_url }}" alt="{{ $provider->full_name }}" class="w-full h-full object-cover">
                            </div>
                            <div class="min-w-0 flex-1 text-center sm:text-left">
                                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">{{ __('front::home.provider_detail_about_title') }}</p>
                                <h1 class="mt-2 text-2xl md:text-3xl font-bold text-gray-900 tracking-tight">{{ $provider->full_name }}</h1>
                                <div class="mt-3 flex flex-wrap items-center justify-center sm:justify-start gap-3 text-sm text-gray-600">
                                    @if ((int) $provider->approved_reviews_count > 0)
                                        <span>
                                            <i class="fas fa-star text-yellow-400" aria-hidden="true"></i>
                                            {{ number_format((float) $provider->review_rating_average, 1) }}
                                            <span class="text-gray-400">·</span>
                                            {{ trans_choice('front::home.provider_card_reviews', $provider->approved_reviews_count, ['count' => $provider->approved_reviews_count]) }}
                                        </span>
                                    @else
                                        <span>{{ __('front::home.provider_card_no_reviews') }}</span>
                                    @endif
                                    @if (filled($provider->provider_card_location_line))
                                        <span><i class="fas fa-map-marker-alt text-red-500" aria-hidden="true"></i> {{ $provider->provider_card_location_line }}</span>
                                    @endif
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                                        @if ($provider->service && filled($provider->service->icon))
                                            <i class="{{ $provider->service->icon }} text-red-600" aria-hidden="true"></i>
                                        @else
                                            <i class="fas fa-wrench text-red-600" aria-hidden="true"></i>
                                        @endif
                                        {{ $provider->provider_card_service_name }}
                                    </span>
                                </div>
                                <p class="mt-4 text-gray-700 leading-relaxed">
                                    @if (filled($serviceDescription))
                                        <span class="block [&_a]:text-red-600 [&_a]:underline">{!! $provider->service?->smartTrans('description') !!}</span>
                                    @else
                                        {{ __('front::home.provider_detail_about_empty') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6 md:p-8">
                        <h2 class="text-xl font-bold text-gray-800">{{ __('front::home.provider_detail_reviews_title') }}</h2>
                        <p class="text-sm text-gray-600 mt-1">{{ __('front::home.provider_detail_reviews_intro') }}</p>

                        @if ($reviews->count() === 0)
                            <p class="mt-6 text-center text-gray-500 text-sm py-8 border border-dashed border-gray-200 rounded-xl bg-gray-50/80">
                                <i class="fa-regular fa-comment-dots text-2xl text-gray-400 block mb-2" aria-hidden="true"></i>
                                {{ __('front::home.provider_detail_reviews_empty') }}
                            </p>
                        @else
                            <div
                                data-provider-reviews
                                data-next-page-url="{{ e($reviews->hasMorePages() ? route('front.provider.reviews.fragment', ['provider' => $provider->profile_slug, 'page' => $reviews->currentPage() + 1]) : '') }}"
                            >
                                <ul class="mt-6 divide-y divide-gray-100 border-t border-gray-100" data-reviews-list>
                                    @include('front::providers.partials.review-items', ['reviews' => $reviews])
                                </ul>
                                <div class="mt-4 hidden" data-reviews-loader>
                                    <div class="flex items-center justify-center">
                                        <span class="inline-flex items-center gap-2 text-sm text-gray-500">
                                            <i class="fas fa-circle-notch fa-spin" aria-hidden="true"></i>
                                            Loading…
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-4 hidden text-center text-sm text-gray-500" data-reviews-end>
                                    No more reviews.
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <aside class="lg:w-1/3 space-y-6">
                    @if (filled($provider->central_phone))
                        @php
                            $telHref = phoneToTelHref(trim((string) $provider->central_phone));
                            $phoneDisplay = trim((string) $provider->central_phone);
                        @endphp
                        <div
                            class="provider-call-dock rounded-2xl border border-red-100 bg-gradient-to-br from-red-50 via-white to-red-50/60 shadow-md max-lg:fixed max-lg:inset-x-0 max-lg:bottom-0 max-lg:z-40 max-lg:rounded-b-none max-lg:rounded-t-2xl max-lg:border-b-0 max-lg:border-x-0 max-lg:border-t max-lg:border-red-100 max-lg:p-4 max-lg:shadow-[0_-8px_32px_rgba(0,0,0,0.12)] max-lg:pb-[max(1rem,env(safe-area-inset-bottom))] lg:relative lg:p-6 lg:shadow-md"
                            role="region"
                            aria-label="{{ __('front::home.provider_card_call_now') }}"
                        >
                            <p class="text-xs font-semibold uppercase tracking-wider text-red-700/90">{{ __('front::home.provider_card_call_now') }}</p>
                            <div class="mt-2 flex flex-col gap-3 max-lg:flex-row max-lg:items-center max-lg:justify-between max-lg:gap-4 lg:block lg:gap-0">
                                <a href="{{ $telHref }}" class="min-w-0 text-xl font-bold text-gray-900 hover:text-red-700 break-all leading-snug transition-colors lg:mt-0 lg:block lg:text-2xl">
                                    {{ $phoneDisplay }}
                                </a>
                                <a href="{{ $telHref }}" class="inline-flex shrink-0 items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white px-5 py-3 rounded-full text-sm font-semibold transition shadow-md hover:shadow-lg max-lg:min-h-[2.75rem] lg:mt-4 lg:w-full">
                                    <i class="fas fa-phone-alt" aria-hidden="true"></i> {{ __('front::home.provider_card_call_provider') }}
                                </a>
                            </div>
                        </div>
                    @endif

                    @if (filled($provider->email))
                        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">{{ __('front::home.provider_card_email') }}</h3>
                            <a href="mailto:{{ $provider->email }}" class="mt-2 block text-red-600 font-medium hover:text-red-700 break-all">{{ $provider->email }}</a>
                        </div>
                    @endif

                    <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6 md:p-7">
                        <h3 class="text-lg font-bold text-gray-800">{{ __('front::home.provider_detail_feedback_title') }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ __('front::home.provider_detail_feedback_note') }}</p>

                        @if (session('success'))
                            <div class="mt-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" role="status">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form id="providerReviewDesignForm" class="mt-6 space-y-5" action="{{ route('front.provider.reviews.store', $provider->profile_slug) }}" method="post">
                            @csrf
                            <div
                                data-provider-rating
                                class="provider-rating-field"
                                data-rating-selected-template="{{ e(__('front::home.provider_detail_rating_selected')) }}"
                            >
                                <span id="providerRatingLegend" class="block text-sm font-medium text-gray-700 mb-2">{{ __('front::home.provider_detail_rate_label') }}</span>
                                <div class="provider-rating-field__stars" role="group" aria-labelledby="providerRatingLegend">
                                    @for ($r = 1; $r <= 5; $r++)
                                        <button type="button" class="provider-rating-field__star" data-rating-value="{{ $r }}" aria-pressed="false" aria-label="{{ __('front::home.provider_detail_star_aria', ['n' => $r]) }}">
                                            <i class="fa-solid fa-star" aria-hidden="true"></i>
                                        </button>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" value="{{ old('rating') }}">
                                <p class="provider-rating-field__summary mt-2 text-sm text-gray-500" data-rating-summary aria-live="polite">{{ __('front::home.provider_detail_rating_unselected') }}</p>
                                @error('rating')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="providerReviewPhone" class="block text-sm font-medium text-gray-700 mb-2">{{ __('front::home.provider_detail_phone_label') }}</label>
                                <input required id="providerReviewPhone" type="text" name="phone" value="{{ old('phone') }}" inputmode="tel" autocomplete="tel" class="w-full rounded-xl border px-4 py-3 text-gray-800 placeholder-gray-400 focus:border-red-400 focus:ring-2 focus:ring-red-200 outline-none transition @error('phone') border-red-500 @else border-gray-200 @enderror" placeholder="{{ __('front::home.provider_detail_phone_placeholder') }}">
                                <p class="mt-1 text-xs text-gray-500">{{ __('front::home.provider_detail_phone_help') }}</p>
                                @error('phone')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="providerReviewName" class="block text-sm font-medium text-gray-700 mb-2">{{ __('front::home.provider_detail_display_name_label') }}</label>
                                <input required id="providerReviewName" type="text" name="display_name" value="{{ old('display_name') }}" autocomplete="name" class="w-full rounded-xl border px-4 py-3 text-gray-800 placeholder-gray-400 focus:border-red-400 focus:ring-2 focus:ring-red-200 outline-none transition @error('display_name') border-red-500 @else border-gray-200 @enderror" placeholder="{{ __('front::home.provider_detail_display_name_placeholder') }}">
                                @error('display_name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="providerReviewComment" class="block text-sm font-medium text-gray-700 mb-2">{{ __('front::home.provider_detail_comment_label') }}</label>
                                <textarea required id="providerReviewComment" name="comment" rows="4" class="w-full rounded-xl border px-4 py-3 text-gray-800 placeholder-gray-400 focus:border-red-400 focus:ring-2 focus:ring-red-200 outline-none transition @error('comment') border-red-500 @else border-gray-200 @enderror" placeholder="{{ __('front::home.provider_detail_comment_placeholder') }}">{{ old('comment') }}</textarea>
                                @error('comment')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-gray-800 hover:bg-gray-900 text-white px-6 py-3 rounded-full text-sm font-semibold transition shadow-md">
                                <i class="fas fa-paper-plane" aria-hidden="true"></i> {{ __('front::home.provider_detail_submit_feedback') }}
                            </button>
                        </form>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        (function () {
            const root = document.querySelector('[data-provider-rating]');
            if (!root) {
                return;
            }

            const buttons = root.querySelectorAll('[data-rating-value]');
            const hidden = root.querySelector('input[name="rating"]');
            const summary = root.querySelector('[data-rating-summary]');
            const unselectedText = summary ? summary.textContent : '';
            const selectedTemplate = root.getAttribute('data-rating-selected-template') || '';

            function setRating(value) {
                const n = parseInt(value, 10);
                if (!hidden) {
                    return;
                }
                if (!Number.isFinite(n) || n < 1 || n > 5) {
                    hidden.value = '';
                    buttons.forEach(function (btn) {
                        btn.classList.remove('is-selected');
                        btn.setAttribute('aria-pressed', 'false');
                    });
                    if (summary) {
                        summary.textContent = unselectedText;
                    }
                    return;
                }

                hidden.value = String(n);
                buttons.forEach(function (btn) {
                    const v = parseInt(btn.getAttribute('data-rating-value'), 10);
                    const on = v <= n;
                    btn.classList.toggle('is-selected', on);
                    btn.setAttribute('aria-pressed', on ? 'true' : 'false');
                });
                if (summary && selectedTemplate) {
                    summary.textContent = selectedTemplate.replace(':n', String(n));
                }
            }

            buttons.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const v = btn.getAttribute('data-rating-value');
                    if (hidden && hidden.value === v) {
                        setRating('');
                    } else {
                        setRating(v);
                    }
                });
            });

            if (hidden && hidden.value) {
                setRating(hidden.value);
            }
        })();
    </script>
    <script>
        (function () {
            const root = document.querySelector('[data-provider-reviews]');
            if (!root) {
                return;
            }

            const list = root.querySelector('[data-reviews-list]');
            const loader = root.querySelector('[data-reviews-loader]');
            const end = root.querySelector('[data-reviews-end]');
            let nextPageUrl = root.getAttribute('data-next-page-url') || '';
            let loading = false;
            let loadedExtraPage = false;

            function setLoading(on) {
                loading = on;
                if (loader) {
                    loader.classList.toggle('hidden', !on);
                }
            }

            function clearNextPage() {
                nextPageUrl = '';
                root.setAttribute('data-next-page-url', '');
            }

            function markPaginationFinished() {
                clearNextPage();
                if (end && loadedExtraPage) {
                    end.classList.remove('hidden');
                }
            }

            function abortPaginationSilently() {
                clearNextPage();
            }

            async function loadNext() {
                if (loading || !nextPageUrl || !list) {
                    return;
                }

                setLoading(true);
                try {
                    const res = await fetch(nextPageUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            Accept: 'application/json',
                        },
                    });
                    if (!res.ok) {
                        throw new Error('Request failed');
                    }
                    const data = await res.json();

                    loadedExtraPage = true;
                    if (data && typeof data.html === 'string' && data.html.trim() !== '') {
                        const tmp = document.createElement('div');
                        tmp.innerHTML = data.html;
                        while (tmp.firstChild) {
                            list.appendChild(tmp.firstChild);
                        }
                    }

                    const incomingNext = data && data.next_page_url ? String(data.next_page_url) : '';
                    nextPageUrl = incomingNext;
                    root.setAttribute('data-next-page-url', incomingNext);
                    if (!incomingNext) {
                        markPaginationFinished();
                    }
                } catch (e) {
                    abortPaginationSilently();
                } finally {
                    setLoading(false);
                }
            }

            function shouldLoadMore() {
                if (!nextPageUrl || loading) {
                    return false;
                }
                const rect = root.getBoundingClientRect();
                return rect.bottom - window.innerHeight < 450;
            }

            function onScroll() {
                if (shouldLoadMore()) {
                    loadNext();
                }
            }

            window.addEventListener('scroll', onScroll, { passive: true });
            window.addEventListener('resize', onScroll);
            onScroll();
        })();
    </script>
@endpush
