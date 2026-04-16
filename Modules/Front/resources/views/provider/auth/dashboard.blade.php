@extends('front::layouts.master')

@php
    use Modules\Platform\Enums\BillingPeriod;

    $user = $providerUser;
    $currentSub = $user->currentPackageSubscription;
    $snapshot = $currentSub?->snapshot;
@endphp

@section('content')
    <div class="bg-gradient-to-br from-red-900 via-red-800 to-red-900 text-white">
        <div class="container mx-auto px-4 py-10 md:py-14 lg:px-8">
            <div class="flex flex-col gap-6 md:flex-row md:items-start md:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-red-200/90">{{ __('front::provider_dashboard.page_title') }}</p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight md:text-4xl">{{ __('front::provider_dashboard.welcome', ['name' => $user->full_name]) }}</h1>
                    <p class="mt-2 text-red-100/95">{{ $user->email }}</p>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
                    <a href="{{ route('front.provider.account') }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-white/70 bg-white/10 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-red-800">
                        <i class="fas fa-user-cog" aria-hidden="true"></i> {{ __('front::provider_account.nav_account_settings') }}
                    </a>
                    <a href="{{ route('front.index') }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-white/70 bg-white/10 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-red-800">
                        <i class="fas fa-home" aria-hidden="true"></i> {{ __('front::provider_dashboard.nav_home') }}
                    </a>
                    @if (filled($user->profile_slug))
                        <a href="{{ route('front.provider.show', $user->profile_slug) }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-white/70 bg-white/10 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-red-800">
                            <i class="fas fa-external-link-alt" aria-hidden="true"></i> {{ __('front::provider_dashboard.nav_public_profile') }}
                        </a>
                    @endif
                    <form method="post" action="{{ route('front.provider.logout') }}" class="inline-flex">
                        @csrf
                        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-white px-5 py-2.5 text-sm font-bold text-red-700 shadow-md transition hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-red-800 sm:w-auto">
                            <i class="fas fa-sign-out-alt" aria-hidden="true"></i> {{ __('front::provider_dashboard.logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-10 lg:px-8 space-y-12">
        @php
            $totalCalls = $providerStats['total_calls'] ?? 0;
            $answeredCalls = $providerStats['answered_calls'] ?? 0;
            $subscriptionsCount = (int) ($providerStats['subscriptions_count'] ?? 0);
            $reviewsCount = (int) ($user->approved_reviews_count ?? 0);
            $ratingAvg = (float) ($user->review_rating_average ?? 0);
        @endphp

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border-l-4 border-red-500 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">{{ __('front::provider_dashboard.stats_total_calls') }}</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalCalls }}</p>
                    </div>
                    <i class="fas fa-phone-alt text-3xl text-red-400" aria-hidden="true"></i>
                </div>
            </div>
            <div class="rounded-2xl border-l-4 border-green-500 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">{{ __('front::provider_dashboard.stats_completed_calls') }}</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $answeredCalls }}</p>
                    </div>
                    <i class="fas fa-check-circle text-3xl text-green-400" aria-hidden="true"></i>
                </div>
            </div>
            <div class="rounded-2xl border-l-4 border-blue-500 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">{{ __('front::provider_dashboard.stats_subscriptions') }}</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $subscriptionsCount }}</p>
                        <p class="mt-1 text-xs text-gray-500">{{ __('front::provider_dashboard.stats_subscriptions_hint') }}</p>
                    </div>
                    <i class="fas fa-box-open text-3xl text-blue-400" aria-hidden="true"></i>
                </div>
            </div>
            <div class="rounded-2xl border-l-4 border-yellow-500 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">{{ __('front::provider_dashboard.stats_rating') }}</p>
                        <p class="text-2xl font-bold text-gray-800">
                            @if ($reviewsCount > 0)
                                {{ number_format($ratingAvg, 1) }} <span class="text-yellow-500" aria-hidden="true">★</span>
                            @else
                                {{ __('front::provider_dashboard.stats_rating_empty') }}
                            @endif
                        </p>
                        @if ($reviewsCount > 0)
                            <p class="mt-1 text-xs text-gray-500">{{ __('front::provider_dashboard.stats_rating_reviews', ['count' => $reviewsCount]) }}</p>
                        @endif
                    </div>
                    <i class="fas fa-star text-3xl text-yellow-400" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900" role="status">{{ session('success') }}</div>
        @endif

        @if ($pendingSubscriptionWhatsAppUrl)
            <div class="rounded-2xl border border-red-100 bg-red-50/80 px-5 py-4 text-gray-900 shadow-sm">
                <p class="font-semibold text-red-900">{{ __('front::provider_dashboard.whatsapp_cta') }}</p>
                <a href="{{ $pendingSubscriptionWhatsAppUrl }}" rel="noopener noreferrer" target="_blank" class="mt-3 inline-flex items-center gap-2 rounded-full bg-red-600 px-5 py-2.5 text-sm font-bold text-white shadow transition hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
                    <i class="fab fa-whatsapp text-lg" aria-hidden="true"></i> {{ __('front::provider_dashboard.whatsapp_cta') }}
                </a>
            </div>
        @elseif ($hasPendingSubscriptionPaymentRequest && ! $whatsappDigitsConfigured)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-amber-950" role="note">
                {{ __('front::provider_dashboard.whatsapp_missing_config') }}
            </div>
        @endif

        <section class="scroll-mt-8" aria-labelledby="current-plan-heading">
            <h2 id="current-plan-heading" class="text-xl font-bold tracking-tight text-gray-900">{{ __('front::provider_dashboard.current_plan_title') }}</h2>
            <div class="mt-4 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm ring-1 ring-black/5">
                <div class="border-l-4 border-red-600 bg-gradient-to-br from-white via-white to-gray-50/60 px-5 py-6 sm:px-7 sm:py-7">
                    @if (! $currentSub || ! $snapshot)
                        <x-front::provider-dashboard-empty-state :message="__('front::provider_dashboard.current_plan_empty')" />
                    @else
                        <dl class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            <div class="rounded-xl border border-gray-100 bg-white/90 p-4 shadow-sm">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('front::provider_dashboard.plan_name') }}</dt>
                                <dd class="mt-2 text-base font-semibold text-gray-900">{{ $snapshot->smartTransName() ?? __('front::provider_dashboard.value_emdash') }}</dd>
                            </div>
                            <div class="rounded-xl border border-gray-100 bg-white/90 p-4 shadow-sm">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('front::provider_dashboard.plan_price') }}</dt>
                                <dd class="mt-2 text-base font-semibold text-gray-900">{{ $snapshot->priceDisplay() }}</dd>
                            </div>
                            <div class="rounded-xl border border-gray-100 bg-white/90 p-4 shadow-sm">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('front::provider_dashboard.plan_billing') }}</dt>
                                <dd class="mt-2 text-base font-semibold text-gray-900">
                                    @php($bp = BillingPeriod::tryFrom((string) ($snapshot->billing_period ?? '')))
                                    {{ $bp ? __('front::provider_dashboard.billing.'.$bp->value) : __('front::provider_dashboard.value_emdash') }}
                                </dd>
                            </div>
                            <div class="rounded-xl border border-gray-100 bg-white/90 p-4 shadow-sm">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('front::provider_dashboard.plan_connections_remaining') }}</dt>
                                <dd class="mt-2 text-base font-semibold tabular-nums text-gray-900">{{ $currentSub->remaining_connections ?? __('front::provider_dashboard.value_emdash') }}</dd>
                            </div>
                            <div class="rounded-xl border border-gray-100 bg-white/90 p-4 shadow-sm">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('front::provider_dashboard.plan_period_end') }}</dt>
                                <dd class="mt-2 text-base font-semibold tabular-nums text-gray-900">{{ $currentSub->ends_at?->format('Y-m-d H:i') ?? __('front::provider_dashboard.value_emdash') }}</dd>
                            </div>
                            <div class="rounded-xl border border-gray-100 bg-white/90 p-4 shadow-sm">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('front::provider_dashboard.plan_status') }}</dt>
                                <dd class="mt-2 flex flex-wrap items-center gap-2">
                                    <span class="{{ $currentSub->status->providerDashboardTailwindBadgeClass() }}">{{ __('front::provider_dashboard.statuses.'.$currentSub->status->value) }}</span>
                                </dd>
                            </div>
                            <div class="rounded-xl border border-gray-100 bg-white/90 p-4 shadow-sm">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('front::provider_dashboard.plan_payment') }}</dt>
                                <dd class="mt-2 flex flex-wrap items-center gap-2">
                                    <span class="{{ $currentSub->payment_status->providerDashboardTailwindBadgeClass() }}">{{ __('front::provider_dashboard.payment_statuses.'.$currentSub->payment_status->value) }}</span>
                                </dd>
                            </div>
                            <div class="rounded-xl border border-gray-100 bg-white/90 p-4 shadow-sm">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('front::provider_dashboard.plan_payment_method') }}</dt>
                                <dd class="mt-2 flex flex-wrap items-center gap-2">
                                    <span class="{{ $currentSub->payment_method->providerDashboardTailwindBadgeClass() }}">{{ __('front::provider_dashboard.payment_methods.'.$currentSub->payment_method->value) }}</span>
                                </dd>
                            </div>
                        </dl>
                    @endif
                </div>
            </div>
        </section>

        <section aria-labelledby="history-heading">
            <h2 id="history-heading" class="text-xl font-bold text-gray-900">{{ __('front::provider_dashboard.subscription_history_title') }}</h2>
            <div
                class="mt-4 overflow-x-auto rounded-2xl border border-gray-200 bg-white shadow-sm"
                data-provider-dashboard-root="subscriptions"
                data-provider-dashboard-endpoint="{{ route('front.provider.dashboard.fragments.subscription-history') }}"
            >
                @include('front::provider.auth.partials.subscription-history-paginated', ['subscriptionHistory' => $subscriptionHistory])
            </div>
        </section>

        <section aria-labelledby="calls-heading">
            <h2 id="calls-heading" class="text-xl font-bold text-gray-900">{{ __('front::provider_dashboard.calls_title') }}</h2>
            <p class="mt-1 text-gray-600">{{ __('front::provider_dashboard.calls_intro') }}</p>
            <div
                class="mt-4 overflow-x-auto rounded-2xl border border-gray-200 bg-white shadow-sm"
                data-provider-dashboard-root="calls"
                data-provider-dashboard-endpoint="{{ route('front.provider.dashboard.fragments.call-log') }}"
            >
                @include('front::provider.auth.partials.call-log-paginated', ['callLog' => $callLog])
            </div>
        </section>

        <section aria-labelledby="packages-heading">
            <h2 id="packages-heading" class="text-xl font-bold text-gray-900">{{ __('front::provider_dashboard.packages_title') }}</h2>
            <p class="mt-1 text-gray-600">{{ __('front::provider_dashboard.packages_intro') }}</p>

            @if ($user->service_id === null)
                <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 shadow-sm">
                    <x-front::provider-dashboard-empty-state
                        embedded
                        tone="amber"
                        :message="__('front::provider_dashboard.packages_no_service')"
                    />
                </div>
            @elseif ($paidPackages->isEmpty())
                <div class="mt-4 rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <x-front::provider-dashboard-empty-state
                        embedded
                        :message="__('front::provider_dashboard.packages_empty')"
                    />
                </div>
            @else
                <div class="mt-6 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($paidPackages as $package)
                        @php($bp = $package->billing_period)
                        <x-front::provider-package-card
                            :package="$package"
                            :billing-label="$bp ? __('front::provider_dashboard.billing.'.$bp->value) : ''"
                            :connections-label="__('front::provider_dashboard.package_connections', ['count' => $package->connections_count])"
                        >
                            <form method="post" action="{{ route('front.provider.subscriptions.request') }}" class="w-full">
                                @csrf
                                <input type="hidden" name="package_id" value="{{ $package->id }}">
                                <button type="submit" class="w-full rounded-full bg-red-600 px-4 py-3 text-sm font-bold text-white shadow transition hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
                                    {{ __('front::provider_dashboard.package_subscribe') }}
                                </button>
                            </form>
                        </x-front::provider-package-card>
                    @endforeach
                </div>
            @endif
        </section>

        <x-front::alert-modal
            :show="$errors->has('package_id')"
            :title="__('front::provider_dashboard.subscription_error_modal_title')"
            :message="$errors->first('package_id')"
            :close-label="__('front::provider_dashboard.subscription_error_modal_close')"
            modal-id="provider-package-request-error-modal"
        />

        <x-front::alert-modal
            :show="session()->has('success')"
            variant="success"
            :title="__('front::provider_dashboard.subscription_success_modal_title')"
            :message="session('success')"
            :close-label="__('front::provider_dashboard.subscription_success_modal_close')"
            modal-id="provider-subscription-request-success-modal"
        />

        <section class="scroll-mt-8" aria-labelledby="bank-heading">
            <h2 id="bank-heading" class="text-xl font-bold tracking-tight text-gray-900">{{ __('front::provider_dashboard.bank_block_title') }}</h2>
            <div class="mt-4 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm ring-1 ring-black/5">
                <div class="border-l-4 border-amber-500 bg-gradient-to-br from-amber-50/40 via-white to-white px-5 py-6 sm:px-7 sm:py-7">
                    @if ($bankInstructions === '')
                        <x-front::provider-dashboard-empty-state tone="amber" :message="__('front::provider_dashboard.bank_block_empty')" />
                    @else
                        <div class="rounded-xl border border-amber-100/90 bg-white/90 p-5 shadow-inner sm:p-6">
                            <div class="leading-relaxed text-gray-800 whitespace-pre-wrap">{{ $bankInstructions }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection

@push('script')
    <script>
        (function () {
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrf = csrfMeta ? csrfMeta.getAttribute('content') : '';
            if (!csrf) {
                return;
            }

            document.addEventListener('click', function (e) {
                const btn = e.target.closest('[data-provider-dashboard-page]');
                if (!btn || btn.disabled) {
                    return;
                }
                const section = btn.getAttribute('data-provider-dashboard-section');
                const page = btn.getAttribute('data-provider-dashboard-page');
                if (!section || page === null || page === '') {
                    return;
                }
                const root = document.querySelector('[data-provider-dashboard-root="' + section + '"]');
                const endpoint = root ? root.getAttribute('data-provider-dashboard-endpoint') : '';
                if (!root || !endpoint) {
                    return;
                }
                e.preventDefault();
                root.setAttribute('aria-busy', 'true');
                root.classList.add('pointer-events-none', 'opacity-70');

                fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ page: parseInt(page, 10) }),
                })
                    .then(function (res) {
                        if (!res.ok) {
                            throw new Error('Bad response');
                        }
                        return res.json();
                    })
                    .then(function (data) {
                        if (typeof data.html !== 'string') {
                            throw new Error('Invalid payload');
                        }
                        root.innerHTML = data.html;
                    })
                    .catch(function () {
                        window.location.reload();
                    })
                    .finally(function () {
                        root.removeAttribute('aria-busy');
                        root.classList.remove('pointer-events-none', 'opacity-70');
                    });
            });
        })();
    </script>
@endpush
