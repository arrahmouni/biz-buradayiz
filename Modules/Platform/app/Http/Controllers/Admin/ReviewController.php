<?php

namespace Modules\Platform\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Modules\Base\Http\Controllers\BaseCrudController;
use Modules\Platform\Enums\permissions\ReviewPermissions;
use Modules\Platform\Enums\ReviewStatus;
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

    public static function middleware(): array
    {
        $middlewares = parent::middleware();

        if (static::$hasPermission) {
            $middlewares = array_merge($middlewares, [
                new Middleware('need.permissions:'.ReviewPermissions::APPROVE, only : ['approve']),
                new Middleware('need.permissions:'.ReviewPermissions::REJECT, only : ['reject']),
            ]);
        }

        return $middlewares;
    }

    public function approve(Request $request)
    {
        $this->data['model'] = $this->model->query()->findOrFail($request->model);

        if ($this->data['model']->status !== ReviewStatus::Pending) {
            return sendFailResponse(customMessage: trans('admin::cruds.reviews.moderation_not_pending'));
        }

        try {
            $this->data['model']->update(['status' => ReviewStatus::Approved]);
        } catch (Exception $e) {
            return sendExceptionResponse($e);
        }

        return sendSuccessResponse();
    }

    public function reject(Request $request)
    {
        $this->data['model'] = $this->model->query()->findOrFail($request->model);

        if ($this->data['model']->status !== ReviewStatus::Pending) {
            return sendFailResponse(customMessage: trans('admin::cruds.reviews.moderation_not_pending'));
        }

        try {
            $this->data['model']->update(['status' => ReviewStatus::Rejected]);
        } catch (Exception $e) {
            return sendExceptionResponse($e);
        }

        return sendSuccessResponse();
    }
}
