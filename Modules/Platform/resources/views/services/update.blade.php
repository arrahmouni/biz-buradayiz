@extends('admin::layouts.master', ['title' => trans('admin::cruds.services.edit')])

@section('toolbar')
    @include('admin::includes.toolbar', [
        'options'               => [
            'title'             => trans('admin::dashboard.aside_menu.service_management.services'),
            'backUrl'           => route('platform.services.index'),
            'actions'           => [
                'save'          => true,
                'back'          => true,
            ],
        ]
    ])
@endsection

@push('style')

@endpush

@section('content')
    <div id="kt_content_container" class="container-fluid">
        <div class="row g-5">
            <div class="col-lg-3"></div>

            <div class="col-xxl-6 col-12">
                <div class="card card-bordered mb-5">
                    <div class="card-header">
                        <h3 class="card-title">
                            @lang('admin::cruds.services.edit')
                        </h3>
                    </div>
                    <div class="card-body">
                        @component('admin::components.forms.form', [
                                'options'       => [
                                    'isAjax'            => true,
                                    'action'            => route('platform.services.postUpdate', [$model->id]),
                                    'method'            => 'PUT',
                                    'addEmptyCheckbox'  => true,
                                ]
                            ])
                            @slot('fields')

                                <div class="row">
                                    <div class="col-12">
                                        @include('admin::components.other.lang_crud', [
                                            'options'           => [
                                                'name'         => [
                                                    'show'      => true,
                                                    'required'  => true,
                                                    'value'     => function($model, $locale) {
                                                        return $model->smartTrans('name', $locale, true);
                                                    },
                                                ],
                                                'description'  => [
                                                    'show'      => true,
                                                    'required'  => false,
                                                    'value'     => function($model, $locale) {
                                                        return $model->smartTrans('description', $locale, true);
                                                    },
                                                ],
                                            ]
                                        ])
                                    </div>
                                    <div class="col-12 mt-5">
                                        @include('admin::components.inputs.switch', [
                                            'options' => [
                                                'name'      => 'show_in_search_filters',
                                                'label'     => trans('admin::cruds.services.show_in_search_filters'),
                                                'checked'   => (bool) old('show_in_search_filters', $model->show_in_search_filters),
                                                'value'     => '1',
                                                'view'      => 'INLINE',
                                            ],
                                        ])
                                    </div>
                                </div>

                            @endslot
                        @endcomponent
                    </div>
                </div>
            </div>

            <div class="col-lg-3"></div>
        </div>
    </div>
@endsection

@push('script')

@endpush
