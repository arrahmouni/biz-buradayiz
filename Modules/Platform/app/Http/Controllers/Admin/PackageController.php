<?php

namespace Modules\Platform\Http\Controllers\Admin;

use Modules\Base\Http\Controllers\BaseCrudController;
use Modules\Platform\Models\Package;
use Modules\Platform\Http\Services\PackageService;
use Modules\Platform\Enums\permissions\PackagePermissions;
use Modules\Platform\Http\Requests\PackageRequest;

class PackageController extends BaseCrudController
{
    protected $model;

    protected $crudService;

    protected $module           = 'platform';

    protected $routePrefix      = 'platform.packages';

    protected $routeParameters  = [];

    protected $createRequest    = PackageRequest::class;

    protected $updateRequest    = PackageRequest::class;

    protected static $permissionClass  = PackagePermissions::class;

    protected static $hasPermission    = true;

    protected $hasSoftDelete    = true;

    protected $hasDisabled      = false;

    protected $hasBulkActions   = true;

    public function __construct(Package $model, PackageService $crudService)
    {
        app('adminHelper')->addBreadcrumbs(trans('admin::dashboard.aside_menu.package_management.packages'), route($this->routePrefix . '.index'));

        $this->model        = $model;
        $this->crudService  = $crudService;

        parent::__construct();
    }

}
