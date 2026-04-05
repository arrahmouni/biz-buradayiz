@php
    $model->loadMissing(['user', 'verimorCallEvent']);
    $rating = max(0, min(5, (int) $model->rating));
@endphp

<div>

    <div class="row">
        <div class="col-12 mb-10 form-group">
            <div class="d-flex align-items-center gap-1 flex-wrap justify-content-center" role="img" aria-label="{{ trans('admin::datatable.reviews.columns.rating') }} {{ $rating }}/5">
                @for ($i = 1; $i <= 5; $i++)
                    <i class="bi {{ $i <= $rating ? 'bi-star-fill text-warning' : 'bi-star text-gray-400' }} fs-2x"></i>
                @endfor
                <span class="text-muted fs-6 ms-2">{{ $rating }}/5</span>
            </div>
        </div>
    </div>

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
                    'label' => trans('admin::datatable.base_columns.created_at'),
                    'value' => $model->created_at?->format('Y-m-d H:i:s') ?? '—',
                ],
            ])
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-10 form-group">
            @include('admin::components.inputs.text', [
                'options' => [
                    'disabled' => true,
                    'label' => trans('admin::inputs.package_subscriptions_crud.service_provider.label'),
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
                    'label' => trans('admin::datatable.reviews.columns.reviewer_display_name'),
                    'value' => $model->reviewer_display_name ?? '—',
                ],
            ])
        </div>
        <div class="col-lg-6 col-12 mb-10 form-group">
            @include('admin::components.inputs.text', [
                'options' => [
                    'disabled' => true,
                    'label' => trans('admin::datatable.reviews.columns.reviewer_phone'),
                    'value' => $model->reviewer_phone_normalized ?? '—',
                ],
            ])
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-10 form-group">
            @include('admin::components.inputs.textarea', [
                'options' => [
                    'disabled' => true,
                    'label' => trans('admin::datatable.reviews.columns.comment'),
                    'value' => $model->body ?? '',
                ],
            ])
        </div>
    </div>
{{-- 
    <div class="row">
        <div class="col-lg-6 col-12 mb-10 form-group">
            @include('admin::components.inputs.text', [
                'options' => [
                    'disabled' => true,
                    'label' => trans('admin::datatable.reviews.columns.call_event'),
                    'value' => $model->verimorCallEvent?->call_uuid ?? '—',
                ],
            ])
        </div>
        <div class="col-lg-6 col-12 mb-10 form-group d-flex align-items-end">
            <a href="{{ route('verimor.verimor_call_events.index') }}" class="btn btn-light-primary">
                @lang('admin::datatable.reviews.columns.verimor_call_events_list')
            </a>
        </div>
    </div> --}}
</div>
