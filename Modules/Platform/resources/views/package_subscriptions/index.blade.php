@php
    use Modules\Auth\Enums\UserType;
    use Modules\Platform\Enums\PackageSubscriptionPaymentMethod;
    use Modules\Platform\Enums\PackageSubscriptionPaymentStatus;
    use Modules\Platform\Enums\PackageSubscriptionStatus;

    $serviceProviderViewUrlTemplate = route('auth.users.show', [
        'userType' => UserType::ServiceProvider->value,
        'model' => 900000001,
    ]);
@endphp

@extends('admin::layouts.master', [
    'title' => trans('admin::dashboard.aside_menu.platform_management.package_subscriptions'),
])

@section('toolbar')
    @component('admin::includes.toolbar', [
            'options'               => [
                'title'             => trans('admin::dashboard.aside_menu.platform_management.package_subscriptions'),
                'actions'           => [
                    'filter'        => true,
                    'search'        => true,
                ],
            ]
        ])

        @slot('filterContent')
            <div class="mb-5">
                @include('admin::components.inputs.select', [
                    'options'           => [
                        'name'          => 'status',
                        'label'         => trans('admin::datatable.package_subscriptions.columns.status'),
                        'placeholder'   => trans('admin::base.all_results'),
                        'clearable'     => true,
                        'data'          => PackageSubscriptionStatus::adminFilterSelectOptions(),
                        'text'          => function ($key, $value) { return $value; },
                        'values'        => function ($key, $value) { return $key; },
                    ]
                ])
            </div>

            <div class="mb-5">
                @include('admin::components.inputs.select', [
                    'options'           => [
                        'name'          => 'payment_status',
                        'label'         => trans('admin::datatable.package_subscriptions.columns.payment_status'),
                        'placeholder'   => trans('admin::base.all_results'),
                        'clearable'     => true,
                        'data'          => PackageSubscriptionPaymentStatus::adminFilterSelectOptions(),
                        'text'          => function ($key, $value) { return $value; },
                        'values'        => function ($key, $value) { return $key; },
                    ]
                ])
            </div>

            <div class="mb-5">
                @include('admin::components.inputs.select', [
                    'options'           => [
                        'name'          => 'payment_method',
                        'label'         => trans('admin::datatable.package_subscriptions.columns.payment_method'),
                        'placeholder'   => trans('admin::base.all_results'),
                        'clearable'     => true,
                        'data'          => PackageSubscriptionPaymentMethod::adminFilterSelectOptions(),
                        'text'          => function ($key, $value) { return $value; },
                        'values'        => function ($key, $value) { return $key; },
                    ]
                ])
            </div>
        @endslot
    @endcomponent
@endsection

@section('content')
    <div id="kt_content_container" class="container-fluid">
        <div class="card shadow-sm ">

            <div class="card-header">
                <div class="card-title">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        @include('admin::components.datatables.header.title', [
                            'options'   => [
                                'role'  => $viewTrashPermission ?? null,
                                'title' => trans('admin::datatable.package_subscriptions.list_title'),
                                'withSwitchArchive' => false,
                            ]
                        ])
                        @if(($package_subscriptions_awaiting_verification_count ?? 0) > 0)
                            <button
                                type="button"
                                class="btn btn-sm btn-light-danger fw-semibold"
                                id="package-subscriptions-filter-awaiting-verification"
                                title="{{ trans('admin::datatable.package_subscriptions.quick_filter_awaiting_verification_title') }}"
                            >
                            <i class="bi bi-patch-check fs-6 me-1"></i>
                            {{ trans('admin::datatable.package_subscriptions.quick_filter_awaiting_verification') }}
                                <span class="badge badge-danger ms-2">{{ $package_subscriptions_awaiting_verification_count }}</span>
                            </button>
                        @endif
                    </div>
                </div>

                <div class="card-toolbar flex-row-reverse">
                    @include('admin::components.datatables.header.toolbar', [
                        'options'               => [
                            'role'              => $createPermission,
                            'multiActions'      => $bulkActionDropdown,
                            'route'             => route('platform.package_subscriptions.create'),
                        ]
                    ])
                </div>
            </div>

            <div class="card-body  py-4">
                @component('admin::components.datatables.table', [
                        'options'           => [
                            'url'           => route('platform.package_subscriptions.datatable'),
                            'withCreatedAt' => true,
                            'withTrash'     => false,
                            'filter'        => true,
                        ]
                    ])
                    @slot('columns')
                        <th class="min-w-275px"> @lang('admin::inputs.package_subscriptions_crud.service_provider.label') </th>
                        <th class=""> @lang('admin::datatable.package_subscriptions.columns.package') </th>
                        <th class="min-w-125px"> @lang('admin::datatable.package_subscriptions.columns.ordered_price') </th>
                        <th class="min-w-175px"> @lang('admin::datatable.package_subscriptions.columns.payment_method') </th>
                        <th class="min-w-125px"> @lang('admin::datatable.package_subscriptions.columns.status') </th>
                        <th class="min-w-140px"> @lang('admin::datatable.package_subscriptions.columns.payment_status') </th>
                    @endslot

                    <script>
                        @slot('jsColumns')
                            {
                                data: 'user_full_name',
                                name: 'user_full_name',
                                orderable: false,
                                render: function (data, type, row, meta) {
                                    if (! row.user_email && ! data) {
                                        return '<span class="text-muted">—</span>';
                                    }
                                    const name = data || '—';
                                    const email = row.user_email || '';
                                    const providerViewUrl = @json($serviceProviderViewUrlTemplate).replace('900000001', String(row.user_id));
                                    const providerViewLinkAttrs = row.user_id
                                        ? ' href="' + providerViewUrl + '" target="_blank" rel="noopener noreferrer"'
                                        : ' href="javascript:;"';
                                    return `
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                <a` + providerViewLinkAttrs + `>
                                                    <div class="symbol-label">
                                                        <img src="${row.user_avatar_url}" alt="${name}" class="w-100" onerror="this.onerror=null; this.src='{{ asset('images/default/avatars/user.png') }}';"/>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="d-flex justify-content-start flex-column">
                                                <a` + providerViewLinkAttrs + ` class="text-dark fw-bolder text-hover-primary mb-1">${name}</a>
                                                <span class="text-muted">${email}</span>
                                            </div>
                                        </div>
                                    `;
                                }
                            },
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
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(function () {
            const awaitingVerificationValue = @json(PackageSubscriptionPaymentStatus::AwaitingVerification->value);

            $('#package-subscriptions-filter-awaiting-verification').on('click', function () {
                const $select = $('#data-table-filter select[name="payment_status"]');
                if (!$select.length) {
                    return;
                }

                $select.val(awaitingVerificationValue).trigger('change');

                if ($.fn.DataTable.isDataTable('#data-table')) {
                    $('#data-table').DataTable().ajax.reload();
                }
            });
        });
    </script>
@endpush
