@extends('admin::layouts.master', [
    'title' => trans('seo::seo_entries.list_title'),
])

@section('toolbar')
    @component('admin::includes.toolbar', [
            'options'               => [
                'title'             => trans('seo::seo_entries.list_title'),
                'actions'           => [
                    'filter'        => false,
                    'search'        => true,
                ],
            ]
        ])
        @slot('filterContent')
        @endslot
    @endcomponent
@endsection

@section('content')
    <div id="kt_content_container" class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header">
                <div class="card-title">
                    @include('admin::components.datatables.header.title', [
                        'options'   => [
                            'role'  => $viewTrashPermission ?? null,
                            'title' => trans('seo::seo_entries.list_title'),
                        ]
                    ])
                </div>
                <div class="card-toolbar flex-row-reverse">
                    @include('admin::components.datatables.header.toolbar', [
                        'options'               => [
                            'role'              => $createPermission,
                            'multiActions'      => $bulkActionDropdown,
                            'route'             => route('seo.entries.create'),
                        ]
                    ])
                </div>
            </div>
            <div class="card-body py-4">
                @component('admin::components.datatables.table', [
                        'options'           => [
                            'url'           => route('seo.entries.datatable'),
                            'filter'        => false,
                        ]
                    ])
                    @slot('columns')
                        <th>@lang('seo::datatable.columns.target')</th>
                        <th>@lang('seo::datatable.columns.meta_title')</th>
                    @endslot

                    <script>
                        @slot('jsColumns')
                            {
                                data: 'target',
                                name: 'target',
                                orderable: false,
                                searchable: false,
                            },
                            {
                                data: 'meta_title',
                                name: 'meta_title',
                                orderable: false,
                            },
                        @endslot
                    </script>
                @endcomponent
            </div>
        </div>
    </div>
@endsection
