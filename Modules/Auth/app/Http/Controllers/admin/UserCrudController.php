<?php

namespace Modules\Auth\Http\Controllers\admin;

use Illuminate\Http\Request;
use Modules\Auth\Enums\permissions\UserPermissions;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Http\Requests\UserCrudRequest;
use Modules\Auth\Http\Services\UserCrudService;
use Modules\Auth\Models\User;
use Modules\Base\Http\Controllers\BaseCrudController;

class UserCrudController extends BaseCrudController
{
    protected $module = 'auth';

    protected $model;

    protected $crudService;

    protected static $permissionClass = UserPermissions::class;

    protected $routePrefix = 'auth.users';

    protected $createRequest = UserCrudRequest::class;

    protected $updateRequest = UserCrudRequest::class;

    protected static $hasPermission = true;

    protected $hasSoftDelete = true;

    protected $hasDisabled = false;

    protected $hasBulkActions = false;

    protected ?UserType $userType = null;

    public function __construct(User $model, UserCrudService $crudService)
    {
        if (! app()->runningInConsole()) {
            $userTypeParam = request()->route('userType');

            if ($userTypeParam !== null) {
                $this->userType = UserType::tryFrom($userTypeParam) ?? abort(404);
                $this->routeParameters = ['userType' => $this->userType->value];
                $this->data['userType'] = $this->userType;
                $this->data['isServiceProvider'] = $this->userType === UserType::ServiceProvider;

                $breadcrumbTitle = match ($this->userType) {
                    UserType::Customer => trans('admin::dashboard.aside_menu.user_management.customers'),
                    UserType::ServiceProvider => trans('admin::dashboard.aside_menu.user_management.service_providers'),
                };

                app('adminHelper')->addBreadcrumbs(
                    $breadcrumbTitle,
                    route($this->routePrefix.'.index', $this->routeParameters)
                );
            }
        }

        $this->model = $model;
        $this->crudService = $crudService;

        parent::__construct();
    }

    public function getModelForAjax(Request $request)
    {
        $query = parent::getModelForAjax($request);

        $userType = $this->resolveAjaxListUserType($request);

        if ($userType !== null) {
            $query->where('type', $userType->value);
        }

        return $query;
    }

    protected function resolveAjaxListUserType(Request $request): ?UserType
    {
        if ($this->userType !== null) {
            return $this->userType;
        }

        if (! $request->filled('userType')) {
            return null;
        }

        return UserType::tryFrom((string) $request->input('userType')) ?? abort(404);
    }
}
