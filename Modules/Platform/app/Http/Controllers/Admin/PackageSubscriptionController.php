<?php

namespace Modules\Platform\Http\Controllers\Admin;

use Illuminate\Routing\Controllers\Middleware;
use Modules\Auth\Enums\permissions\UserPermissions;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;
use Modules\Base\Http\Controllers\BaseCrudController;
use Modules\Platform\Enums\PackageSubscriptionPaymentStatus;
use Modules\Platform\Enums\permissions\PackageSubscriptionPermissions;
use Modules\Platform\Http\Requests\PackageSubscriptionRequest;
use Modules\Platform\Http\Services\PackageSubscriptionService;
use Modules\Platform\Models\PackageSubscription;

class PackageSubscriptionController extends BaseCrudController
{
    protected $model;

    protected $crudService;

    protected $module = 'platform';

    protected $routePrefix = 'platform.package_subscriptions';

    protected $routeParameters = [];

    protected $createRequest = PackageSubscriptionRequest::class;

    protected $updateRequest = PackageSubscriptionRequest::class;

    protected static $permissionClass = PackageSubscriptionPermissions::class;

    protected static $hasPermission = true;

    protected $hasSoftDelete = false;

    protected $hasDisabled = false;

    protected $hasBulkActions = false;

    public function __construct(PackageSubscription $model, PackageSubscriptionService $crudService)
    {
        app('adminHelper')->addBreadcrumbs(
            trans('admin::dashboard.aside_menu.platform_management.package_subscriptions'),
            route($this->routePrefix.'.index')
        );

        $this->model = $model;
        $this->crudService = $crudService;

        parent::__construct();
    }

    public static function middleware(): array
    {
        $middlewares = parent::middleware();

        if (static::$hasPermission) {
            $middlewares = array_merge($middlewares, [
                new Middleware('need.permissions:'.UserPermissions::SHOW, only : ['serviceProviderPreview']),
            ]);
        }

        return $middlewares;
    }

    public function index()
    {
        $this->data['package_subscriptions_awaiting_verification_count'] = PackageSubscription::query()
            ->where('payment_status', PackageSubscriptionPaymentStatus::AwaitingVerification)
            ->count();

        return parent::index();
    }

    public function serviceProviderPreview(User $user)
    {
        $type = $user->type;
        $typeValue = $type instanceof UserType ? $type->value : $type;

        if ($typeValue !== UserType::ServiceProvider->value) {
            abort(404);
        }

        $user->loadMissing(['service.translations', 'city.translations']);

        $serviceName = $user->service?->smartTrans('name');
        $cityName = $user->city ? ($user->city->smartTrans('name') ?? $user->city->native_name) : null;

        return response()->json([
            'full_name' => trim($user->full_name),
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'central_phone' => $user->central_phone,
            'service_name' => $serviceName,
            'city_name' => $cityName,
            'image_url' => $user->image_url,
        ]);
    }
}
