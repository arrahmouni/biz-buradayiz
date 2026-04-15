@php
    use Modules\Platform\Enums\PackageSubscriptionPaymentStatus;
    use Modules\Platform\Enums\PackageSubscriptionStatus;
    use Modules\Platform\Enums\permissions\PackageSubscriptionPermissions;

    $canUpdate = app('owner') || app('admin')->can(PackageSubscriptionPermissions::UPDATE);
    $model->loadMissing('snapshot');
    $isFreeTierSubscription = $model->isFreeTierCatalogSubscription();
    $canUpdateSubscriptionStatuses = $canUpdate && ! $isFreeTierSubscription;
@endphp

@extends('admin::layouts.master', ['title' => trans('admin::cruds.package_subscriptions.view')])

@section('toolbar')
    @include('admin::includes.toolbar', [
        'options'               => [
            'title'             => trans('admin::dashboard.aside_menu.platform_management.package_subscriptions'),
            'backUrl'           => route('platform.package_subscriptions.index'),
            'actions'           => [
                'save'          => false,
                'back'          => true,
            ],
        ]
    ])
@endsection

@section('content')
    <div id="kt_content_container" class="container-fluid">
        <div class="d-flex flex-column gap-7 gap-lg-10">
            <div class="d-flex flex-wrap flex-stack gap-5 gap-lg-10">
                <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-bold mb-lg-n2 me-auto">
                    <li class="nav-item">
                        <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#kt_package_subscription_summary">
                            @lang('admin::cruds.package_subscriptions.view_page.tab_summary')
                        </a>
                    </li>
                </ul>
            </div>

            <div class="d-flex flex-column flex-xl-row gap-7 gap-lg-10">
                <div class="card card-flush py-4 flex-row-fluid">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>
                                @lang('admin::cruds.package_subscriptions.view_page.subscription_details')
                                — @lang('admin::cruds.package_subscriptions.view_page.subscription_number', ['id' => $model->id])
                            </h2>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-bordered mb-0 fs-6 gy-5 min-w-300px">
                                <tbody class="fw-bold text-gray-600">
                                    <tr>
                                        <td class="text-muted">
                                            <div class="d-flex align-items-center">
                                                <span class="svg-icon svg-icon-2 me-2">
                                                    {!! config('admin.svgs.calendar') !!}
                                                </span>
                                                @lang('admin::cruds.package_subscriptions.view_page.created_at')
                                            </div>
                                        </td>
                                        <td class="fw-bolder text-end">
                                            {{ $model->created_at?->format('Y-m-d H:i') ?? '—' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">
                                            <div class="d-flex align-items-center">
                                                <span class="svg-icon svg-icon-2 me-2">
                                                    {!! config('admin.svgs.payment_method') !!}
                                                </span>
                                                @lang('admin::cruds.package_subscriptions.view_page.payment_method')
                                            </div>
                                        </td>
                                        <td class="fw-bolder text-end">
                                            <div class="d-flex align-items-center justify-content-end">
                                                <span>{{ $model->payment_method_format['label'] }}</span>
                                                <img src="{{ $model->payment_method_format['img'] }}" class="w-50px ms-2" alt="{{ $model->payment_method_format['label'] }}" />
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card card-flush py-4 flex-row-fluid">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>
                                @lang('admin::cruds.package_subscriptions.view_page.customer_details')
                            </h2>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-bordered mb-0 fs-6 gy-5 min-w-300px">
                                <tbody class="fw-bold text-gray-600">
                                    <tr>
                                        <td class="text-muted">
                                            <div class="d-flex align-items-center">
                                                <span class="svg-icon svg-icon-2 me-2">
                                                    {!! config('admin.svgs.customer') !!}
                                                </span>
                                                @lang('admin::datatable.package_subscriptions.columns.user')
                                            </div>
                                        </td>
                                        <td class="fw-bolder text-end">
                                            {{ $model->user ? trim($model->user->full_name) : '—' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">
                                            <div class="d-flex align-items-center">
                                                <span class="svg-icon svg-icon-2 me-2">
                                                    {!! config('admin.svgs.email_order') !!}
                                                </span>
                                                @lang('admin::inputs.base_crud.email.label')
                                            </div>
                                        </td>
                                        <td class="fw-bolder text-end">
                                            {{ $model->user?->email ?? '—' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">
                                            <div class="d-flex align-items-center">
                                                <span class="svg-icon svg-icon-2 me-2">
                                                    {!! config('admin.svgs.phone') !!}
                                                </span>
                                                @lang('admin::datatable.base_columns.phone_number')
                                            </div>
                                        </td>
                                        <td class="fw-bolder text-end">
                                            {{ $model->user?->phone_number ?? '—' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">
                                            <div class="d-flex align-items-center">
                                                <span class="svg-icon svg-icon-2 me-2">
                                                    {!! config('admin.svgs.phone') !!}
                                                </span>
                                                @lang('admin::datatable.base_columns.central_phone')
                                            </div>
                                        </td>
                                        <td class="fw-bolder text-end">
                                            {{ $model->user?->central_phone ?? '—' }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="kt_package_subscription_summary" role="tab-panel">
                    <div class="d-flex flex-column gap-7 gap-lg-10">
                        <div class="card card-flush py-4 flex-row-fluid overflow-hidden">
                            <div class="position-absolute top-0 end-0 opacity-10 pe-none text-end">
                                <img src="{{ asset('modules/admin/metronic/demo/media/icons/duotune/ecommerce/ecm001.svg') }}" class="w-175px" alt="" />
                            </div>
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>@lang('admin::cruds.package_subscriptions.view_page.package_and_status')</h2>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                @if ($isFreeTierSubscription)
                                    <p class="text-muted fs-7 mb-5">
                                        @lang('admin::cruds.package_subscriptions.view_page.free_tier_not_editable')
                                    </p>
                                @endif
                                <div class="table-responsive">
                                    <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0">
                                        <thead>
                                            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                                <th class="min-w-200px">@lang('admin::cruds.package_subscriptions.view_page.package')</th>
                                                <th class="min-w-125px text-end">@lang('admin::cruds.package_subscriptions.view_page.ordered_price')</th>
                                                <th class="min-w-125px text-end">@lang('admin::cruds.package_subscriptions.view_page.remaining_connections')</th>
                                                <th class="min-w-125px text-end">@lang('admin::cruds.package_subscriptions.view_page.subscription_status')</th>
                                                <th class="min-w-125px text-end">@lang('admin::cruds.package_subscriptions.view_page.payment_status')</th>
                                                @if ($canUpdateSubscriptionStatuses)
                                                    <th class="min-w-100px text-end">@lang('admin::datatable.base_columns.actions')</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody class="fw-bold text-gray-600">
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="symbol symbol-50px me-5">
                                                            <span class="symbol-label bg-light d-flex align-items-center justify-content-center">
                                                                <img src="{{ asset('modules/admin/metronic/demo/media/icons/duotune/ecommerce/ecm001.svg') }}" class="h-35px w-35px" alt="" />
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <span class="fw-bolder text-gray-800">{{ $model->snapshot?->smartTransName() ?? '—' }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-end">{{ $model->snapshot?->priceDisplay() ?? '—' }}</td>
                                                <td class="text-end">{{ $model->remaining_connections ?? '—' }}</td>
                                                <td class="text-end">
                                                    <span class="btn btn-sm btn-font-sm btn-label-{{ $model->status->datatableBadgeColor() }} text-center">
                                                        {{ $model->status_label }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <span class="btn btn-sm btn-font-sm btn-label-{{ $model->payment_status->datatableBadgeColor() }} text-center">
                                                        {{ $model->payment_status_label }}
                                                    </span>
                                                </td>
                                                @if ($canUpdateSubscriptionStatuses)
                                                    <td class="text-end">
                                                        <div class="btn btn-light btn-active-light-primary btn-sm datatable-action-menu">
                                                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#change-subscription-statuses-{{ $model->id }}" class="p-2">
                                                                <i class="bi bi-pencil-square fs-4"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                @endif
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card card-flush py-4 flex-row-fluid">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>@lang('admin::cruds.package_subscriptions.view_page.timeline')</h2>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="table-responsive">
                                    <table class="table align-middle table-row-bordered mb-0 fs-6 gy-5">
                                        <tbody class="fw-bold text-gray-600">
                                            <tr>
                                                <td class="text-muted">@lang('admin::cruds.package_subscriptions.view_page.starts_at')</td>
                                                <td class="fw-bolder text-end">{{ $model->starts_at?->format('Y-m-d H:i') ?? '—' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">@lang('admin::cruds.package_subscriptions.view_page.ends_at')</td>
                                                <td class="fw-bolder text-end">{{ $model->ends_at?->format('Y-m-d H:i') ?? '—' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">@lang('admin::cruds.package_subscriptions.view_page.cancelled_at')</td>
                                                <td class="fw-bolder text-end">{{ $model->cancelled_at?->format('Y-m-d H:i') ?? '—' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">@lang('admin::cruds.package_subscriptions.view_page.paid_at')</td>
                                                <td class="fw-bolder text-end">{{ $model->paid_at?->format('Y-m-d H:i') ?? '—' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">@lang('admin::cruds.package_subscriptions.view_page.admin_notes')</td>
                                                <td class="fw-bolder text-end text-wrap">{{ $model->admin_notes !== null && $model->admin_notes !== '' ? $model->admin_notes : '—' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($canUpdateSubscriptionStatuses)
            @component('admin::components.modals.modal', [
                'options' => [
                    'id' => 'change-subscription-statuses-'.$model->id,
                ],
            ])
                @slot('body')
                    @component('admin::components.forms.form', [
                        'options' => [
                            'method' => 'PUT',
                            'checkForEmptyCheckbox' => true,
                            'action' => route('platform.package_subscriptions.postUpdate', ['model' => $model->id]),
                        ],
                    ])
                        @slot('fields')
                            <div class="mb-10 text-center">
                                <h2 class="mb-3">@lang('admin::cruds.package_subscriptions.view_page.change_statuses')</h2>
                            </div>
                            <div class="row g-9 mb-8">
                                <div class="col-md-6 fv-row">
                                    @include('admin::components.inputs.select', [
                                        'options' => [
                                            'name' => 'status',
                                            'required' => true,
                                            'label' => trans('admin::inputs.package_subscriptions_crud.status.label'),
                                            'placeholder' => trans('admin::inputs.base_crud.status.placeholder'),
                                            'data' => PackageSubscriptionStatus::adminFilterSelectOptions(),
                                            'text' => fn ($key, $value) => $value,
                                            'values' => fn ($key, $value) => $key,
                                            'value' => $model->status->value,
                                            'select' => fn ($key, $value) => $key === $model->status->value,
                                        ],
                                    ])
                                </div>
                                <div class="col-md-6 fv-row">
                                    @include('admin::components.inputs.select', [
                                        'options' => [
                                            'name' => 'payment_status',
                                            'required' => true,
                                            'label' => trans('admin::inputs.package_subscriptions_crud.payment_status.label'),
                                            'placeholder' => trans('admin::inputs.base_crud.status.placeholder'),
                                            'data' => PackageSubscriptionPaymentStatus::adminFilterSelectOptions(),
                                            'text' => fn ($key, $value) => $value,
                                            'values' => fn ($key, $value) => $key,
                                            'value' => $model->payment_status->value,
                                            'select' => fn ($key, $value) => $key === $model->payment_status->value,
                                        ],
                                    ])
                                </div>
                            </div>
                            {{-- <div class="row g-9 mb-8">
                                <div class="col-12 fv-row">
                                    @include('admin::components.inputs.checkbox', [
                                        'options' => [
                                            'name' => 'notify_user',
                                            'label' => trans('admin::cruds.package_subscriptions.view_page.notify_user'),
                                            'checked' => true,
                                            'value' => 1,
                                        ],
                                    ])
                                </div>
                            </div> --}}
                            <div class="text-center">
                                <button type="reset" data-bs-dismiss="modal" class="btn btn-light me-3">
                                    @lang('admin::confirmations.cancel')
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <span class="indicator-label">@lang('admin::base.save')</span>
                                    <span class="indicator-progress">
                                        @lang('admin::base.please_wait_dot')
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                            </div>
                        @endslot
                    @endcomponent
                @endslot
            @endcomponent
        @endif
    </div>
@endsection
