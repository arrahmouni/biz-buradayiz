@php
    use Modules\Auth\Enums\permissions\UserPermissions;

    $canUpdate = app('owner') || app('admin')->can(UserPermissions::UPDATE);
    $ratingAvg = $model->review_rating_average;
    $ratingRounded = $ratingAvg !== null ? max(0, min(5, (int) round((float) $ratingAvg))) : null;
    $langLabel = $model->lang && isset($_ALL_LOCALE_[$model->lang])
        ? $_ALL_LOCALE_[$model->lang]['native']
        : ($model->lang ?? '—');
    $socialLabel = $model->provider
        ? \Illuminate\Support\Str::headline(str_replace('_', ' ', (string) $model->provider))
        : null;
@endphp

@extends('admin::layouts.master', ['title' => trans('admin::cruds.users.view')])

@section('toolbar')
    @component('admin::includes.toolbar', [
            'options'               => [
                'title'             => trans('admin::cruds.users.view'),
                'backUrl'           => route('auth.users.index', ['userType' => $userType->value]),
                'actions'           => [
                    'save'          => false,
                    'back'          => true,
                ],
            ]
        ])
        @if ($canUpdate)
            @slot('otherActions')
                @component('admin::components.other.hyperlink', [
                        'options'           => [
                            'title'         => trans('admin::cruds.users.edit'),
                            'href'          => route('auth.users.update', ['userType' => $userType->value, 'model' => $model->id]),
                            'class'         => 'btn btn-light-primary',
                        ]
                    ])
                @endcomponent
            @endslot
        @endif
    @endcomponent
@endsection

@section('content')
    <div id="kt_content_container" class="container-fluid">
        <div class="d-flex flex-column gap-7 gap-lg-10">
            <div class="card card-flush">
                <div class="card-body d-flex flex-column flex-lg-row align-items-lg-center gap-5 py-8">
                    <div class="symbol symbol-100px symbol-lg-125px flex-shrink-0">
                        <img src="{{ $model->image_url }}" alt="{{ $model->full_name }}" class="rounded" onerror="this.onerror=null; this.src='{{ asset('images/default/avatars/user.png') }}';" />
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex flex-wrap align-items-center gap-3 mb-2">
                            <h1 class="fs-2 fw-bold text-gray-900 mb-0">{{ $model->full_name }}</h1>
                            <span class="btn btn-sm btn-font-sm btn-label-{{ $model->status_format['color'] }}">
                                {{ $model->status_format['label'] }}
                            </span>
                        </div>
                        <div class="text-gray-600 fw-semibold fs-5 mb-1">{{ $model->email }}</div>
                        <div class="text-muted fs-7">
                            @lang('admin::datatable.admins.columns.joined_date'):
                            <span class="text-gray-700 fw-semibold">{{ $model->created_at_format }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if ($isServiceProvider)
                <div class="row g-5 g-xl-10">
                    <div class="col-xl-6">
                        <div class="card card-flush h-xl-100">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>@lang('admin::cruds.users.view_page.ratings')</h2>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="d-flex flex-wrap align-items-center gap-4 mb-6">
                                    @if ($ratingRounded !== null)
                                        <div class="d-flex align-items-center gap-1" role="img" aria-label="@lang('admin::cruds.users.view_page.rating_average')">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="bi {{ $i <= $ratingRounded ? 'bi-star-fill text-warning' : 'bi-star text-gray-400' }} fs-2x"></i>
                                            @endfor
                                        </div>
                                        <div class="text-gray-800 fw-bold fs-3">
                                            {{ number_format((float) $ratingAvg, 2) }}
                                        </div>
                                    @else
                                        <span class="text-muted fw-semibold">—</span>
                                    @endif
                                </div>
                                <div class="table-responsive">
                                    <table class="table align-middle table-row-bordered mb-0 fs-6 gy-5 min-w-300px">
                                        <tbody class="fw-bold text-gray-600">
                                            <tr>
                                                <td class="text-muted">@lang('admin::cruds.users.view_page.approved_reviews_count')</td>
                                                <td class="fw-bolder text-end">{{ $model->approved_reviews_count ?? 0 }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">@lang('admin::cruds.users.view_page.ranking_score')</td>
                                                <td class="fw-bolder text-end font-monospace">
                                                    {{ $model->ranking_score === null ? '—' : number_format((float) $model->ranking_score, 4) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card card-flush h-xl-100">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>@lang('admin::cruds.users.view_page.service_location')</h2>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                @php
                                    $providerServiceImageMedia = $model->getFirstMedia(\Modules\Auth\Models\User::SERVICE_IMAGE_MEDIA_COLLECTION);
                                @endphp
                                <div class="table-responsive">
                                    <table class="table align-middle table-row-bordered mb-0 fs-6 gy-5 min-w-300px">
                                        <tbody class="fw-bold text-gray-600">
                                            <tr>
                                                <td class="text-muted">@lang('admin::inputs.user_crud.company_name.label')</td>
                                                <td class="fw-bolder text-end">{{ $model->company_name ?: '—' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">@lang('admin::inputs.user_crud.service_id.label')</td>
                                                <td class="fw-bolder text-end">{{ $model->service?->name ?? '—' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">@lang('admin::inputs.user_crud.service_image.label')</td>
                                                <td class="fw-bolder text-end">
                                                    @if ($providerServiceImageMedia)
                                                        @php
                                                            $providerServiceImageUrl = $providerServiceImageMedia->getUrl();
                                                        @endphp
                                                        <a
                                                            href="{{ $providerServiceImageUrl }}"
                                                            class="d-inline-flex justify-content-end cursor-pointer"
                                                            data-fslightbox="admin-provider-service-{{ $model->id }}"
                                                            aria-label="{{ trans('admin::inputs.user_crud.service_image.label') }}"
                                                        >
                                                            <div class="symbol symbol-75px flex-shrink-0">
                                                                <img
                                                                    src="{{ $providerServiceImageUrl }}"
                                                                    alt="{{ $model->service?->name ?? $model->full_name }}"
                                                                    class="rounded"
                                                                    onerror="this.onerror=null; this.src='{{ app_placeholder_url() }}';"
                                                                />
                                                            </div>
                                                        </a>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">@lang('admin::inputs.user_crud.country_id.label')</td>
                                                <td class="fw-bolder text-end">{{ $model->city?->state?->country?->name ?? '—' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">@lang('admin::inputs.user_crud.state_id.label')</td>
                                                <td class="fw-bolder text-end">{{ $model->city?->state?->name ?? '—' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">@lang('admin::inputs.user_crud.city_id.label')</td>
                                                <td class="fw-bolder text-end">{{ $model->city?->name ?? '—' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    $subscription = $model->currentPackageSubscription;
                    $showSubscriptionHistoryTab = $canViewProviderSubscriptionHistory ?? false;
                    $showCallLogTab = $canViewProviderCallLog ?? false;
                @endphp
                <div class="card card-flush">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>@lang('admin::cruds.users.view_page.subscription')</h2>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        @if ($subscription)
                            <div class="table-responsive">
                                <table class="table align-middle table-row-bordered mb-0 fs-6 gy-5 min-w-300px">
                                    <tbody class="fw-bold text-gray-600">
                                        <tr>
                                            <td class="text-muted">@lang('admin::cruds.package_subscriptions.view_page.package')</td>
                                            <td class="fw-bolder text-end">{{ $subscription->snapshot?->smartTransName() ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">@lang('admin::cruds.package_subscriptions.view_page.subscription_status')</td>
                                            <td class="fw-bolder text-end">
                                                <span class="btn btn-sm btn-font-sm btn-label-{{ $subscription->status->datatableBadgeColor() }}">
                                                    {{ $subscription->status_label }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">@lang('admin::cruds.package_subscriptions.view_page.ends_at')</td>
                                            <td class="fw-bolder text-end">{{ $subscription->ends_at?->format('Y-m-d H:i') ?? '—' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0 fw-semibold">—</p>
                        @endif
                    </div>
                </div>

                @if ($showSubscriptionHistoryTab || $showCallLogTab)
                    @php
                        $verimorCallEventsDtLabels = [
                            'yes' => trans('verimor::strings.yes'),
                            'no' => trans('verimor::strings.no'),
                        ];
                        $verimorCallDirectionDtBadgeColors = \Modules\Verimor\Enums\VerimorCallDirection::datatableBadgeColorsByValue();
                    @endphp
                    <div class="card card-flush">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>@lang('admin::cruds.users.view_page.history_tabs_title')</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold mb-8" role="tablist">
                                @if ($showSubscriptionHistoryTab)
                                    <li class="nav-item" role="presentation">
                                        <a
                                            class="nav-link text-active-primary pb-4 @if ($showSubscriptionHistoryTab) active @endif"
                                            id="provider-view-tab-subscriptions-link"
                                            data-bs-toggle="tab"
                                            href="#provider-view-tab-subscriptions"
                                            role="tab"
                                            aria-controls="provider-view-tab-subscriptions"
                                            aria-selected="{{ $showSubscriptionHistoryTab ? 'true' : 'false' }}"
                                        >
                                            @lang('admin::cruds.users.view_page.tab_subscription_history')
                                        </a>
                                    </li>
                                @endif
                                @if ($showCallLogTab)
                                    <li class="nav-item" role="presentation">
                                        <a
                                            class="nav-link text-active-primary pb-4 @if (! $showSubscriptionHistoryTab) active @endif"
                                            id="provider-view-tab-calls-link"
                                            data-bs-toggle="tab"
                                            href="#provider-view-tab-calls"
                                            role="tab"
                                            aria-controls="provider-view-tab-calls"
                                            aria-selected="{{ ! $showSubscriptionHistoryTab ? 'true' : 'false' }}"
                                        >
                                            @lang('admin::cruds.users.view_page.tab_call_log')
                                        </a>
                                    </li>
                                @endif
                            </ul>

                            <div class="tab-content">
                                @if ($showSubscriptionHistoryTab)
                                    <div
                                        class="tab-pane fade @if ($showSubscriptionHistoryTab) show active @endif"
                                        id="provider-view-tab-subscriptions"
                                        role="tabpanel"
                                        aria-labelledby="provider-view-tab-subscriptions-link"
                                        tabindex="0"
                                    >
                                        @component('admin::components.datatables.table', [
                                                'options' => [
                                                    'id' => 'provider-view-subscriptions-table',
                                                    'url' => route('auth.users.showSubscriptionsDatatable', [
                                                        'userType' => $userType->value,
                                                        'model' => $model->id,
                                                    ]),
                                                    'withExportButton' => false,
                                                    'withTrash' => false,
                                                    'filter' => false,
                                                    'search' => true,
                                                    'withCreatedAt' => true,
                                                    'createdAtColumn' => trans('admin::datatable.package_subscriptions.columns.created_at'),
                                                ],
                                            ])
                                            @slot('columns')
                                                <th class="min-w-200px">@lang('admin::datatable.package_subscriptions.columns.package')</th>
                                                <th class="min-w-125px">@lang('admin::datatable.package_subscriptions.columns.ordered_price')</th>
                                                <th class="min-w-175px">@lang('admin::datatable.package_subscriptions.columns.payment_method')</th>
                                                <th class="min-w-125px">@lang('admin::datatable.package_subscriptions.columns.status')</th>
                                                <th class="min-w-140px">@lang('admin::datatable.package_subscriptions.columns.payment_status')</th>
                                            @endslot

                                            <script>
                                                @slot('jsColumns')
                                                {
                                                    data: 'package_name',
                                                    name: 'package_name',
                                                    orderable: false,
                                                    render: function (data, type, row, meta) {
                                                        if (data === '—' || ! data) {
                                                            return '<span class="text-muted">—</span>';
                                                        }
                                                        return '<span class="text-dark fw-bolder">' + data + '</span>';
                                                    }
                                                },
                                                {
                                                    data: 'price_display',
                                                    name: 'price_display',
                                                    orderable: false,
                                                    searchable: false,
                                                    render: function (data, type, row, meta) {
                                                        if (data === '—' || ! data) {
                                                            return '<span class="text-muted">—</span>';
                                                        }
                                                        return '<span class="badge badge-light fs-7 fw-semibold">' + data + '</span>';
                                                    }
                                                },
                                                {
                                                    data: 'payment_method_format',
                                                    name: 'payment_method_format',
                                                    orderable: false,
                                                    searchable: false,
                                                    render: function (data, type, row, meta) {
                                                        if (type !== 'display') {
                                                            return (data && data.label) ? data.label : '';
                                                        }
                                                        if (! data || ! data.label) {
                                                            return '<span class="text-muted">—</span>';
                                                        }
                                                        const label = data.label;
                                                        const img = data.img || '';
                                                        return `
                                                            <div class="d-flex align-items-center">
                                                                <div class="symbol symbol-circle symbol-40px me-3 flex-shrink-0">
                                                                    <span class="symbol-label bg-light-primary d-flex align-items-center justify-content-center p-2">
                                                                        <img src="${img}" class="w-20px h-20px" alt="" />
                                                                    </span>
                                                                </div>
                                                                <span class="text-gray-800 fw-semibold fs-7 lh-sm">${label}</span>
                                                            </div>
                                                        `;
                                                    }
                                                },
                                                {
                                                    data: 'status_badge',
                                                    name: 'status_badge',
                                                    orderable: false,
                                                    searchable: false,
                                                    render: function (data, type, row, meta) {
                                                        if (! data || ! data.label) {
                                                            return '<span class="text-muted">—</span>';
                                                        }
                                                        return '<span class="btn btn-sm btn-font-sm btn-label-' + data.color + ' text-center w-100">' + data.label + '</span>';
                                                    }
                                                },
                                                {
                                                    data: 'payment_status_badge',
                                                    name: 'payment_status_badge',
                                                    orderable: false,
                                                    searchable: false,
                                                    render: function (data, type, row, meta) {
                                                        if (! data || ! data.label) {
                                                            return '<span class="text-muted">—</span>';
                                                        }
                                                        return '<span class="btn btn-sm btn-font-sm btn-label-' + data.color + ' text-center w-100">' + data.label + '</span>';
                                                    }
                                                },
                                                @endslot
                                            </script>
                                        @endcomponent
                                    </div>
                                @endif

                                @if ($showCallLogTab)
                                    <div
                                        class="tab-pane fade @if (! $showSubscriptionHistoryTab) show active @endif"
                                        id="provider-view-tab-calls"
                                        role="tabpanel"
                                        aria-labelledby="provider-view-tab-calls-link"
                                        tabindex="0"
                                    >
                                        @component('admin::components.datatables.table', [
                                                'options' => [
                                                    'id' => 'provider-view-call-events-table',
                                                    'url' => route('auth.users.showCallEventsDatatable', [
                                                        'userType' => $userType->value,
                                                        'model' => $model->id,
                                                    ]),
                                                    'withExportButton' => false,
                                                    'withTrash' => false,
                                                    'filter' => false,
                                                    'search' => true,
                                                    'withCreatedAt' => true,
                                                    'createdAtColumn' => trans('admin::datatable.verimor_call_events.columns.created_at'),
                                                ],
                                            ])
                                            @slot('columns')
                                                <th class="min-w-200px">@lang('admin::datatable.verimor_call_events.columns.call_uuid')</th>
                                                <th class="min-w-100px">@lang('admin::datatable.verimor_call_events.columns.direction')</th>
                                                <th class="min-w-125px">@lang('admin::datatable.verimor_call_events.columns.destination')</th>
                                                <th class="min-w-100px">@lang('admin::datatable.verimor_call_events.columns.answered')</th>
                                                <th class="min-w-125px">@lang('admin::datatable.verimor_call_events.columns.consumed_quota')</th>
                                            @endslot

                                            <script>
                                                @slot('jsColumns')
                                                {
                                                    data: 'call_uuid',
                                                    name: 'call_uuid',
                                                    render: function (data, type, row, meta) {
                                                        if (! data) {
                                                            return '<span class="text-muted">—</span>';
                                                        }
                                                        return '<span class="text-gray-800 fw-semibold font-monospace fs-8 lh-sm">' + data + '</span>';
                                                    }
                                                },
                                                {
                                                    data: 'direction',
                                                    name: 'direction',
                                                    render: function (data, type, row, meta) {
                                                        const directionLabels = @json(\Modules\Verimor\Enums\VerimorCallDirection::filterOptions());
                                                        const directionBadgeColors = @json($verimorCallDirectionDtBadgeColors);
                                                        if (! data) {
                                                            if (type !== 'display' && type !== 'filter') {
                                                                return '';
                                                            }
                                                            return '<span class="text-muted">—</span>';
                                                        }
                                                        const d = String(data).toLowerCase();
                                                        const label = directionLabels[d] || data;
                                                        if (type !== 'display' && type !== 'filter') {
                                                            return label;
                                                        }
                                                        const color = directionBadgeColors[d] || 'secondary';
                                                        return '<span class="badge badge-light-' + color + ' fs-7 fw-semibold">' + label + '</span>';
                                                    }
                                                },
                                                {
                                                    data: 'destination_number_normalized',
                                                    name: 'destination_number_normalized',
                                                    render: function (data, type, row, meta) {
                                                        if (! data) {
                                                            return '<span class="text-muted">—</span>';
                                                        }
                                                        return '<span class="text-dark fw-bold font-monospace fs-7">' + data + '</span>';
                                                    }
                                                },
                                                {
                                                    data: 'answered',
                                                    name: 'answered',
                                                    orderable: false,
                                                    searchable: false,
                                                    render: function (data, type, row, meta) {
                                                        const L = @json($verimorCallEventsDtLabels);
                                                        const on = !! data;
                                                        if (type !== 'display' && type !== 'filter') {
                                                            return on ? L.yes : L.no;
                                                        }
                                                        const label = on ? L.yes : L.no;
                                                        const color = on ? 'success' : 'secondary';
                                                        return '<span class="btn btn-sm btn-font-sm btn-label-' + color + ' text-center w-100">' + label + '</span>';
                                                    }
                                                },
                                                {
                                                    data: 'consumed_quota',
                                                    name: 'consumed_quota',
                                                    orderable: false,
                                                    searchable: false,
                                                    render: function (data, type, row, meta) {
                                                        const L = @json($verimorCallEventsDtLabels);
                                                        const on = !! data;
                                                        if (type !== 'display' && type !== 'filter') {
                                                            return on ? L.yes : L.no;
                                                        }
                                                        const label = on ? L.yes : L.no;
                                                        const color = on ? 'success' : 'secondary';
                                                        return '<span class="btn btn-sm btn-font-sm btn-label-' + color + ' text-center w-100">' + label + '</span>';
                                                    }
                                                },
                                                @endslot
                                            </script>
                                        @endcomponent
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <div class="row g-5 g-xl-10">
                <div class="col-xl-6">
                    <div class="card card-flush h-xl-100">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>@lang('admin::cruds.users.view_page.contact')</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="table-responsive">
                                <table class="table align-middle table-row-bordered mb-0 fs-6 gy-5 min-w-300px">
                                    <tbody class="fw-bold text-gray-600">
                                        <tr>
                                            <td class="text-muted">
                                                <div class="d-flex align-items-center">
                                                    <span class="svg-icon svg-icon-2 me-2">{!! config('admin.svgs.phone') !!}</span>
                                                    @lang('admin::datatable.base_columns.phone_number')
                                                </div>
                                            </td>
                                            <td class="fw-bolder text-end">
                                                @if (! empty($model->phone_number))
                                                    <a href="tel:{{ $model->phone_number }}" class="text-gray-800 text-hover-primary">{{ $model->phone_number }}</a>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">
                                                <div class="d-flex align-items-center">
                                                    <span class="svg-icon svg-icon-2 me-2">{!! config('admin.svgs.phone') !!}</span>
                                                    @lang('admin::datatable.base_columns.central_phone')
                                                </div>
                                            </td>
                                            <td class="fw-bolder text-end">
                                                @if (! empty($model->central_phone))
                                                    <a href="tel:{{ $model->central_phone }}" class="text-gray-800 text-hover-primary">{{ $model->central_phone }}</a>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="card card-flush h-xl-100">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>@lang('admin::cruds.users.view_page.account')</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="table-responsive">
                                <table class="table align-middle table-row-bordered mb-0 fs-6 gy-5 min-w-300px">
                                    <tbody class="fw-bold text-gray-600">
                                        <tr>
                                            <td class="text-muted">@lang('admin::inputs.base_crud.lang.label')</td>
                                            <td class="fw-bolder text-end">{{ $langLabel }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">@lang('admin::cruds.users.view_page.email_verified')</td>
                                            <td class="fw-bolder text-end">
                                                @if ($model->email_verified_at)
                                                    {{ $model->email_verified_at->format('Y-m-d H:i') }}
                                                @else
                                                    @lang('admin::cruds.users.view_page.email_not_verified')
                                                @endif
                                            </td>
                                        </tr>
                                        @if ($model->provider)
                                            <tr>
                                                <td class="text-muted">@lang('admin::cruds.users.view_page.social_login')</td>
                                                <td class="fw-bolder text-end">
                                                    {{ $socialLabel }}
                                                    @if ($model->provider_id)
                                                        <span class="text-muted fw-normal">({{ $model->provider_id }})</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($isServiceProvider && $model->addresses->isNotEmpty())
                <div class="card card-flush">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>@lang('admin::cruds.users.view_page.addresses')</h2>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-row-dashed align-middle gs-0 gy-4">
                                <thead>
                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase">
                                        <th class="min-w-125px">@lang('admin::cruds.users.view_page.address_title')</th>
                                        <th class="min-w-125px">@lang('admin::cruds.users.view_page.address_default')</th>
                                        <th class="min-w-175px">@lang('admin::inputs.user_crud.city_id.label')</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-700">
                                    @foreach ($model->addresses as $address)
                                        <tr>
                                            <td>{{ $address->title ?? '—' }}</td>
                                            <td>
                                                @if ($address->is_default)
                                                    @lang('admin::cruds.users.view_page.default_yes')
                                                @else
                                                    @lang('admin::cruds.users.view_page.default_no')
                                                @endif
                                            </td>
                                            <td>{{ $address->city?->name ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@if ($isServiceProvider && $model->getFirstMedia(\Modules\Auth\Models\User::SERVICE_IMAGE_MEDIA_COLLECTION))
    @push('script')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof refreshFsLightbox === 'function') {
                    refreshFsLightbox();
                }
            });
        </script>
    @endpush
@endif

@if ($isServiceProvider && (($canViewProviderSubscriptionHistory ?? false) || ($canViewProviderCallLog ?? false)))
    @push('script')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const subsTab = document.getElementById('provider-view-tab-subscriptions-link');
                if (subsTab) {
                    subsTab.addEventListener('shown.bs.tab', function () {
                        if (window.jQuery && $.fn.DataTable && $.fn.DataTable.isDataTable('#provider-view-subscriptions-table')) {
                            window.jQuery('#provider-view-subscriptions-table').DataTable().columns.adjust();
                        }
                    });
                }
                const callsTab = document.getElementById('provider-view-tab-calls-link');
                if (callsTab) {
                    callsTab.addEventListener('shown.bs.tab', function () {
                        if (window.jQuery && $.fn.DataTable && $.fn.DataTable.isDataTable('#provider-view-call-events-table')) {
                            window.jQuery('#provider-view-call-events-table').DataTable().columns.adjust();
                        }
                    });
                }
            });
        </script>
    @endpush
@endif

@section('modal')
    @if ($isServiceProvider && ($canViewProviderCallLog ?? false))
        @include('admin::components.modals.view_modal', [
            'options' => [
                'id' => 'verimorCallEventViewModal',
                'title' => trans('verimor::strings.view_modal_title.call_event'),
            ],
        ])
    @endif
@endsection
