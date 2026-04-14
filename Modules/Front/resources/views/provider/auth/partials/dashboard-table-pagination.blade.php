@if ($paginator->hasPages())
    <nav class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 px-4 py-3" aria-label="{{ __('front::provider_dashboard.table_pagination_label') }}">
        <button
            type="button"
            class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
            data-provider-dashboard-section="{{ $section }}"
            data-provider-dashboard-page="{{ $paginator->currentPage() - 1 }}"
            @if ($paginator->onFirstPage()) disabled @endif
        >{{ __('front::provider_dashboard.table_pagination_prev') }}</button>
        <span class="text-sm text-gray-600 tabular-nums">{{ __('front::provider_dashboard.table_pagination_meta', ['current' => $paginator->currentPage(), 'last' => $paginator->lastPage()]) }}</span>
        <button
            type="button"
            class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
            data-provider-dashboard-section="{{ $section }}"
            data-provider-dashboard-page="{{ $paginator->currentPage() + 1 }}"
            @if (! $paginator->hasMorePages()) disabled @endif
        >{{ __('front::provider_dashboard.table_pagination_next') }}</button>
    </nav>
@endif
