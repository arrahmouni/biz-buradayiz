@php
    use Modules\Platform\Enums\BillingPeriod;
@endphp

@extends('admin::layouts.master', ['title' => trans('admin::cruds.packages.add')])

@section('toolbar')
    @include('admin::includes.toolbar', [
        'options'               => [
            'title'             => trans('admin::dashboard.aside_menu.package_management.packages'),
            'backUrl'           => route('platform.packages.index'),
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
                            @lang('admin::cruds.packages.add')
                        </h3>
                    </div>
                    <div class="card-body">
                        @component('admin::components.forms.form', [
                                'options'       => [
                                    'isAjax'    => true,
                                    'action'    => route('platform.packages.postCreate'),
                                ]
                            ])
                            @slot('fields')
                                <div class="row">
                                    <div class="col-12">
                                        @include('admin::components.other.lang_crud', [
                                            'options'           => [
                                                'name'          => [
                                                    'show'      => true,
                                                    'required'  => true,
                                                    'value'     => null,
                                                ],
                                                'description'   => [
                                                    'show'      => true,
                                                    'required'  => false,
                                                    'value'     => null,
                                                ],
                                                'features'      => [
                                                    'show'      => true,
                                                    'required'  => false,
                                                    'value'     => null,
                                                ],
                                            ]
                                        ])
                                    </div>
                                </div>

                                <div class="separator separator-dashed my-5"></div>

                                <div class="row">
                                    <div class="col-12 mb-10 form-group">
                                        @include('admin::components.inputs.checkbox', [
                                            'options' => [
                                                'id'        => 'package_is_free_tier',
                                                'name'      => 'is_free_tier',
                                                'label'     => trans('admin::cruds.packages.is_free_tier'),
                                                'checked'   => old('is_free_tier', false),
                                                'value'     => '1',
                                            ],
                                        ])
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 mb-10 form-group">
                                        @include('admin::components.inputs.select', [
                                            'options' => [
                                                'id'            => 'package_service_ids',
                                                'name'          => 'service_ids',
                                                'label'         => trans('admin::cruds.packages.services'),
                                                'placeholder'   => trans('admin::cruds.packages.services_placeholder'),
                                                'required'      => true,
                                                'isAjax'        => true,
                                                'multiple'      => true,
                                                'url'           => route('platform.services.ajaxList'),
                                                'selected'      => [],
                                            ],
                                        ])
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6 col-12 mb-10 form-group">
                                        @include('admin::components.inputs.text', [
                                            'options' => [
                                                'id'        => 'package_price',
                                                'name'      => 'price',
                                                'type'      => 'number',
                                                'label'     => trans('admin::cruds.packages.price'),
                                                'required'  => true,
                                                'value'     => old('price'),
                                                'disabled'  => old('is_free_tier', false),
                                            ],
                                        ])
                                    </div>
                                    <div class="col-lg-6 col-12 mb-10 form-group">
                                        @include('admin::components.inputs.select', [
                                            'options' => [
                                                'name'      => 'currency',
                                                'label'     => trans('admin::cruds.packages.currency'),
                                                'required'  => true,
                                                'data'      => getCurrencySelectOptions(),
                                                'text'      => fn ($key, $value) => $value,
                                                'values'    => fn ($key, $value) => $key,
                                            ],
                                        ])
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-4 col-12 mb-10 form-group">
                                        @include('admin::components.inputs.text', [
                                            'options' => [
                                                'name'      => 'sort_order',
                                                'type'      => 'number',
                                                'label'     => trans('admin::cruds.packages.sort_order'),
                                                'required'  => false,
                                                'value'     => old('sort_order', 0),
                                            ],
                                        ])
                                    </div>
                                    <div class="col-lg-4 col-12 mb-10 form-group">
                                        @include('admin::components.inputs.text', [
                                            'options' => [
                                                'name'      => 'connections_count',
                                                'type'      => 'number',
                                                'label'     => trans('admin::cruds.packages.connections_count'),
                                                'required'  => true,
                                                'value'     => old('connections_count', 1),
                                            ],
                                        ])
                                    </div>
                                    <div class="col-lg-4 col-12 mb-10 form-group">
                                        @include('admin::components.inputs.select', [
                                            'options' => [
                                                'name'      => 'billing_period',
                                                'label'     => trans('admin::cruds.packages.billing_period'),
                                                'required'  => true,
                                                'data'      => BillingPeriod::getBillingPeriodsSelectOptions(),
                                                'text'      => fn ($key, $value) => $value,
                                                'values'    => fn ($key, $value) => $key,
                                                'value'     => old('billing_period', BillingPeriod::Monthly->value),
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
    <script>
        $(function () {
            var $free = $('#package_is_free_tier');
            var $price = $('#package_price');
            function syncPackageFreeTierPrice() {
                if ($free.is(':checked')) {
                    $price.val('0').prop('disabled', true);
                } else {
                    $price.prop('disabled', false);
                }
            }
            $free.on('change', syncPackageFreeTierPrice);
            syncPackageFreeTierPrice();
        });
    </script>
@endpush
