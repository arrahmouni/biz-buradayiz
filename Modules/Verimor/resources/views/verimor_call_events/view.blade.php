<div>
    <div class="row">
        <div class="col-lg-6 col-12 mb-10 form-group">
            @include('admin::components.inputs.text', [
                'options' => [
                    'disabled' => true,
                    'label' => trans('admin::datatable.base_columns.id'),
                    'value' => $model->id,
                ],
            ])
        </div>
        <div class="col-lg-6 col-12 mb-10 form-group">
            @include('admin::components.inputs.text', [
                'options' => [
                    'disabled' => true,
                    'label' => trans('admin::datatable.verimor_call_events.columns.call_uuid'),
                    'value' => $model->call_uuid,
                ],
            ])
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-12 mb-10 form-group">
            @include('admin::components.inputs.text', [
                'options' => [
                    'disabled' => true,
                    'label' => trans('admin::datatable.verimor_call_events.columns.event_type'),
                    'value' => $model->event_type?->value ?? '—',
                ],
            ])
        </div>
        <div class="col-lg-6 col-12 mb-10 form-group">
            @include('admin::components.inputs.text', [
                'options' => [
                    'disabled' => true,
                    'label' => trans('admin::datatable.verimor_call_events.columns.direction'),
                    'value' => $model->direction?->value ?? '—',
                ],
            ])
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-12 mb-10 form-group">
            @include('admin::components.inputs.text', [
                'options' => [
                    'disabled' => true,
                    'label' => trans('admin::datatable.verimor_call_events.columns.destination'),
                    'value' => $model->destination_number_normalized ?? '—',
                ],
            ])
        </div>
        <div class="col-lg-6 col-12 mb-10 form-group">
            @include('admin::components.inputs.text', [
                'options' => [
                    'disabled' => true,
                    'label' => trans('admin::datatable.verimor_call_events.columns.subscription_id'),
                    'value' => $model->package_subscription_id ?? '—',
                ],
            ])
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-12 mb-10 form-group">
            @include('admin::components.inputs.text', [
                'options' => [
                    'disabled' => true,
                    'label' => trans('admin::datatable.verimor_call_events.columns.answered'),
                    'value' => $model->answered ? trans('verimor::strings.yes') : trans('verimor::strings.no'),
                ],
            ])
        </div>
        <div class="col-lg-6 col-12 mb-10 form-group">
            @include('admin::components.inputs.text', [
                'options' => [
                    'disabled' => true,
                    'label' => trans('admin::datatable.verimor_call_events.columns.consumed_quota'),
                    'value' => $model->consumed_quota ? trans('verimor::strings.yes') : trans('verimor::strings.no'),
                ],
            ])
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-10 form-group">
            @include('admin::components.inputs.text', [
                'options' => [
                    'disabled' => true,
                    'label' => trans('admin::datatable.verimor_call_events.columns.provider'),
                    'value' => $model->user
                        ? $model->user->full_name.' ('.$model->user->email.')'
                        : '—',
                ],
            ])
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-12 mb-10 form-group">
            @include('admin::components.inputs.text', [
                'options' => [
                    'disabled' => true,
                    'label' => trans('admin::datatable.base_columns.created_at'),
                    'value' => $model->created_at?->format('Y-m-d H:i:s') ?? '—',
                ],
            ])
        </div>
        <div class="col-lg-6 col-12 mb-10 form-group">
            @include('admin::components.inputs.text', [
                'options' => [
                    'disabled' => true,
                    'label' => trans('admin::datatable.base_columns.updated_at'),
                    'value' => $model->updated_at?->format('Y-m-d H:i:s') ?? '—',
                ],
            ])
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-10 form-group">
            @include('admin::components.inputs.textarea', [
                'options' => [
                    'label' => trans('admin::datatable.verimor_call_events.columns.raw_payload'),
                    'disabled' => true,
                    'value' => json_encode($model->raw_payload ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                    'rows' => 16,
                ],
            ])
        </div>
    </div>
</div>
