@extends('admin::layouts.master', [
    'title' => trans('admin::dashboard.aside_menu.package_management.packages'),
])

@section('toolbar')
    @component('admin::includes.toolbar', [
            'options'               => [
                'title'             => trans('admin::dashboard.aside_menu.package_management.packages'),
                'actions'           => [
                    'filter'        => false,
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

            <div class="card-header">
                <div class="card-title">
                    @include('admin::components.datatables.header.title', [
                        'options'   => [
                            'role'  => $viewTrashPermission,
                            'title' => trans('admin::datatable.packages.list_title'),
                        ]
                    ])
                </div>

                <div class="card-toolbar flex-row-reverse">
                    @include('admin::components.datatables.header.toolbar', [
                        'options'               => [
                            'role'              => $createPermission,
                            'multiActions'      => $bulkActionDropdown,
                            'route'             => route('platform.packages.create'),
                        ]
                    ])
                </div>
            </div>

            <div class="card-body  py-4">
                @component('admin::components.datatables.table', [
                        'options'           => [
                            'url'           => route('platform.packages.datatable'),
                            'withCreatedAt' => true,
                        ]
                    ])
                    @slot('columns')
                        <th> @lang('admin::datatable.base_columns.name') </th>
                        <th> @lang('admin::datatable.packages.columns.free_tier') </th>
                        <th> @lang('admin::datatable.packages.columns.popular') </th>
                        <th> @lang('admin::datatable.packages.columns.price') </th>
                        <th> @lang('admin::datatable.packages.columns.billing_period') </th>
                        <th> @lang('admin::datatable.packages.columns.connections_count') </th>
                        <th> @lang('admin::datatable.packages.columns.services') </th>
                    @endslot

                    <script>
                        @slot('jsColumns')
                            {
                                data: 'name',
                                name: 'name',
                            },
                            {
                                data: 'free_tier_badge',
                                name: 'free_tier_badge',
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
                                data: 'popular_badge',
                                name: 'popular_badge',
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
                                data: 'price_display',
                                name: 'price_display',
                                orderable: false,
                                searchable: false,
                            },
                            {
                                data: 'billing_period',
                                name: 'billing_period',
                                orderable: false,
                                searchable: false,
                            },
                            {
                                data: 'connections_count',
                                name: 'connections_count',
                                orderable: false,
                                searchable: false,
                            },
                            {
                                data: 'services_list',
                                name: 'services_list',
                                orderable: false,
                                searchable: false,
                            },
                        @endslot
                    </script>

                @endcomponent
            </div>
        </div>
    </div>
@endsection

@push('script')

@endpush
