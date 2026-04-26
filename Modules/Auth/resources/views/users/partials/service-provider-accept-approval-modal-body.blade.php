<div class="mb-8 text-center">
    <h2 class="mb-3 fs-2">{{ trans('admin::cruds.users.accept_approval_modal.title') }}</h2>
    <p class="text-gray-600 mb-0">{{ trans('admin::cruds.users.accept_approval_modal.description') }}</p>
</div>
<div class="form-group">
    <div class="fv-row">
        @include('admin::components.inputs.text', [
            'options' => [
                'id'                => 'sp_accept_central_phone',
                'name'              => 'central_phone',
                'label'             => trans('admin::inputs.user_crud.central_phone.label'),
                'placeholder'       => trans('admin::inputs.user_crud.central_phone.placeholder'),
                'help'              => trans('admin::inputs.user_crud.central_phone.help'),
                'required'          => true,
                'onlyPlusDigits'    => true,
            ],
        ])
    </div>
    <div class="invalid-feedback d-none text-start" data-sp-accept-central-phone-error role="alert"></div>
</div>
<div class="d-flex flex-wrap justify-content-end gap-3 pt-8">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
        {{ trans('admin::confirmations.cancel') }}
    </button>
    <button type="button" class="btn btn-success" id="spAcceptServiceProviderConfirm">
        {{ trans('admin::cruds.users.accept_approval_modal.confirm') }}
    </button>
</div>
