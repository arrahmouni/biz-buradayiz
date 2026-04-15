@php
    use Modules\Auth\Enums\UserType;
    use Modules\Platform\Enums\PackageSubscriptionPaymentMethod;
    use Modules\Platform\Enums\PackageSubscriptionPaymentStatus;
    use Modules\Platform\Enums\PackageSubscriptionStatus;
    use Modules\Platform\Models\Package;

    $serviceProviderPreviewUrlTemplate = route('platform.package_subscriptions.service_provider_preview', ['user' => 0]);
    $freeTierPackageIds = Package::query()
        ->where('is_free_tier', true)
        ->pluck('id')
        ->map(fn ($id) => (int) $id)
        ->values()
        ->all();
@endphp

@extends('admin::layouts.master', ['title' => trans('admin::cruds.package_subscriptions.add')])

@section('toolbar')
    @include('admin::includes.toolbar', [
        'options'               => [
            'title'             => trans('admin::dashboard.aside_menu.platform_management.package_subscriptions'),
            'backUrl'           => route('platform.package_subscriptions.index'),
            'actions'           => [
                'save'          => true,
                'back'          => true,
            ],
        ]
    ])
@endsection

@section('content')
    <div id="kt_content_container" class="container-fluid">
        <div class="row g-5">
            <div class="col-lg-3"></div>

            <div class="col-xxl-6 col-12">
                <div class="card card-bordered mb-5">
                    <div class="card-header">
                        <h3 class="card-title">
                            @lang('admin::cruds.package_subscriptions.add')
                        </h3>
                    </div>
                    <div class="card-body">
                        @component('admin::components.forms.form', [
                                'options'       => [
                                    'isAjax'    => true,
                                    'action'    => route('platform.package_subscriptions.postCreate'),
                                ]
                            ])
                            @slot('fields')
                                <div class="row">
                                    <div class="col-12 mb-10 form-group">
                                        @include('admin::components.inputs.select', [
                                            'options' => [
                                                'id'            => 'package_subscription_user_id',
                                                'name'          => 'user_id',
                                                'label'         => trans('admin::inputs.package_subscriptions_crud.service_provider.label'),
                                                'placeholder'   => trans('admin::inputs.package_subscriptions_crud.service_provider.placeholder'),
                                                'subText'       => trans('admin::inputs.package_subscriptions_crud.service_provider.subText'),
                                                'required'      => true,
                                                'isAjax'        => true,
                                                'url'           => route('auth.users.ajaxList', ['userType' => UserType::ServiceProvider->value]),
                                                'selected'      => [],
                                            ],
                                        ])
                                    </div>
                                </div>

                                <div id="package_subscription_service_provider_preview" class="card card-bordered shadow-sm d-none mb-10 border-start border-4 border-primary">
                                    <div class="card-body py-6 px-6 px-lg-8">
                                        <div class="d-flex flex-column flex-md-row align-items-md-start gap-5 gap-md-7">
                                            <div class="symbol symbol-65px symbol-circle flex-shrink-0">
                                                <img src="" alt="" class="package-subscription-provider-preview__avatar w-100 rounded-circle" width="65" height="65">
                                            </div>
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
                                                    <h4 class="fs-5 fw-bold text-gray-900 mb-0 package-subscription-provider-preview__name"></h4>
                                                    <span class="badge badge-light-primary fw-semibold">
                                                        @lang('admin::inputs.package_subscriptions_crud.service_provider_preview.title')
                                                    </span>
                                                </div>
                                                <div class="row g-3 g-lg-4">
                                                    <div class="col-md-6">
                                                        <div class="text-gray-600 fs-8 fw-semibold text-uppercase ls-1 mb-1">
                                                            @lang('admin::inputs.package_subscriptions_crud.service_provider_preview.email')
                                                        </div>
                                                        <div class="text-gray-800 fs-6 fw-semibold text-break package-subscription-provider-preview__email"></div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="text-gray-600 fs-8 fw-semibold text-uppercase ls-1 mb-1">
                                                            @lang('admin::inputs.package_subscriptions_crud.service_provider_preview.phone')
                                                        </div>
                                                        <div class="text-gray-800 fs-6 package-subscription-provider-preview__phone"></div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="text-gray-600 fs-8 fw-semibold text-uppercase ls-1 mb-1">
                                                            @lang('admin::inputs.package_subscriptions_crud.service_provider_preview.central_phone')
                                                        </div>
                                                        <div class="text-gray-800 fs-6 package-subscription-provider-preview__central_phone"></div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="text-gray-600 fs-8 fw-semibold text-uppercase ls-1 mb-1">
                                                            @lang('admin::inputs.package_subscriptions_crud.service_provider_preview.service')
                                                        </div>
                                                        <div class="text-gray-800 fs-6 package-subscription-provider-preview__service"></div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="text-gray-600 fs-8 fw-semibold text-uppercase ls-1 mb-1">
                                                            @lang('admin::inputs.package_subscriptions_crud.service_provider_preview.city')
                                                        </div>
                                                        <div class="text-gray-800 fs-6 package-subscription-provider-preview__city"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 mb-10 form-group">
                                        @include('admin::components.inputs.select', [
                                            'options' => [
                                                'id'            => 'package_subscription_package_id',
                                                'name'          => 'package_id',
                                                'label'         => trans('admin::inputs.package_subscriptions_crud.package.label'),
                                                'placeholder'   => trans('admin::inputs.package_subscriptions_crud.package.placeholder'),
                                                'required'      => true,
                                                'isAjax'        => true,
                                                'url'           => route('platform.packages.ajaxList'),
                                                'selected'      => [],
                                            ],
                                        ])
                                    </div>
                                </div>

                                <div id="package_subscription_free_tier_notice" class="alert alert-primary d-none mb-10" role="alert">
                                    @lang('admin::cruds.package_subscriptions.create.free_tier_fields_locked')
                                </div>

                                <div class="row">
                                    <div class="col-lg-4 col-12 mb-10 form-group">
                                        @include('admin::components.inputs.select', [
                                            'options' => [
                                                'id'        => 'package_subscription_status',
                                                'name'      => 'status',
                                                'label'     => trans('admin::inputs.package_subscriptions_crud.status.label'),
                                                'required'  => true,
                                                'data'      => collect(PackageSubscriptionStatus::cases())->mapWithKeys(
                                                    fn ($case) => [$case->value => trans('admin::cruds.package_subscriptions.statuses.'.$case->value)]
                                                )->all(),
                                                'text'      => fn ($key, $value) => $value,
                                                'values'    => fn ($key, $value) => $key,
                                                'value'     => old('status', PackageSubscriptionStatus::PendingPayment->value),
                                            ],
                                        ])
                                    </div>
                                    <div class="col-lg-4 col-12 mb-10 form-group">
                                        @include('admin::components.inputs.select', [
                                            'options' => [
                                                'id'        => 'package_subscription_payment_status',
                                                'name'      => 'payment_status',
                                                'label'     => trans('admin::inputs.package_subscriptions_crud.payment_status.label'),
                                                'required'  => true,
                                                'data'      => collect(PackageSubscriptionPaymentStatus::cases())->mapWithKeys(
                                                    fn ($case) => [$case->value => trans('admin::cruds.package_subscriptions.payment_statuses.'.$case->value)]
                                                )->all(),
                                                'text'      => fn ($key, $value) => $value,
                                                'values'    => fn ($key, $value) => $key,
                                                'value'     => old('payment_status', PackageSubscriptionPaymentStatus::Pending->value),
                                            ],
                                        ])
                                    </div>
                                    <div class="col-lg-4 col-12 mb-10 form-group">
                                        @include('admin::components.inputs.select', [
                                            'options' => [
                                                'id'        => 'package_subscription_payment_method',
                                                'name'      => 'payment_method',
                                                'label'     => trans('admin::inputs.package_subscriptions_crud.payment_method.label'),
                                                'required'  => true,
                                                'data'      => collect(PackageSubscriptionPaymentMethod::cases())->mapWithKeys(
                                                    fn ($case) => [$case->value => trans('admin::cruds.package_subscriptions.payment_methods.'.$case->value)]
                                                )->all(),
                                                'text'      => fn ($key, $value) => $value,
                                                'values'    => fn ($key, $value) => $key,
                                                'value'     => old('payment_method', PackageSubscriptionPaymentMethod::BankTransfer->value),
                                            ],
                                        ])
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 mb-10 form-group">
                                        @include('admin::components.inputs.textarea', [
                                            'options' => [
                                                'name'      => 'admin_notes',
                                                'label'     => trans('admin::inputs.package_subscriptions_crud.admin_notes.label'),
                                                'required'  => false,
                                                'value'     => old('admin_notes'),
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
            const freeTierPackageIds = @json($freeTierPackageIds);
            const subscriptionStatusActive = @json(PackageSubscriptionStatus::Active->value);
            const subscriptionPaymentPaid = @json(PackageSubscriptionPaymentStatus::Paid->value);
            const subscriptionPaymentMethodOther = @json(PackageSubscriptionPaymentMethod::Other->value);
            const subscriptionStatusPendingPayment = @json(PackageSubscriptionStatus::PendingPayment->value);
            const subscriptionPaymentPending = @json(PackageSubscriptionPaymentStatus::Pending->value);
            const subscriptionPaymentMethodBankTransfer = @json(PackageSubscriptionPaymentMethod::BankTransfer->value);

            const previewUrlTemplate = @json($serviceProviderPreviewUrlTemplate);
            const $preview = $('#package_subscription_service_provider_preview');
            const $select = $('#package_subscription_user_id');
            const $packageSelect = $('#package_subscription_package_id');
            const $freeTierNotice = $('#package_subscription_free_tier_notice');
            const $subscriptionStatus = $('#package_subscription_status');
            const $subscriptionPaymentStatus = $('#package_subscription_payment_status');
            const $subscriptionPaymentMethod = $('#package_subscription_payment_method');

            function syncFreeTierSubscriptionFields() {
                const raw = $packageSelect.val();
                const packageId = raw ? parseInt(raw, 10) : NaN;
                const isFreeTier = !Number.isNaN(packageId) && freeTierPackageIds.indexOf(packageId) !== -1;

                if (isFreeTier) {
                    $freeTierNotice.removeClass('d-none');
                    $subscriptionStatus.val(subscriptionStatusActive).prop('disabled', true);
                    $subscriptionPaymentStatus.val(subscriptionPaymentPaid).prop('disabled', true);
                    $subscriptionPaymentMethod.val(subscriptionPaymentMethodOther).prop('disabled', true);
                } else {
                    $freeTierNotice.addClass('d-none');
                    $subscriptionStatus.prop('disabled', false);
                    $subscriptionPaymentStatus.prop('disabled', false);
                    $subscriptionPaymentMethod.prop('disabled', false);
                    if (!raw) {
                        $subscriptionStatus.val(subscriptionStatusPendingPayment);
                        $subscriptionPaymentStatus.val(subscriptionPaymentPending);
                        $subscriptionPaymentMethod.val(subscriptionPaymentMethodBankTransfer);
                    }
                }

                $subscriptionStatus.add($subscriptionPaymentStatus).add($subscriptionPaymentMethod).trigger('change');
            }

            function dashIfEmpty(v) {
                return (v !== null && v !== undefined && String(v).trim() !== '') ? String(v).trim() : '—';
            }

            function serviceProviderPreviewUrl(id) {
                return previewUrlTemplate.replace(/\/0(\/)?$/, '/' + id + '$1');
            }

            function loadServiceProviderPreview(userId) {
                if (!userId) {
                    $preview.addClass('d-none');
                    return;
                }

                $.ajax({
                    url: serviceProviderPreviewUrl(userId),
                    dataType: 'json',
                }).done(function (data) {
                    $preview.find('.package-subscription-provider-preview__avatar').attr('src', data.image_url || '');
                    $preview.find('.package-subscription-provider-preview__avatar').attr('alt', data.full_name || '');
                    $preview.find('.package-subscription-provider-preview__name').text(data.full_name || '—');
                    $preview.find('.package-subscription-provider-preview__email').text(data.email || '—');
                    $preview.find('.package-subscription-provider-preview__phone').text(dashIfEmpty(data.phone_number));
                    $preview.find('.package-subscription-provider-preview__central_phone').text(dashIfEmpty(data.central_phone));
                    $preview.find('.package-subscription-provider-preview__service').text(dashIfEmpty(data.service_name));
                    $preview.find('.package-subscription-provider-preview__city').text(dashIfEmpty(data.city_name));
                    $preview.removeClass('d-none');
                }).fail(function () {
                    $preview.addClass('d-none');
                });
            }

            $select.on('change', function () {
                loadServiceProviderPreview($(this).val());
            });

            if ($select.val()) {
                loadServiceProviderPreview($select.val());
            }

            $packageSelect.on('change select2:select', syncFreeTierSubscriptionFields);
            syncFreeTierSubscriptionFields();
            setTimeout(syncFreeTierSubscriptionFields, 300);
        });
    </script>
@endpush
