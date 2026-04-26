<?php

namespace Modules\Auth\Http\Controllers\admin;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use InvalidArgumentException;
use Modules\Admin\Enums\AdminStatus;
use Modules\Auth\Enums\permissions\UserPermissions;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Http\Requests\AcceptServiceProviderRequest;
use Modules\Auth\Http\Requests\UserCrudRequest;
use Modules\Auth\Http\Services\UserCrudService;
use Modules\Auth\Models\User;
use Modules\Base\Http\Controllers\BaseCrudController;
use Modules\Platform\Enums\permissions\PackageSubscriptionPermissions;
use Modules\Platform\Http\Services\PackageSubscriptionService;
use Modules\Verimor\Enums\permissions\VerimorCallEventPermissions;
use Modules\Verimor\Http\Services\VerimorCallEventService;

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

        $this->model = $model;
        $this->crudService = $crudService;

        parent::__construct();
    }

    public static function middleware(): array
    {
        $parent = parent::middleware();

        foreach ($parent as $i => $mw) {
            if ($mw instanceof Middleware && $mw->middleware === 'need.permissions:'.static::$permissionClass::READ) {
                $parent[$i] = new Middleware(
                    $mw->middleware,
                    array_values(array_unique(array_merge($mw->only ?? [], [
                        'providerSubscriptionsDatatable',
                        'providerCallEventsDatatable',
                    ])))
                );
            }
        }

        $parent[] = new Middleware(
            'need.permissions:'.UserPermissions::UPDATE,
            only: ['acceptServiceProvider'],
        );

        return $parent;
    }

    public function acceptServiceProvider(AcceptServiceProviderRequest $request)
    {
        if ($this->userType !== UserType::ServiceProvider) {
            abort(404);
        }

        $model = $this->crudService->getModel(
            id: (int) $request->route('model'),
            withTrashed: $this->hasSoftDelete,
            withDisabled: $this->hasDisabled
        );

        $modelTypeValue = $model->type instanceof UserType
            ? $model->type->value
            : (string) $model->type;

        if ($modelTypeValue !== $this->userType->value) {
            abort(404);
        }

        try {
            $this->crudService->acceptPendingServiceProvider(
                $model,
                (string) $request->validated('central_phone')
            );
        } catch (InvalidArgumentException $e) {
            return sendFailResponse(customMessage: $e->getMessage());
        } catch (Exception $e) {
            return sendExceptionResponse($e);
        }

        return sendSuccessResponse();
    }

    public function index()
    {
        if ($this->userType === UserType::ServiceProvider) {
            $this->data['service_providers_pending_approval_count'] = User::query()
                ->where('type', UserType::ServiceProvider->value)
                ->where('status', AdminStatus::PENDING)
                ->whereNull('approved_at')
                ->count();
        }

        return parent::index();
    }

    public function show(Request $request)
    {
        if ($this->userType === null) {
            abort(404);
        }

        app('adminHelper')->addBreadcrumbs(trans('admin::dashboard.breadcrumbs.view'));

        $model = $this->crudService->getModel(
            id: (int) $request->route('model'),
            withTrashed: $this->hasSoftDelete,
            withDisabled: $this->hasDisabled
        );

        $modelTypeValue = $model->type instanceof UserType
            ? $model->type->value
            : (string) $model->type;

        if ($modelTypeValue !== $this->userType->value) {
            abort(404);
        }

        if ($this->userType === UserType::ServiceProvider) {
            $model->load([
                'media',
                'service.translations',
                'city.translations',
                'city.state.translations',
                'city.state.country.translations',
                'addresses',
                'currentPackageSubscription.snapshot',
            ]);

            $this->data['canViewProviderSubscriptionHistory'] = app('owner')
                || app('admin')->can(PackageSubscriptionPermissions::READ);
            $this->data['canViewProviderCallLog'] = app('owner')
                || app('admin')->can(VerimorCallEventPermissions::READ);
        } else {
            $model->load(['media']);
        }

        $this->data['model'] = $model;

        return view($this->module.'::'.$this->model::VIEW_PATH.'.view', $this->data);
    }

    public function providerSubscriptionsDatatable(Request $request): JsonResponse
    {
        $this->authorizePackageSubscriptionRead();

        $user = $this->resolveServiceProviderForViewDatatable($request);
        $user->loadMissing('currentPackageSubscription');

        $data = $request->all();
        $data['scoped_user_id'] = $user->id;
        $data['exclude_subscription_id'] = $user->currentPackageSubscription?->id;

        return app(PackageSubscriptionService::class)->getDataTable($data);
    }

    public function providerCallEventsDatatable(Request $request): JsonResponse
    {
        $this->authorizeVerimorCallEventRead();

        $user = $this->resolveServiceProviderForViewDatatable($request);

        $data = $request->all();
        $data['scoped_user_id'] = $user->id;

        return app(VerimorCallEventService::class)->getDataTable($data);
    }

    protected function resolveServiceProviderForViewDatatable(Request $request): User
    {
        if ($this->userType !== UserType::ServiceProvider) {
            abort(404);
        }

        $model = $this->crudService->getModel(
            id: (int) $request->route('model'),
            withTrashed: $this->hasSoftDelete,
            withDisabled: $this->hasDisabled
        );

        $modelTypeValue = $model->type instanceof UserType
            ? $model->type->value
            : (string) $model->type;

        if ($modelTypeValue !== $this->userType->value) {
            abort(404);
        }

        return $model;
    }

    protected function authorizePackageSubscriptionRead(): void
    {
        if (! app('owner') && ! app('admin')->can(PackageSubscriptionPermissions::READ)) {
            abort(403);
        }
    }

    protected function authorizeVerimorCallEventRead(): void
    {
        if (! app('owner') && ! app('admin')->can(VerimorCallEventPermissions::READ)) {
            abort(403);
        }
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
