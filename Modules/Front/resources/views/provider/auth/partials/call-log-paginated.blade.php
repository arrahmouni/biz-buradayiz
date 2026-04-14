@php
    use Modules\Front\Support\ProviderDashboardTailwindBadge;
    use Modules\Verimor\Enums\VerimorCallDirection;
@endphp

@if ($callLog->isEmpty())
    <p class="p-6 text-gray-600">{{ __('front::provider_dashboard.calls_empty') }}</p>
@else
    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
        <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-600">
            <tr>
                <th class="px-4 py-3">{{ __('front::provider_dashboard.calls_col_time') }}</th>
                <th class="px-4 py-3">{{ __('front::provider_dashboard.calls_col_direction') }}</th>
                <th class="px-4 py-3">{{ __('front::provider_dashboard.calls_col_caller') }}</th>
                <th class="px-4 py-3">{{ __('front::provider_dashboard.calls_col_destination') }}</th>
                <th class="px-4 py-3">{{ __('front::provider_dashboard.calls_col_answered') }}</th>
                <th class="px-4 py-3">{{ __('front::provider_dashboard.calls_col_quota') }}</th>
                <th class="px-4 py-3">{{ __('front::provider_dashboard.calls_col_call_id') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-gray-800">
            @foreach ($callLog as $event)
                <tr class="hover:bg-gray-50/80">
                    <td class="whitespace-nowrap px-4 py-3">{{ $event->created_at?->format('Y-m-d H:i') }}</td>
                    <td class="px-4 py-3">
                        <span class="{{ VerimorCallDirection::providerDashboardTailwindBadgeClassForNullable($event->direction) }}">
                            @if ($event->direction)
                                {{ __('front::provider_dashboard.calls_directions.'.$event->direction->value) }}
                            @else
                                {{ __('front::provider_dashboard.value_emdash') }}
                            @endif
                        </span>
                    </td>
                    <td class="px-4 py-3 font-mono text-xs">{{ $event->caller_number_normalized ?? __('front::provider_dashboard.value_emdash') }}</td>
                    <td class="px-4 py-3 font-mono text-xs">{{ $event->destination_number_normalized ?? __('front::provider_dashboard.value_emdash') }}</td>
                    <td class="px-4 py-3">
                        <span class="{{ ProviderDashboardTailwindBadge::forBool((bool) $event->answered) }}">{{ $event->answered ? __('front::provider_dashboard.calls_yes') : __('front::provider_dashboard.calls_no') }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="{{ ProviderDashboardTailwindBadge::forBool((bool) $event->consumed_quota) }}">{{ $event->consumed_quota ? __('front::provider_dashboard.calls_yes') : __('front::provider_dashboard.calls_no') }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <details class="group">
                            <summary class="cursor-pointer rounded font-mono text-xs text-red-700 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500">{{ \Illuminate\Support\Str::limit($event->call_uuid, 12, '…') }}</summary>
                            <span class="mt-1 block break-all font-mono text-xs text-gray-600">{{ $event->call_uuid }}</span>
                        </details>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @include('front::provider.auth.partials.dashboard-table-pagination', [
        'paginator' => $callLog,
        'section' => 'calls',
    ])
@endif
