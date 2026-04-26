@extends('admin::layouts.master', [
    'title' => $isServiceProvider
        ? trans('admin::dashboard.aside_menu.user_management.service_providers')
        : trans('admin::dashboard.aside_menu.user_management.customers'),
])

@section('toolbar')
    @component('admin::includes.toolbar', [
            'options'               => [
                'title'             => $isServiceProvider
                    ? trans('admin::dashboard.aside_menu.user_management.service_providers')
                    : trans('admin::dashboard.aside_menu.user_management.customers'),
                'actions'           => [
                    'filter'        => true,
                    'search'        => true,
                ],
            ]
        ])

        @slot('filterContent')

            <div class="mb-5">
                @include('admin::components.inputs.select', [
                    'options'           => [
                        'name'          => 'status',
                        'label'         => trans('admin::datatable.base_columns.status'),
                        'placeholder'   => trans('admin::base.all_results'),
                        'clearable'     => true,
                        'data'          => $adminStatuses,
                        'text'          => function ($key, $value) { return $value; },
                        'values'        => function ($key, $value) { return $key; },
                    ]
                ])
            </div>

            @if($isServiceProvider)
                <div class="d-none">
                    <select name="approval" id="users-filter-approval" aria-hidden="true" tabindex="-1">
                        <option value="">@lang('admin::base.all_results')</option>
                        <option value="pending">{{ trans('admin::datatable.users.quick_filter_pending_approval_option') }}</option>
                    </select>
                </div>
            @endif

            @if($isServiceProvider)
                <div class="mb-5">
                    @include('admin::components.inputs.select', [
                        'options'           => [
                            'id'            => 'users_filter_service_id',
                            'name'          => 'service_id',
                            'label'         => trans('admin::datatable.users.columns.service_type'),
                            'placeholder'   => trans('admin::base.all_results'),
                            'clearable'     => true,
                            'isAjax'        => true,
                            'url'           => route('platform.services.ajaxList'),
                            'selected'      => [],
                            'autoSelectFirst' => false,
                        ],
                    ])
                </div>
                <div class="mb-5">
                    @include('admin::components.inputs.select', [
                        'options'           => [
                            'id'                      => 'users_filter_country_id',
                            'name'                    => 'filter_country_id',
                            'label'                   => trans('admin::inputs.user_crud.country_id.label'),
                            'placeholder'             => trans('admin::inputs.user_crud.country_id.placeholder'),
                            'clearable'               => false,
                            'isAjax'                  => true,
                            'url'                     => route('zms.countries.ajaxList'),
                            'selected'                => [],
                            'clearDependentsSelector' => '#users_filter_state_id,#users_filter_city_id',
                            'autoSelectFirst'         => true,
                        ],
                    ])
                </div>
                <div class="mb-5">
                    @include('admin::components.inputs.select', [
                        'options'           => [
                            'id'                      => 'users_filter_state_id',
                            'name'                    => 'filter_state_id',
                            'label'                   => trans('admin::inputs.user_crud.state_id.label'),
                            'placeholder'             => trans('admin::inputs.user_crud.state_id.placeholder'),
                            'clearable'               => false,
                            'isAjax'                  => true,
                            'url'                     => route('zms.states.ajaxList'),
                            'selected'                => [],
                            'parentSelect'            => '#users_filter_country_id',
                            'ajaxParentParam'         => 'country_id',
                            'clearDependentsSelector' => '#users_filter_city_id',
                            'autoSelectFirst'         => true,
                            'disabled'                => true,
                        ],
                    ])
                </div>
                <div class="mb-5">
                    @include('admin::components.inputs.select', [
                        'options'           => [
                            'id'              => 'users_filter_city_id',
                            'name'            => 'city_id',
                            'label'           => trans('admin::inputs.user_crud.city_id.label'),
                            'placeholder'     => trans('admin::base.all_results'),
                            'clearable'       => true,
                            'isAjax'          => true,
                            'url'             => route('zms.cities.ajaxList'),
                            'selected'        => [],
                            'parentSelect'    => '#users_filter_state_id',
                            'ajaxParentParam' => 'state_id',
                            'disabled'        => true,
                        ],
                    ])
                </div>
            @endif
        @endslot
    @endcomponent
@endsection

@section('content')
    <div id="kt_content_container" class="container-fluid">
        <div class="card shadow-sm ">

            <div class="card-header">
                <div class="card-title">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        @include('admin::components.datatables.header.title', [
                            'options'   => [
                                'role'  => $viewTrashPermission,
                                'title' => $isServiceProvider
                                    ? trans('admin::datatable.users.list_title_service_providers')
                                    : trans('admin::datatable.users.list_title_customers'),
                            ]
                        ])
                        @if($isServiceProvider && ($service_providers_pending_approval_count ?? 0) > 0)
                            <button
                                type="button"
                                class="btn btn-sm btn-light-danger fw-semibold"
                                id="users-filter-pending-approval"
                                title="{{ trans('admin::datatable.users.quick_filter_pending_approval_title') }}"
                            >
                                <i class="bi bi-person-check fs-6 me-1"></i>
                                {{ trans('admin::datatable.users.quick_filter_pending_approval') }}
                                <span class="badge badge-danger ms-2">{{ $service_providers_pending_approval_count }}</span>
                            </button>
                        @endif
                    </div>
                </div>

                <div class="card-toolbar flex-row-reverse">
                    @include('admin::components.datatables.header.toolbar', [
                        'options'               => [
                            'role'              => $createPermission,
                            'multiActions'      => $bulkActionDropdown,
                            'route'             => route('auth.users.create', ['userType' => $userType->value]),
                        ]
                    ])
                </div>
            </div>

            <div class="card-body  py-4">
                @php
                    $serviceProviderViewUrlTemplate = $isServiceProvider
                        ? route('auth.users.show', ['userType' => $userType->value, 'model' => 900000001])
                        : '';
                @endphp
                @component('admin::components.datatables.table', [
                        'options'           => [
                            'url'           => route('auth.users.datatable', ['userType' => $userType->value]),
                            'filter'        => true,
                        ],
                    ])
                    @slot('columns')
                        <th style="width: 30%">
                            @lang('admin::inputs.package_subscriptions_crud.'.strtolower(str_replace('-', '_', $userType->value)).'.label')
                        </th>
                        <th> @lang('admin::datatable.base_columns.phone_number') </th>
                        <th> @lang('admin::datatable.base_columns.central_phone') </th>
                        <th style="width: 8%"> @lang('admin::datatable.base_columns.status') </th>
                        <th> @lang('admin::datatable.admins.columns.joined_date') </th>
                        @if($isServiceProvider)
                            <th> @lang('admin::datatable.users.columns.service_type') </th>
                            <th> @lang('admin::datatable.users.columns.state') </th>
                            <th> @lang('admin::datatable.users.columns.city') </th>
                            <th> @lang('admin::datatable.users.columns.ranking_score') </th>
                        @endif
                    @endslot

                    <script>
                        @slot('jsColumns')
                            {
                                data: 'full_name',
                                name: 'full_name',
                                orderable: false,
                                searchable: false,
                                render: function (data, type, row, meta) {
                                    @if ($isServiceProvider)
                                        const providerViewUrl = @json($serviceProviderViewUrlTemplate).replace('900000001', String(row.id));
                                        const providerViewLinkAttrs = ' href="' + providerViewUrl + '" target="_blank" rel="noopener noreferrer"';
                                    @else
                                        const providerViewLinkAttrs = ' href="javascript:;"';
                                    @endif
                                    return `
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                <a` + providerViewLinkAttrs + `>
                                                    <div class="symbol-label">
                                                        <img src="${row.image_url}" alt="${row.full_name}" class="w-100" onerror="this.onerror=null; this.src='{{ asset('images/default/avatars/user.png') }}';" />
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="d-flex justify-content-start flex-column">
                                                <a` + providerViewLinkAttrs + ` class="text-dark fw-bolder text-hover-primary mb-1">${row.full_name}</a>
                                                <span class="text-muted">${row.email}</span>
                                            </div>
                                        </div>
                                    `;
                                }
                            },
                            {
                                data        : 'phone_number',
                                name        : 'phone_number',
                                orderable   : false,
                                searchable  : false,
                                render      : function (data, type, row, meta) {
                                    return isEmpty(data) ? "{{ DEFAULT_PHONE }}" :
                                    `
                                        <a href="tel:${data}" class="text-dark fw-bolder text-hover-primary">${data}</a>
                                    `;
                                }
                            },
                            {
                                data        : 'central_phone',
                                name        : 'central_phone',
                                orderable   : false,
                                searchable  : false,
                                render      : function (data, type, row, meta) {
                                    return isEmpty(data) ? '—' :
                                    `
                                        <a href="tel:${data}" class="text-dark fw-bolder text-hover-primary">${data}</a>
                                    `;
                                }
                            },
                            {
                                data : 'status_format',
                                name : 'status_format',
                                orderable: false,
                                searchable: false,
                                render: function (data, type, row, meta) {
                                    return `
                                        <span class="btn btn-sm btn-font-sm btn-label-${data.color} text-center w-100">${data.label}</span>
                                    `;
                                }
                            },
                            {
                                data : 'created_at_format',
                                name : 'created_at_format',
                                orderable: false,
                                searchable: false,
                                render: function (data, type, row, meta) {
                                    return `
                                        <span class="text-gray-600 fw-semibold">${data}</span>
                                    `;
                                }
                            },
                            @if($isServiceProvider)
                            {
                                data: 'service_name',
                                name: 'service_name',
                                orderable: false,
                                searchable: false,
                                render: function (data, type, row, meta) {
                                    return `<span class="text-gray-700 fw-semibold">${data ?? '—'}</span>`;
                                }
                            },
                            {
                                data: 'state_name',
                                name: 'state_name',
                                orderable: false,
                                searchable: false,
                                render: function (data, type, row, meta) {
                                    return `<span class="text-gray-700 fw-semibold">${data ?? '—'}</span>`;
                                }
                            },
                            {
                                data: 'city_name',
                                name: 'city_name',
                                orderable: false,
                                searchable: false,
                                render: function (data, type, row, meta) {
                                    return `<span class="text-gray-700 fw-semibold">${data ?? '—'}</span>`;
                                }
                            },
                            {
                                data: 'ranking_score_display',
                                name: 'ranking_score',
                                orderable: true,
                                searchable: false,
                                render: function (data, type, row, meta) {
                                    if (type === 'sort' || type === 'filter' || type === 'type' || type === 'export') {
                                        return data ?? '';
                                    }
                                    const v = data == null || data === '' ? '—' : data;
                                    return `<span class="text-gray-800 fw-bold font-monospace">${v}</span>`;
                                }
                            },
                            @endif
                        @endslot
                    </script>

                @endcomponent
            </div>
        </div>
    </div>
@endsection

@section('modal')
    @if($isServiceProvider)
        @include('auth::users.partials.service-provider-accept-approval-modal')
    @endif
@endsection

@if($isServiceProvider)
    @push('script')
        <script>
            $(function () {
                function bindServiceProvidersFilteredCount() {
                    const $table = $('#data-table');
                    const $value = $('#users-service-providers-filtered-count-value');
                    if (!$table.length || !$value.length || !$.fn.DataTable.isDataTable($table)) {
                        return;
                    }
                    const api = $table.DataTable();
                    const update = function () {
                        const n = api.page.info().recordsDisplay;
                        $value.text(typeof n === 'number' ? n.toLocaleString() : '—');
                    };
                    $table.off('draw.dt.usersSpFilteredCount').on('draw.dt.usersSpFilteredCount', update);
                    update();
                }

                bindServiceProvidersFilteredCount();

                $('#users-filter-pending-approval').on('click', function () {
                    const $select = $('#users-filter-approval');
                    if (!$select.length) {
                        return;
                    }

                    $select.val('pending').trigger('change');

                    if ($.fn.DataTable.isDataTable('#data-table')) {
                        $('#data-table').DataTable().ajax.reload();
                    }
                });

                const $acceptModal = $('#spAcceptServiceProviderModal');
                const $acceptInput = $('#sp_accept_central_phone');
                const $acceptError = $('[data-sp-accept-central-phone-error]');
                const spAcceptUrlDataKey = 'spAcceptUrl';
                const spAcceptErrorMsg = @json(trans('admin::cruds.users.central_phone_required_approval'));
                const spAcceptTarget = document.querySelector('#root-page');

                function spAcceptParseResponseJson(xhr) {
                    if (xhr.responseJSON) {
                        return xhr.responseJSON;
                    }
                    if (xhr.responseText) {
                        try {
                            return JSON.parse(xhr.responseText);
                        } catch (e) {
                            return null;
                        }
                    }
                    return null;
                }

                function spAcceptFirstErrorMessage(payload) {
                    if (! payload || ! payload.errors || typeof payload.errors !== 'object') {
                        return null;
                    }
                    const keys = Object.keys(payload.errors);
                    for (let i = 0; i < keys.length; i++) {
                        const v = payload.errors[keys[i]];
                        if (Array.isArray(v) && v.length) {
                            return v[0];
                        }
                        if (typeof v === 'string' && v) {
                            return v;
                        }
                    }
                    return null;
                }

                function spAcceptShowModalError(message) {
                    if (! message) {
                        return;
                    }
                    $acceptInput.addClass('is-invalid');
                    $acceptError.removeClass('d-none').addClass('d-block').text(message);
                    $acceptInput.trigger('focus');
                }

                function spAcceptClearModalError() {
                    $acceptInput.removeClass('is-invalid');
                    $acceptError.addClass('d-none').removeClass('d-block').text('');
                }

                function spAcceptReloadUsersDataTable() {
                    const $table = $('#data-table');
                    if ($table.length
                        && typeof $.fn.DataTable !== 'undefined'
                        && $.fn.DataTable.isDataTable($table[0])) {
                        $table.DataTable().ajax.reload(null, false);
                    }
                }

                if ($acceptModal.length) {
                    $(document).on('click', '.sp-service-provider-accept', function (e) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        const $a = $(this);
                        $acceptModal.data(spAcceptUrlDataKey, $a.attr('data-accept-url'));
                        const p = $a.attr('data-initial-prefill');
                        let prefill = null;
                        if (p) {
                            try {
                                prefill = JSON.parse(p);
                            } catch (err) {
                                prefill = null;
                            }
                        }
                        if (prefill == null) {
                            $acceptInput.val('');
                        } else {
                            $acceptInput.val(String(prefill));
                        }
                        spAcceptClearModalError();
                        bootstrap.Modal.getOrCreateInstance($acceptModal[0]).show();
                    });

                    $acceptInput.on('input', function () {
                        if (String($acceptInput.val() || '').trim()) {
                            spAcceptClearModalError();
                        }
                    });

                    $acceptModal.on('hidden.bs.modal', function () {
                        $acceptInput.val('').removeClass('is-invalid');
                        $acceptError.addClass('d-none').removeClass('d-block').text('');
                    });

                    $('#spAcceptServiceProviderConfirm').on('click', function () {
                        const postUrl = $acceptModal.data(spAcceptUrlDataKey);
                        if (!postUrl) {
                            return;
                        }
                        const v = String($acceptInput.val() || '').trim();
                        if (!v) {
                            spAcceptShowModalError(spAcceptErrorMsg);
                            return;
                        }
                        spAcceptClearModalError();
                        if (! spAcceptTarget) {
                            return;
                        }
                        const blockUI = new KTBlockUI(spAcceptTarget, spinnerOption);
                        const csrfToken = $('meta[name="csrf-token"]').attr('content');
                        const handleNotify = function (response) {
                            if (! response || ! response.message) {
                                return;
                            }
                            if (response.notify_type === 'toastr') {
                                GLOBAL.TOASTR.INIT(response.message.type, response.message.title, response.message.description);
                            } else {
                                GLOBAL.SWAL.INIT(response.message.type, response.message.title, response.message.description);
                            }
                        };
                        $.ajax({
                            type: 'POST',
                            url: postUrl,
                            dataType: 'json',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            data: {
                                _token: csrfToken,
                                central_phone: v,
                            },
                            beforeSend: function () {
                                blockUI.block();
                            },
                        })
                            .done(function (response) {
                                if (response && response.success) {
                                    spAcceptReloadUsersDataTable();
                                    const inst = bootstrap.Modal.getInstance($acceptModal[0]);
                                    if (inst) {
                                        inst.hide();
                                    }
                                }
                                handleNotify(response);
                            })
                            .fail(function (xhr) {
                                const j = spAcceptParseResponseJson(xhr);
                                if (! j) {
                                    GLOBAL.TOASTR.INIT('error');
                                    return;
                                }
                                const is422 = xhr.status === 422 || Number(j.code) === 422;
                                if (is422) {
                                    const fromFields = spAcceptFirstErrorMessage(j);
                                    if (fromFields) {
                                        spAcceptShowModalError(fromFields);
                                        return;
                                    }
                                    if (j.message && j.message.description) {
                                        spAcceptShowModalError(j.message.description);
                                        return;
                                    }
                                }
                                if (j.message && j.message.type && j.message.description) {
                                    handleNotify({ message: j.message, notify_type: j.notify_type || 'toastr' });
                                    return;
                                }
                                if (typeof j.message === 'string' && j.message) {
                                    GLOBAL.TOASTR.INIT('error', '', j.message);
                                    return;
                                }
                                GLOBAL.TOASTR.INIT('error');
                            })
                            .always(function () {
                                try {
                                    blockUI.release();
                                    blockUI.destroy();
                                } catch (err) {
                                }
                            });
                    });
                }
            });
        </script>
    @endpush
@endif
