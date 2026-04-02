@php
    $model = $model ?? null;

    $serviceSelected = [];
    if ($model && $model->service_id) {
        $serviceSelected = [['id' => $model->service_id, 'text' => $model->service?->name ?? '']];
    }

    $countrySelected = [];
    if ($model && $model->city?->state?->country) {
        $country = $model->city->state->country;
        $countrySelected = [['id' => $country->id, 'text' => $country->name ?? $country->native_name]];
    }

    $stateSelected = [];
    if ($model && $model->city?->state) {
        $state = $model->city->state;
        $stateSelected = [['id' => $state->id, 'text' => $state->name ?? $state->native_name]];
    }

    $citySelected = [];
    if ($model && $model->city_id) {
        $citySelected = [['id' => $model->city_id, 'text' => $model->city?->name ?? $model->city?->native_name ?? '']];
    }
@endphp

<div class="row">
    <div class="col-lg-6 col-12 mb-10 form-group">
        @include('admin::components.inputs.select', [
            'options' => [
                'id'            => 'user_sp_service_id',
                'name'          => 'service_id',
                'label'         => trans('admin::inputs.user_crud.service_id.label'),
                'placeholder'   => trans('admin::inputs.user_crud.service_id.placeholder'),
                'help'          => trans('admin::inputs.user_crud.service_id.help'),
                'required'      => true,
                'isAjax'        => true,
                'url'             => route('platform.services.ajaxList'),
                'selected'        => $serviceSelected,
                'autoSelectFirst' => empty($serviceSelected),
            ],
        ])
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-12 mb-10 form-group">
        @include('admin::components.inputs.select', [
            'options' => [
                'id'                      => 'user_sp_country_id',
                'name'                    => 'country_id',
                'label'                   => trans('admin::inputs.user_crud.country_id.label'),
                'placeholder'             => trans('admin::inputs.user_crud.country_id.placeholder'),
                'help'                    => trans('admin::inputs.user_crud.country_id.help'),
                'required'                => true,
                'isAjax'                  => true,
                'url'                     => route('zms.countries.ajaxList'),
                'selected'                => $countrySelected,
                'clearDependentsSelector' => '#user_sp_state_id,#user_sp_city_id',
                'autoSelectFirst'         => empty($countrySelected),
            ],
        ])
    </div>

    <div class="col-lg-6 col-12 mb-10 form-group">
        @include('admin::components.inputs.select', [
            'options' => [
                'id'                      => 'user_sp_state_id',
                'name'                    => 'state_id',
                'label'                   => trans('admin::inputs.user_crud.state_id.label'),
                'placeholder'             => trans('admin::inputs.user_crud.state_id.placeholder'),
                'help'                    => trans('admin::inputs.user_crud.state_id.help'),
                'required'                => true,
                'isAjax'                  => true,
                'url'                     => route('zms.states.ajaxList'),
                'selected'                => $stateSelected,
                'parentSelect'            => '#user_sp_country_id',
                'ajaxParentParam'         => 'country_id',
                'clearDependentsSelector' => '#user_sp_city_id',
                'autoSelectFirst'         => empty($countrySelected),
            ],
        ])
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-12 mb-10 form-group">
        @include('admin::components.inputs.select', [
            'options' => [
                'id'              => 'user_sp_city_id',
                'name'            => 'city_id',
                'label'           => trans('admin::inputs.user_crud.city_id.label'),
                'placeholder'     => trans('admin::inputs.user_crud.city_id.placeholder'),
                'help'            => trans('admin::inputs.user_crud.city_id.help'),
                'required'        => true,
                'isAjax'          => true,
                'url'             => route('zms.cities.ajaxList'),
                'selected'        => $citySelected,
                'parentSelect'    => '#user_sp_state_id',
                'ajaxParentParam' => 'state_id',
                'disabled'        => empty($stateSelected),
            ],
        ])
    </div>
</div>
