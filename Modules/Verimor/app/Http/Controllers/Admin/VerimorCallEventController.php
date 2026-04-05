<?php

namespace Modules\Verimor\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;
use Modules\Base\Http\Controllers\BaseCrudController;
use Modules\Verimor\Enums\permissions\VerimorCallEventPermissions;
use Modules\Verimor\Http\Services\VerimorCallEventService;
use Modules\Verimor\Models\VerimorCallEvent;

class VerimorCallEventController extends BaseCrudController
{
    protected $model;

    protected $crudService;

    protected $module = 'verimor';

    protected $routePrefix = 'verimor.verimor_call_events';

    protected $routeParameters = [];

    protected $createRequest = Request::class;

    protected $updateRequest = Request::class;

    protected static $permissionClass = VerimorCallEventPermissions::class;

    protected static $hasPermission = true;

    protected $hasSoftDelete = false;

    protected $hasDisabled = false;

    protected $hasBulkActions = false;

    public function __construct(VerimorCallEvent $model, VerimorCallEventService $crudService)
    {
        app('adminHelper')->addBreadcrumbs(trans('admin::dashboard.aside_menu.platform_management.verimor_call_events'), route($this->routePrefix.'.index'));

        $this->model = $model;
        $this->crudService = $crudService;

        parent::__construct();
    }

    public function index()
    {
        $this->data['verimorCallEventsFilterUserSelected'] = [];

        $userId = request()->query('user_id');
        if (is_numeric($userId) && (int) $userId > 0) {
            $user = User::query()->find((int) $userId);
            if ($user !== null && $user->type === UserType::ServiceProvider) {
                $this->data['verimorCallEventsFilterUserSelected'] = [$user->formAjaxArray(true)];
            }
        }

        return parent::index();
    }
}
