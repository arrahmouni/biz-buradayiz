<?php

namespace Modules\Auth\Http\Controllers\admin;

use Exception;
use Illuminate\Http\Request;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;
use Modules\Auth\Http\Requests\UserCrudRequest;
use Modules\Auth\Http\Services\UserCrudService;
use Modules\Auth\Enums\permissions\UserPermissions;
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
                    route($this->routePrefix . '.index', $this->routeParameters)
                );
            }
        }

        $this->model = $model;
        $this->crudService = $crudService;

        parent::__construct();
    }

    public function update(Request $request)
    {
        app('adminHelper')->addBreadcrumbs(trans('admin::dashboard.breadcrumbs.edit'));

        $this->data['model'] = $this->crudService->getModel(
            id: $request->model,
            withTrashed: $this->hasSoftDelete,
            withDisabled: $this->hasDisabled
        );

        if ($this->userType === UserType::ServiceProvider) {
            $this->data['model']->loadMissing([
                'service.translations',
                'city.translations',
                'city.state.translations',
                'city.state.country.translations',
            ]);
        }

        return view($this->module . '::' . $this->model::VIEW_PATH . '.update', $this->data);
    }

    public function datatable(Request $request)
    {
        if ($this->userType !== null) {
            $request->merge(['userType' => $this->userType->value]);
        }

        return $this->crudService->getDataTable($request->all());
    }

    public function postUpdate(Request $request)
    {
        app($this->updateRequest);

        $this->data['model'] = $this->crudService->getModel(
            id: $request->model,
            withTrashed: $this->hasSoftDelete,
            withDisabled: $this->hasDisabled
        );

        if ($this->userType !== null) {
            $modelType = $this->data['model']->type;
            $modelTypeValue = $modelType instanceof UserType ? $modelType->value : $modelType;

            if ($modelTypeValue !== $this->userType->value) {
                abort(404);
            }
        }

        try {
            $this->crudService->updateModel(
                $this->data['model'],
                app($this->updateRequest)->validated()
            );
        } catch (Exception $e) {
            return sendExceptionResponse($e);
        }

        return sendSuccessResponse(route($this->routePrefix . '.index', $this->routeParameters));
    }
}
