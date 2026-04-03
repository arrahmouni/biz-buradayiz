@extends('admin::layouts.master', ['title' => $isServiceProvider
    ? trans('admin::dashboard.aside_menu.user_management.create_service_provider')
    : trans('admin::dashboard.aside_menu.user_management.create_customer')])

@section('toolbar')
    @include('admin::includes.toolbar', [
        'options'                   => [
            'title'                 => $isServiceProvider
                ? trans('admin::dashboard.aside_menu.user_management.create_service_provider')
                : trans('admin::dashboard.aside_menu.user_management.create_customer'),
            'backUrl'               => route('auth.users.index', ['userType' => $userType->value]),
            'createUrl'             => route('auth.users.create', ['userType' => $userType->value]),
            'actions'               => [
                'save'              => true,
                'saveAndCreateNew'  => true,
                'back'              => true,
            ],
        ]
    ])
@endsection

@section('content')
    <div id="kt_content_container" class="container-fluid">
        <div class="row g-5">
            <div class="col-lg-3"></div>

            <div class="col-12">
                <div class="card card-bordered mb-5">
                    <div class="card-header">
                        <h3 class="card-title">
                            {{ $isServiceProvider
                                ? trans('admin::dashboard.aside_menu.user_management.create_service_provider')
                                : trans('admin::dashboard.aside_menu.user_management.create_customer') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        @component('admin::components.forms.form', [
                                'options'       => [
                                    'isAjax'    => true,
                                    'action'    => route('auth.users.postCreate', ['userType' => $userType->value]),
                                ]
                            ])
                            @slot('fields')

                                <div class="fv-row d-flex justify-content-center">
                                    <div class="d-flex flex-column align-items-center mb-10 form-group">
                                        @include('admin::components.inputs.image', [
                                            'options'       => [
                                                'name'              => 'image',
                                                'removeFieldName'   => 'image_remove',
                                                'isAvatar'          => true,
                                                'width'             => '125',
                                                'height'            => '125',
                                                'circle'            => true,
                                                'subText'           => trans('admin::inputs.user_crud.image.subText', [
                                                    'types' => getImageTypes(true),
                                                    'size'  => config('base.file.image.max_size'),
                                                ]),
                                                'accept'            => getImageTypes(),
                                            ]
                                        ])
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6 col-12 mb-10 form-group">
                                        @include('admin::components.inputs.select', [
                                            'options'           => [
                                                'name'          => 'status',
                                                'label'         => trans('admin::inputs.base_crud.status.label'),
                                                'placeholder'   => trans('admin::inputs.base_crud.status.placeholder'),
                                                'help'          => trans('admin::inputs.base_crud.status.help'),
                                                'required'      => true,
                                                'data'          => $adminStatuses,
                                                'text'          => fn($key, $value) => $value,
                                                'values'        => fn($key, $value) => $key,
                                            ]
                                        ])
                                    </div>

                                    <div class="col-lg-6 col-12 mb-10 form-group">
                                        @include('admin::components.inputs.select', [
                                            'options'           => [
                                                'name'          => 'lang',
                                                'label'         => trans('admin::inputs.base_crud.lang.label'),
                                                'placeholder'   => trans('admin::inputs.base_crud.lang.placeholder'),
                                                'help'          => trans('admin::inputs.base_crud.lang.help'),
                                                'required'      => true,
                                                'data'          => $_ALL_LOCALE_,
                                                'text'          => fn($key, $value) => $value['native'],
                                                'values'        => fn($key, $value) => $key,
                                            ]
                                        ])
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6 col-12 mb-10 form-group">
                                        @include('admin::components.inputs.text', [
                                            'options'           => [
                                                'name'          => 'first_name',
                                                'label'         => trans('admin::inputs.user_crud.first_name.label'),
                                                'placeholder'   => trans('admin::inputs.user_crud.first_name.placeholder'),
                                                'required'      => true,
                                            ],
                                        ])
                                    </div>

                                    <div class="col-lg-6 col-12 mb-10 form-group">
                                        @include('admin::components.inputs.text', [
                                            'options'           => [
                                                'name'          => 'last_name',
                                                'label'         => trans('admin::inputs.user_crud.last_name.label'),
                                                'placeholder'   => trans('admin::inputs.user_crud.last_name.placeholder'),
                                                'required'      => true,
                                            ],
                                        ])
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6 col-12 mb-10 form-group">
                                        @include('admin::components.inputs.text', [
                                            'options'           => [
                                                'name'          => 'email',
                                                'label'         => trans('admin::inputs.base_crud.email.label'),
                                                'placeholder'   => trans('admin::inputs.base_crud.email.placeholder'),
                                                'required'      => true,
                                                'emailMask'     => true,
                                            ]
                                        ])
                                    </div>
                                    <div class="col-lg-6 col-12 mb-10 form-group">
                                        @include('admin::components.inputs.phone', [
                                            'options'               => [
                                                'name'              => 'phone',
                                                'fullNumberName'    => 'phone_number',
                                                'label'             => trans('admin::inputs.admin_crud.phone_number.label'),
                                                'required'          => true,
                                            ]
                                        ])
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6 col-12 mb-10 form-group">
                                        @include('admin::components.inputs.password', [
                                            'options'           => [
                                                'name'          => 'password',
                                                'label'         => trans('admin::inputs.base_crud.password.label'),
                                                'placeholder'   => trans('admin::inputs.base_crud.password.placeholder'),
                                                'help'          => trans('admin::inputs.base_crud.password.help'),
                                                'required'      => true,
                                            ]
                                        ])
                                    </div>
                                    <div class="col-lg-6 col-12 mb-10 form-group">
                                        @include('admin::components.inputs.password', [
                                            'options'           => [
                                                'name'          => 'password_confirmation',
                                                'label'         => trans('admin::inputs.base_crud.password_confirmation.label'),
                                                'placeholder'   => trans('admin::inputs.base_crud.password_confirmation.placeholder'),
                                                'help'          => trans('admin::inputs.base_crud.password_confirmation.help'),
                                                'highlight'     => false,
                                                'required'      => true,
                                            ]
                                        ])
                                    </div>
                                </div>

                                @if($isServiceProvider)
                                    @include('auth::users.partials.service-provider-fields', ['model' => null])
                                @endif

                                <div class="separator separator-dashed my-5"></div>
                            @endslot
                        @endcomponent
                    </div>
                </div>
            </div>

            <div class="col-lg-3"></div>
        </div>
    </div>
@endsection
