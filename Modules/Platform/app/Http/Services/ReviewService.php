<?php

namespace Modules\Platform\Http\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Base\Classes\CustomDataTable;
use Modules\Base\Http\Services\BaseCrudService;
use Modules\Platform\Enums\permissions\ReviewPermissions;
use Modules\Platform\Enums\ReviewStatus;
use Modules\Platform\Models\Review as CrudModel;
use Yajra\DataTables\Facades\DataTables;

class ReviewService extends BaseCrudService
{
    protected $modelClass = CrudModel::class;

    protected $modelScopes = ['user', 'verimorCallEvent'];

    /**
     * @param  array<string, mixed>  $data
     */
    public function createModel(array $data): CrudModel
    {
        $modelData = $this->prepareModelData($data);

        return DB::transaction(function () use ($modelData) {
            return CrudModel::create($modelData);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateModel(CrudModel $model, array $data): CrudModel
    {
        $modelData = $this->prepareModelData($data);

        DB::transaction(function () use ($model, $modelData) {
            $model->update($modelData);
        });

        return $model;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function getDataTable(array $data): JsonResponse
    {
        $model = CrudModel::query()->with($this->modelScopes);

        if ($this->hasWithDisabled()) {
            $model = $model->withDisabled();
        }

        if ($this->shouldShowTrash($data, ReviewPermissions::VIEW_TRASH)) {
            $model = $model->onlyTrashed();
        }

        $canApproveReview = app('owner') || app('admin')->can(ReviewPermissions::APPROVE);
        $canRejectReview  = app('owner') || app('admin')->can(ReviewPermissions::REJECT);

        return DataTables::of($model)
            ->filter(function ($query) use ($data) {
                if (isset($data['search']['value']) && $data['search']['value'] !== '') {
                    $query->simpleSearch($data['search']['value']);
                }
                if (isset($data['advanced_search']) && ! empty($data['advanced_search'])) {
                    $query->advancedSearch($data['advanced_search']);
                }
            })
            ->addColumn('body_preview', function (CrudModel $model) {
                if ($model->body === null || $model->body === '') {
                    return '—';
                }

                return Str::limit(strip_tags($model->body), 120);
            })
            ->addColumn('provider_user', function (CrudModel $model) {
                $user = $model->user;
                if ($user === null) {
                    return null;
                }

                return [
                    'image_url' => $user->image_url,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                ];
            })
            ->addColumn('call_event', function (CrudModel $model) {
                $event = $model->verimorCallEvent;
                if ($event === null) {
                    return null;
                }

                return [
                    'id' => $event->id,
                    'call_uuid' => $event->call_uuid,
                ];
            })
            ->addColumn('actions', function (CrudModel $model) use ($canApproveReview, $canRejectReview) {
                $modalAction = app('customDataTable')
                    ->excludeActions([])
                    ->routePrefix('platform.reviews')
                    ->of($model, ReviewPermissions::PERMISSION_NAMESPACE)
                    ->viewAsModal(
                        route('platform.reviews.viewAsModal', ['model' => $model->id]),
                        $model->id,
                        'reviewViewModal',
                        trans('admin::cruds.reviews.view'),
                    );

                $additionalActions = empty($modalAction) ? [] : [$modalAction];

                if ($model->status === ReviewStatus::Pending) {
                    $moderationTable = app('customDataTable')
                        ->routePrefix('platform.reviews')
                        ->of($model, ReviewPermissions::PERMISSION_NAMESPACE);

                    if ($canApproveReview) {
                        $additionalActions[] = $moderationTable->addAction(
                            'approve',
                            'bi-check-lg',
                            5,
                            null,
                            trans('admin::cruds.approve.title'),
                            'button',
                            '#198754',
                            true,
                            route('platform.reviews.approve', ['model' => $model->id]),
                        );
                    }

                    if ($canRejectReview) {
                        $additionalActions[] = $moderationTable->addAction(
                            'reject',
                            'bi-x-lg',
                            6,
                            null,
                            trans('admin::cruds.reject.title'),
                            'button',
                            '#dc3545',
                            true,
                            route('platform.reviews.reject', ['model' => $model->id]),
                        );
                    }
                }

                return app('customDataTable')
                    ->routePrefix('platform.reviews')
                    ->of($model, ReviewPermissions::PERMISSION_NAMESPACE)
                    ->excludeActions([VIEW_ACTION, UPDATE_ACTION, DISABLE_ACTION, ENABLE_ACTION])
                    ->getDatatableActions(additionalActions: $additionalActions);
            })
            ->rawColumns(['actions'])
            ->toJson();
    }
}
