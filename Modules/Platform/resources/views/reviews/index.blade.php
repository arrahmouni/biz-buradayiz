@extends('admin::layouts.master', [
    'title' => trans('admin::dashboard.aside_menu.review_management.reviews'),
])

@section('toolbar')
    @component('admin::includes.toolbar', [
            'options'               => [
                'title'             => trans('admin::dashboard.aside_menu.review_management.reviews'),
                'actions'           => [
                    'filter'        => true,
                    'search'        => true,
                ],
            ]
        ])

        @slot('filterContent')
            {{-- Filter Contetn --}}
        @endslot
    @endcomponent
@endsection

@push('style')

@endpush

@section('content')
    <div id="kt_content_container" class="container-fluid">
        <div class="card shadow-sm ">

            <!--begin::Card header-->
            <div class="card-header">
                <!--begin::Card title-->
                <div class="card-title">
                    @include('admin::components.datatables.header.title', [
                        'options'   => [
                            'role'  => $viewTrashPermission,
                            'title' => trans('admin::datatable.reviews.list_title'),
                        ]
                    ])
                </div>
                <!--begin::Card title-->

                <!--begin::Card toolbar-->
                <div class="card-toolbar flex-row-reverse">
                    @include('admin::components.datatables.header.toolbar', [
                        'options'               => [
                            'withAddButton'     => false,
                        ]
                    ])
                </div>
                <!--end::Card toolbar-->
            </div>
            <!--end::Card header-->

            <!--begin::Card body-->
            <div class="card-body  py-4">
                @php
                    use Modules\Platform\Enums\ReviewStatus;

                    $reviewStatusDtLabels = [];
                    $reviewStatusDtBadgeColors = [];
                    foreach (ReviewStatus::cases() as $case) {
                        $reviewStatusDtLabels[$case->value] = trans('admin::cruds.reviews.statuses.'.$case->value);
                        $reviewStatusDtBadgeColors[$case->value] = $case->datatableBadgeColor();
                    }
                @endphp
                @component('admin::components.datatables.table', [
                        'options'           => [
                            'url'           => route('platform.reviews.datatable'),
                            'createdAtColumn' => trans('admin::datatable.base_columns.created_at'),
                        ]
                    ])
                    @slot('columns')
                        {{-- Datatable Columns --}}
                        <th> @lang('admin::inputs.package_subscriptions_crud.service_provider.label') </th>
                        <th> @lang('admin::datatable.reviews.columns.rating') </th>
                        <th> @lang('admin::datatable.reviews.columns.status') </th>
                        <th> @lang('admin::datatable.reviews.columns.reviewer_display_name') </th>
                        <th> @lang('admin::datatable.reviews.columns.reviewer_phone') </th>
                        <th> @lang('admin::datatable.reviews.columns.comment') </th>
                        {{-- <th> @lang('admin::datatable.reviews.columns.call_event') </th> --}}
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
                                data: 'rating',
                                name: 'rating',
                                render: function (data, type, row, meta) {
                                    if (data === null || data === undefined) {
                                        return '<span class="text-muted">—</span>';
                                    }
                                    var r = parseInt(data, 10);
                                    if (isNaN(r) || r < 1) {
                                        return '<span class="text-muted">—</span>';
                                    }
                                    if (type !== 'display' && type !== 'filter') {
                                        return data;
                                    }
                                    var html = '<span class="d-inline-flex align-items-center gap-1">';
                                    for (var i = 1; i <= 5; i++) {
                                        html += '<i class="bi ' + (i <= r ? 'bi-star-fill text-warning' : 'bi-star text-gray-400') + ' fs-8"></i>';
                                    }
                                    html += '<span class="text-muted fs-8 ms-1">' + r + '/5</span></span>';
                                    return html;
                                }
                            },
                            {
                                data: 'status',
                                name: 'status',
                                orderable: false,
                                searchable: false,
                                render: function (data, type, row, meta) {
                                    var value = data != null && data !== '' ? String(data) : (row.status != null ? String(row.status) : '');
                                    if (type !== 'display' && type !== 'filter') {
                                        return value;
                                    }
                                    if (! value) {
                                        return '<span class="text-muted">—</span>';
                                    }
                                    var labels = @json($reviewStatusDtLabels);
                                    var colors = @json($reviewStatusDtBadgeColors);
                                    var label = labels[value] != null ? labels[value] : value;
                                    var color = colors[value] != null ? colors[value] : 'secondary';
                                    var safeLabel = String(label).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
                                    return '<span class="btn btn-sm btn-font-sm btn-label-' + color + ' fs-7 fw-semibold text-center w-100">' + safeLabel + '</span>';
                                }
                            },
                            {
                                data: 'reviewer_display_name',
                                name: 'reviewer_display_name',
                                render: function (data, type, row, meta) {
                                    if (! data) {
                                        return '<span class="text-muted">—</span>';
                                    }
                                    if (type !== 'display' && type !== 'filter') {
                                        return data;
                                    }
                                    return '<span class="text-dark fw-semibold">' + data + '</span>';
                                }
                            },
                            {
                                data: 'reviewer_phone_normalized',
                                name: 'reviewer_phone_normalized',
                                render: function (data, type, row, meta) {
                                    if (! data) {
                                        return '<span class="text-muted">—</span>';
                                    }
                                    if (type !== 'display' && type !== 'filter') {
                                        return data;
                                    }
                                    return '<span class="text-dark fw-semibold">' + data + '</span>';
                                }
                            },
                            {
                                data: 'body_preview',
                                name: 'body_preview',
                                orderable: false,
                                searchable: false,
                                render: function (data, type, row, meta) {
                                    if (! data || data === '—') {
                                        return '<span class="text-muted">—</span>';
                                    }
                                    if (type !== 'display' && type !== 'filter') {
                                        return data;
                                    }
                                    const t = String(data).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
                                    return '<span class="text-gray-800">' + t + '</span>';
                                }
                            },
                            // {
                            //     data: 'call_event',
                            //     name: 'call_event',
                            //     orderable: false,
                            //     searchable: false,
                            //     render: function (data, type, row, meta) {
                            //         if (! data || ! data.call_uuid) {
                            //             if (type !== 'display' && type !== 'filter') {
                            //                 return '';
                            //             }
                            //             return '<span class="text-muted">—</span>';
                            //         }
                            //         if (type !== 'display' && type !== 'filter') {
                            //             return data.call_uuid;
                            //         }
                            //         return '<span class="text-dark fw-semibold font-monospace fs-8 lh-sm">' + data.call_uuid + '</span>';
                            //     }
                            // },
                        @endslot
                    </script>

                @endcomponent
            </div>
            <!--end::Card body-->
        </div>
    </div>
@endsection

@push('script')

@endpush

@section('modal')
    @include('admin::components.modals.view_modal', [
        'options' => [
            'id' => 'reviewViewModal',
            'title' => trans('admin::cruds.reviews.view'),
        ],
    ])
@endsection
