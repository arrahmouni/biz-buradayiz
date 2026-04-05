<?php

namespace Modules\Platform\Http\Controllers\Admin;

use Modules\Base\Http\Controllers\BaseCrudController;
use Modules\Platform\Enums\permissions\ReviewPermissions;
use Modules\Platform\Http\Requests\ReviewRequest;
use Modules\Platform\Http\Services\ReviewService;
use Modules\Platform\Models\Review;

class ReviewController extends BaseCrudController
{
    protected $model;

    protected $crudService;

    protected $module = 'platform';

    protected $routePrefix = 'platform.reviews';

    protected $routeParameters = [];

    protected $createRequest = ReviewRequest::class;

    protected $updateRequest = ReviewRequest::class;

    protected static $permissionClass = ReviewPermissions::class;

    protected static $hasPermission = true;

    protected $hasSoftDelete = true;

    protected $hasDisabled = false;

    protected $hasBulkActions = true;

    public function __construct(Review $model, ReviewService $crudService)
    {
        app('adminHelper')->addBreadcrumbs(trans('admin::dashboard.aside_menu.review_management.reviews'), route($this->routePrefix.'.index'));

        $this->model = $model;
        $this->crudService = $crudService;

        parent::__construct();
    }
}
