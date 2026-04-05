@extends('admin::layouts.master', [
    'title' => trans('admin::dashboard.aside_menu.platform_management.verimor_call_events'),
])

@section('toolbar')
    @component('admin::includes.toolbar', [
            'options'               => [
                'title'             => trans('admin::dashboard.aside_menu.platform_management.verimor_call_events'),
                'actions'           => [
                    'filter'        => true,
                    'search'        => true,
                ],
            ],
        ])

        @slot('filterContent')
            <div class="mb-5">
                @include('admin::components.inputs.select', [
                    'options'           => [
                        'name'          => 'direction',
                        'label'         => trans('admin::datatable.verimor_call_events.columns.direction'),
                        'placeholder'   => trans('admin::base.all_results'),
                        'clearable'     => true,
                        'data'          => \Modules\Verimor\Enums\VerimorCallDirection::filterOptions(),
                        'text'          => function ($key, $value) { return $value; },
                        'values'        => function ($key, $value) { return $key; },
                    ],
                ])
            </div>
            <div class="mb-5">
                @include('admin::components.inputs.select', [
                    'options'           => [
                        'name'          => 'event_type',
                        'label'         => trans('admin::datatable.verimor_call_events.columns.event_type'),
                        'placeholder'   => trans('admin::base.all_results'),
                        'clearable'     => true,
                        'data'          => \Modules\Verimor\Enums\VerimorCallEventType::filterOptions(),
                        'text'          => function ($key, $value) { return $value; },
                        'values'        => function ($key, $value) { return $key; },
                    ],
                ])
            </div>
            <div class="mb-5">
                @include('admin::components.inputs.select', [
                    'options'           => [
                        'id'            => 'verimor_call_events_filter_user_id',
                        'name'          => 'user_id',
                        'label'         => trans('admin::datatable.verimor_call_events.columns.provider'),
                        'placeholder'   => trans('admin::base.all_results'),
                        'clearable'     => true,
                        'isAjax'        => true,
                        'url'           => route('auth.users.ajaxList', ['userType' => \Modules\Auth\Enums\UserType::ServiceProvider->value]),
                        'selected'      => $verimorCallEventsFilterUserSelected ?? [],
                    ],
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
                                'role'  => null,
                                'title' => trans('admin::datatable.verimor_call_events.list_title'),
                                'withSwitchArchive' => false,
                            ],
                        ])
                    </div>
                </div>

                <div class="card-toolbar flex-row-reverse">
                    @include('admin::components.datatables.header.toolbar', [
                        'options'               => [
                            'withAddButton' => false,
                        ],
                    ])
                </div>
            </div>

            @php
                $verimorCallEventsDtLabels = [
                    'yes' => trans('verimor::strings.yes'),
                    'no' => trans('verimor::strings.no'),
                ];
                $verimorCallDirectionDtBadgeColors = \Modules\Verimor\Enums\VerimorCallDirection::datatableBadgeColorsByValue();
            @endphp

            <div class="card-body  py-4">
                @component('admin::components.datatables.table', [
                        'options'           => [
                            'url'           => route('verimor.verimor_call_events.datatable'),
                            'withCreatedAt' => true,
                            'createdAtColumn' => trans('admin::datatable.verimor_call_events.columns.created_at'),
                            'withTrash'     => false,
                            'filter'        => true,
                        ],
                    ])
                    @slot('columns')
                    <th class="min-w-275px">@lang('admin::datatable.verimor_call_events.columns.provider')</th>
                        <th class="min-w-200px">@lang('admin::datatable.verimor_call_events.columns.call_uuid')</th>
                        <th class="min-w-100px">@lang('admin::datatable.verimor_call_events.columns.direction')</th>
                        <th class="min-w-125px">@lang('admin::datatable.verimor_call_events.columns.destination')</th>
                        <th class="min-w-100px">@lang('admin::datatable.verimor_call_events.columns.answered')</th>
                        <th class="min-w-125px">@lang('admin::datatable.verimor_call_events.columns.consumed_quota')</th>
                    @endslot

                    <script>
                        @slot('jsColumns')
                        {
                                data: 'provider_user',
                                name: 'provider_user',
                                orderable: false,
                                searchable: false,
                                render: function (data, type, row, meta) {
                                    if (! data || ! data.full_name) {
                                        if (type !== 'display' && type !== 'filter') {
                                            return '';
                                        }
                                        return '<span class="text-muted">—</span>';
                                    }
                                    if (type !== 'display' && type !== 'filter') {
                                        return (data.full_name || '') + (data.email ? ' ' + data.email : '');
                                    }
                                    return `
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                <a href="javascript:;">
                                                    <div class="symbol-label">
                                                        <img src="${data.image_url}" alt="${data.full_name}" class="w-100" onerror="this.onerror=null; this.src='{{ asset('images/default/avatars/user.png') }}';" />
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="d-flex justify-content-start flex-column">
                                                <a href="javascript:;" class="text-dark fw-bolder text-hover-primary mb-1">${data.full_name}</a>
                                                <span class="text-muted">${data.email}</span>
                                            </div>
                                        </div>
                                    `;
                                }
                            },
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
        </div>
    </div>
@endsection

@section('modal')
    @include('admin::components.modals.view_modal', [
        'options' => [
            'id' => 'verimorCallEventViewModal',
            'title' => trans('verimor::strings.view_modal_title.call_event'),
        ],
    ])
@endsection
