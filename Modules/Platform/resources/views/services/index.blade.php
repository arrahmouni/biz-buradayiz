@extends('admin::layouts.master', [
    'title' => trans('admin::dashboard.aside_menu.service_management.services'),
])

@section('toolbar')
    @component('admin::includes.toolbar', [
            'options'               => [
                'title'             => trans('admin::dashboard.aside_menu.service_management.services'),
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

            <!--begin::Card header-->
            <div class="card-header">
                <!--begin::Card title-->
                <div class="card-title">
                    @include('admin::components.datatables.header.title', [
                        'options'   => [
                            'role'  => $viewTrashPermission,
                            'title' => trans('admin::datatable.services.list_title'),
                        ]
                    ])
                </div>
                <!--begin::Card title-->

                <!--begin::Card toolbar-->
                <div class="card-toolbar flex-row-reverse">
                    @include('admin::components.datatables.header.toolbar', [
                        'options'               => [
                            'role'              => $createPermission,
                            'multiActions'      => $bulkActionDropdown,
                            'route'             => route('platform.services.create'),
                        ]
                    ])
                </div>
                <!--end::Card toolbar-->
            </div>
            <!--end::Card header-->

            <!--begin::Card body-->
            <div class="card-body  py-4">
                @component('admin::components.datatables.table', [
                        'options'           => [
                            'url'           => route('platform.services.datatable'),
                            'filter'        => true,
                        ]
                    ])
                    @slot('columns')
                        {{-- Datatable Columns --}}
                        <th class="w-75px"> @lang('admin::datatable.services.columns.icon') </th>
                        <th> @lang('admin::datatable.base_columns.name') </th>
                        <th> @lang('admin::datatable.base_columns.description') </th>
                        <th class="text-center w-100px"> @lang('admin::datatable.services.columns.service_providers_count') </th>
                        <th> @lang('admin::datatable.services.columns.show_in_search_filters') </th>
                    @endslot

                    <script>
                        @slot('jsColumns')
                            {
                                data: 'icon',
                                name: 'icon',
                                orderable: false,
                                searchable: false,
                                className: 'text-center',
                                render: function (data, type) {
                                    const raw = data == null ? '' : String(data).trim();
                                    if (type === 'sort' || type === 'filter' || type === 'type' || type === 'export') {
                                        return raw;
                                    }
                                    if (raw === '') {
                                        return type === 'display'
                                            ? '<span class="text-muted">—</span>'
                                            : '';
                                    }
                                    const isFontAwesome = raw.startsWith('fa');
                                    if (isFontAwesome) {
                                        const safeClass = raw.replace(/[^a-zA-Z0-9 _-]/g, '');
                                        return '<i class="' + safeClass + ' fs-2x"></i>';
                                    }
                                    const div = document.createElement('div');
                                    div.textContent = raw;
                                    return '<span class="fs-2x lh-1 d-inline-flex align-items-center justify-content-center">' + div.innerHTML + '</span>';
                                },
                            },
                            {
                                data: 'name',
                                name: 'name',
                            },
                            {
                                data: 'description',
                                name: 'description',
                            },
                            {
                                data: 'service_providers_count',
                                name: 'service_providers_count',
                                orderable: false,
                                searchable: false,
                                className: 'text-center',
                            },
                            {
                                data: 'show_in_search_filters',
                                name: 'show_in_search_filters',
                                orderable: false,
                                searchable: false,
                            },
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
