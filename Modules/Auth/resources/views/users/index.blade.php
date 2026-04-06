@extends('admin::layouts.master', [
    'title' => $isServiceProvider
        ? trans('admin::dashboard.aside_menu.user_management.service_providers')
        : trans('admin::dashboard.aside_menu.user_management.customers'),
])

@section('toolbar')
    @component('admin::includes.toolbar', [
            'options'               => [
                'title'             => $isServiceProvider
                    ? trans('admin::dashboard.aside_menu.user_management.service_providers')
                    : trans('admin::dashboard.aside_menu.user_management.customers'),
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
                        'label'         => trans('admin::datatable.base_columns.status'),
                        'placeholder'   => trans('admin::base.all_results'),
                        'clearable'     => true,
                        'data'          => $adminStatuses,
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
                    @include('admin::components.datatables.header.title', [
                        'options'   => [
                            'role'  => $viewTrashPermission,
                            'title' => $isServiceProvider
                                ? trans('admin::datatable.users.list_title_service_providers')
                                : trans('admin::datatable.users.list_title_customers'),
                        ]
                    ])
                </div>

                <div class="card-toolbar flex-row-reverse">
                    @include('admin::components.datatables.header.toolbar', [
                        'options'               => [
                            'role'              => $createPermission,
                            'multiActions'      => $bulkActionDropdown,
                            'route'             => route('auth.users.create', ['userType' => $userType->value]),
                        ]
                    ])
                </div>
            </div>

            <div class="card-body  py-4">
                @php
                    $serviceProviderViewUrlTemplate = $isServiceProvider
                        ? route('auth.users.view', ['userType' => $userType->value, 'model' => 900000001])
                        : '';
                @endphp
                @component('admin::components.datatables.table', [
                        'options'           => [
                            'url'           => route('auth.users.datatable', ['userType' => $userType->value]),
                            'filter'        => true,
                        ]
                    ])
                    @slot('columns')
                        <th style="width: 30%">
                            @lang('admin::inputs.package_subscriptions_crud.'.strtolower(str_replace('-', '_', $userType->value)).'.label')
                        </th>
                        <th> @lang('admin::datatable.base_columns.phone_number') </th>
                        <th> @lang('admin::datatable.base_columns.central_phone') </th>
                        <th style="width: 8%"> @lang('admin::datatable.base_columns.status') </th>
                        <th> @lang('admin::datatable.admins.columns.joined_date') </th>
                        @if($isServiceProvider)
                            <th> @lang('admin::datatable.users.columns.service_type') </th>
                            <th> @lang('admin::datatable.users.columns.state') </th>
                            <th> @lang('admin::datatable.users.columns.city') </th>
                        @endif
                    @endslot

                    <script>
                        @slot('jsColumns')
                            {
                                data: 'full_name',
                                name: 'full_name',
                                orderable: false,
                                searchable: false,
                                render: function (data, type, row, meta) {
                                    @if ($isServiceProvider)
                                        const providerViewUrl = @json($serviceProviderViewUrlTemplate).replace('900000001', String(row.id));
                                        const providerViewLinkAttrs = ' href="' + providerViewUrl + '" target="_blank" rel="noopener noreferrer"';
                                    @else
                                        const providerViewLinkAttrs = ' href="javascript:;"';
                                    @endif
                                    return `
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                <a` + providerViewLinkAttrs + `>
                                                    <div class="symbol-label">
                                                        <img src="${row.image_url}" alt="${row.full_name}" class="w-100" onerror="this.onerror=null; this.src='{{ asset('images/default/avatars/user.png') }}';" />
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="d-flex justify-content-start flex-column">
                                                <a` + providerViewLinkAttrs + ` class="text-dark fw-bolder text-hover-primary mb-1">${row.full_name}</a>
                                                <span class="text-muted">${row.email}</span>
                                            </div>
                                        </div>
                                    `;
                                }
                            },
                            {
                                data        : 'phone_number',
                                name        : 'phone_number',
                                orderable   : false,
                                searchable  : false,
                                render      : function (data, type, row, meta) {
                                    return isEmpty(data) ? "{{ DEFAULT_PHONE }}" :
                                    `
                                        <a href="tel:${data}" class="text-dark fw-bolder text-hover-primary">${data}</a>
                                    `;
                                }
                            },
                            {
                                data        : 'central_phone',
                                name        : 'central_phone',
                                orderable   : false,
                                searchable  : false,
                                render      : function (data, type, row, meta) {
                                    return isEmpty(data) ? '—' :
                                    `
                                        <a href="tel:${data}" class="text-dark fw-bolder text-hover-primary">${data}</a>
                                    `;
                                }
                            },
                            {
                                data : 'status_format',
                                name : 'status_format',
                                orderable: false,
                                searchable: false,
                                render: function (data, type, row, meta) {
                                    return `
                                        <span class="btn btn-sm btn-font-sm btn-label-${data.color} text-center w-100">${data.label}</span>
                                    `;
                                }
                            },
                            {
                                data : 'created_at_format',
                                name : 'created_at_format',
                                orderable: false,
                                searchable: false,
                                render: function (data, type, row, meta) {
                                    return `
                                        <span class="text-gray-600 fw-semibold">${data}</span>
                                    `;
                                }
                            },
                            @if($isServiceProvider)
                            {
                                data: 'service_name',
                                name: 'service_name',
                                orderable: false,
                                searchable: false,
                                render: function (data, type, row, meta) {
                                    return `<span class="text-gray-700 fw-semibold">${data ?? '—'}</span>`;
                                }
                            },
                            {
                                data: 'state_name',
                                name: 'state_name',
                                orderable: false,
                                searchable: false,
                                render: function (data, type, row, meta) {
                                    return `<span class="text-gray-700 fw-semibold">${data ?? '—'}</span>`;
                                }
                            },
                            {
                                data: 'city_name',
                                name: 'city_name',
                                orderable: false,
                                searchable: false,
                                render: function (data, type, row, meta) {
                                    return `<span class="text-gray-700 fw-semibold">${data ?? '—'}</span>`;
                                }
                            },
                            @endif
                        @endslot
                    </script>

                @endcomponent
            </div>
        </div>
    </div>
@endsection
