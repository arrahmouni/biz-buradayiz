<?php

namespace Modules\Seo\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Base\Http\Controllers\BaseCrudController;
use Modules\Seo\Enums\permissions\SeoPermissions;
use Modules\Seo\Http\Requests\SeoRequest;
use Modules\Seo\Http\Services\SeoService;
use Modules\Seo\Models\Seo;

class SeoController extends BaseCrudController
{
    protected $model;

    protected $crudService;

    protected $module = 'seo';

    protected $routePrefix = 'seo.entries';

    protected $createRequest = SeoRequest::class;

    protected $updateRequest = SeoRequest::class;

    protected static $permissionClass = SeoPermissions::class;

    protected static $hasPermission = true;

    protected $hasSoftDelete = true;

    protected $hasDisabled = false;

    protected $hasBulkActions = true;

    public function __construct(Seo $model, SeoService $crudService)
    {
        app('adminHelper')->addBreadcrumbs(trans('seo::breadcrumbs.seo_entries'), route($this->routePrefix.'.index'));

        $this->model = $model;
        $this->crudService = $crudService;

        parent::__construct();

        $this->data['page_targets'] = $crudService->getPageTargetOptionsForForm();
    }
}
