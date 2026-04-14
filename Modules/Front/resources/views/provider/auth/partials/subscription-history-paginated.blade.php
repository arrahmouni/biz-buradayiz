@if ($subscriptionHistory->isEmpty())
    <p class="p-6 text-gray-600">{{ __('front::provider_dashboard.subscription_history_empty') }}</p>
@else
    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
        <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-600">
            <tr>
                <th class="px-4 py-3">{{ __('front::provider_dashboard.subscription_history_col_ref') }}</th>
                <th class="px-4 py-3">{{ __('front::provider_dashboard.subscription_history_col_time') }}</th>
                <th class="px-4 py-3">{{ __('front::provider_dashboard.plan_name') }}</th>
                <th class="px-4 py-3">{{ __('front::provider_dashboard.plan_status') }}</th>
                <th class="px-4 py-3">{{ __('front::provider_dashboard.plan_payment') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-gray-800">
            @foreach ($subscriptionHistory as $sub)
                @php($histSnapshot = $sub->snapshot)
                <tr class="hover:bg-gray-50/80">
                    <td class="whitespace-nowrap px-4 py-3 font-semibold text-gray-900">{{ __('front::provider_dashboard.subscription_ref', ['id' => $sub->id]) }}</td>
                    <td class="whitespace-nowrap px-4 py-3">{{ $sub->created_at?->format('Y-m-d H:i') }}</td>
                    <td class="px-4 py-3">{{ $histSnapshot?->smartTransName() ?? __('front::provider_dashboard.value_emdash') }}</td>
                    <td class="px-4 py-3">
                        <span class="{{ $sub->status->providerDashboardTailwindBadgeClass() }}">{{ __('front::provider_dashboard.statuses.'.$sub->status->value) }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="{{ $sub->payment_status->providerDashboardTailwindBadgeClass() }}">{{ __('front::provider_dashboard.payment_statuses.'.$sub->payment_status->value) }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @include('front::provider.auth.partials.dashboard-table-pagination', [
        'paginator' => $subscriptionHistory,
        'section' => 'subscriptions',
    ])
@endif
