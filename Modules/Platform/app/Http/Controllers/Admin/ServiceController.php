<?php

namespace Modules\Platform\Http\Controllers\Admin;

use Modules\Base\Http\Controllers\BaseCrudController;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;
use Modules\Platform\Models\Service;
use Modules\Platform\Http\Services\ServiceService;
use Modules\Platform\Enums\permissions\ServicePermissions;
use Modules\Platform\Http\Requests\ServiceRequest;

class ServiceController extends BaseCrudController
{
    protected $model;

    protected $crudService;

    protected $module           = 'platform';

    protected $routePrefix      = 'platform.services';

    protected $routeParameters  = [];

    protected $createRequest    = ServiceRequest::class;

    protected $updateRequest    = ServiceRequest::class;

    protected static $permissionClass  = ServicePermissions::class;

    protected static $hasPermission    = true;

    protected $hasSoftDelete    = true;

    protected $hasDisabled      = true;

    protected $hasBulkActions   = true;

    public function __construct(Service $model, ServiceService $crudService)
    {
        app('adminHelper')->addBreadcrumbs(trans('admin::dashboard.aside_menu.service_management.services'), route($this->routePrefix . '.index'));

        $this->model        = $model;
        $this->crudService  = $crudService;

        parent::__construct();
    }

    public function canDelete($model)
    {
        if ($this->checkIfServiceProviderHasActiveSubscription($model->id)) {
            return sendFailInternalResponse('service_cannot_be_deleted_with_active_subscriptions');
        }

        return sendSuccessInternalResponse();
    }

    public function canDisable($model)
    {
        if ($this->checkIfServiceProviderHasActiveSubscription($model->id)) {
            return sendFailInternalResponse('service_cannot_be_disabled_with_active_subscriptions');
        }

        return sendSuccessInternalResponse();
    }


    public function checkIfServiceProviderHasActiveSubscription($serviceId)
    {
        return User::query()
            ->where('type', UserType::ServiceProvider->value)
            ->where('service_id', $serviceId)
            ->whereHas('activePackageSubscription')
            ->exists();
    }

}
