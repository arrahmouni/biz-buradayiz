@extends('admin::auth.layouts.master')

@section('title', trans('admin::auth.login_page.meta_title'))

@push('style')
    <link rel="stylesheet" href="{{ Module::asset('admin:global/css/admin-login.css') }}?v={{ $_STYLE_VER_ }}">
    <style>
        .admin-modern-login-page .admin-login-background-section {
            background-image: url('{{ asset('modules/admin/metronic/demo/media/patterns/A7.png') }}');
        }
    </style>
@endpush

@section('mainContent')
    <div class="modern-login-container">
        <div class="modern-login-card">
            <div class="login-card-header">
                <a href="javascript:;" class="modern-login-logo-link">
                    @include('admin::components.other.image', [
                        'options' => [
                            'class' => 'modern-login-logo',
                            'src'   => getSetting('app_mobile_logo', asset('images/default/logos/app_mobile_logo.svg')),
                            'alt'   => 'Logo',
                        ]
                    ])
                </a>
                <h1 class="modern-title">
                    @lang('admin::auth.login_page.sign_in_to_account')
                </h1>
                <p class="modern-subtitle">
                    @lang('admin::auth.login_page.subtitle')
                </p>
            </div>

            <form class="modern-login-form" id="login" action="{{ route('admin.auth.authenticate') }}" method="POST">
                @csrf

                @include('admin::components.inputs.text', [
                    'options' => [
                        'name'      => 'email',
                        'type'      => 'email',
                        'view'      => 'MODERN_LOGIN',
                        'label'     => trans('admin::inputs.base_crud.email.label'),
                        'required'  => true,
                    ],
                ])

                @include('admin::components.inputs.password', [
                    'options' => [
                        'name'          => 'password',
                        'view'          => 'MODERN_LOGIN',
                        'label'         => trans('admin::inputs.base_crud.password.label'),
                        'highlight'     => false,
                        'required'      => true,
                    ],
                ])

                <div class="text-center">
                    @include('admin::components.buttons.submit', [
                        'options' => [
                            'id'        => 'login_submit',
                            'label'     => trans('admin::auth.login_page.sign_in'),
                            'progress_label' => trans('admin::base.verifying'),
                            'variant'   => 'modern_login',
                        ],
                    ])
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script>
        document.getElementById('login').addEventListener('submit', function () {
            var form = this;
            if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
                return;
            }
            var btn = document.getElementById('login_submit');
            if (!btn) {
                return;
            }
            btn.disabled = true;
            btn.setAttribute('data-kt-indicator', 'on');
        });
    </script>
@endpush
