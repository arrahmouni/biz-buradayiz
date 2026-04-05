<?php

namespace Modules\Auth\Http\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Enums\AdminStatus;
use Modules\Auth\Enums\permissions\UserPermissions;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User as CrudModel;
use Modules\Base\Http\Services\BaseCrudService;
use Modules\Verimor\Enums\permissions\VerimorCallEventPermissions;
use Yajra\DataTables\Facades\DataTables;

class UserCrudService extends BaseCrudService
{
    protected $modelClass = CrudModel::class;

    protected $unnecessaryFieldsForCrud = [
        'country_id',
        'state_id',
        'image',
        'image_remove',
    ];

    public function createModel(array $data): CrudModel
    {
        $data = $this->normalizeUserCrudPayload($data);
        $modelData = $this->prepareModelData($data);

        $model = DB::transaction(function () use ($modelData, $data) {
            $model = CrudModel::create($modelData);
            $this->uploadImageForModel($model, $data, CrudModel::MEDIA_COLLECTION, 'image');

            return $model;
        });

        return $model;
    }

    public function updateModel(CrudModel $model, array $data): CrudModel
    {
        if (is_null($data['password'])) {
            unset($data['password']);
        }

        $data = $this->normalizeUserCrudPayload($data);
        $modelData = $this->prepareModelData($data);

        DB::transaction(function () use ($data, $model, $modelData) {
            $model->update($modelData);

            if (isset($data['image_remove']) && $data['image_remove'] == true) {
                $this->removeUserImage($model);
            }

            $this->uploadImageForModel($model, $data, CrudModel::MEDIA_COLLECTION, 'image');

            if ($data['status'] != AdminStatus::ACTIVE) {
                $this->removeFcmToken($model);
            }
        });

        return $model;
    }

    private function removeUserImage(CrudModel $model): void
    {
        $media = $model->getFirstMedia(CrudModel::MEDIA_COLLECTION);

        if ($media) {
            $media->delete();
        }
    }

    private function normalizeUserCrudPayload(array $data): array
    {
        if (($data['type'] ?? null) !== UserType::ServiceProvider->value) {
            $data['service_id'] = null;
            $data['city_id'] = null;
        }

        if (array_key_exists('central_phone', $data) && $data['central_phone'] === '') {
            $data['central_phone'] = null;
        }

        return $data;
    }

    private function removeFcmToken(CrudModel $model): void
    {
        foreach ($model->fcmTokens ?? [] as $token) {
            $token->delete();
        }
    }

    public function getDataTable(array $data): JsonResponse
    {
        $userType = $data['userType'] ?? request()->route('userType');
        $isServiceProvider = $userType === UserType::ServiceProvider->value;

        $model = CrudModel::query()->with('media');

        if ($userType !== null) {
            $model->where('type', $userType);
        }

        if ($isServiceProvider) {
            $model->with([
                'service.translations',
                'city.translations',
                'city.state.translations',
                'city.state.country.translations',
            ]);
        }

        if ($this->hasWithDisabled()) {
            $model = $model->withDisabled();
        }

        if ($this->shouldShowTrash($data, UserPermissions::VIEW_TRASH)) {
            $model = $model->onlyTrashed();
        }

        $routeParamsForActions = $userType !== null ? ['userType' => $userType] : [];
        $canViewVerimorCallEvents = app('owner') || app('admin')->can(VerimorCallEventPermissions::READ);

        $dataTable = DataTables::of($model)
            ->filter(function ($query) use ($data) {
                if (isset($data['search']['value']) && ! empty($data['search']['value'])) {
                    $query->simpleSearch($data['search']['value']);
                }
            });

        if ($isServiceProvider) {
            $dataTable
                ->addColumn('service_name', function ($row) {
                    return $row->service?->name ?? '—';
                })
                ->addColumn('country_name', function ($row) {
                    return $row->city?->state?->country?->name ?? '—';
                })
                ->addColumn('state_name', function ($row) {
                    return $row->city?->state?->name ?? '—';
                })
                ->addColumn('city_name', function ($row) {
                    return $row->city?->name ?? '—';
                });
        }

        return $dataTable
            ->addColumn('actions', function ($row) use ($routeParamsForActions, $isServiceProvider, $canViewVerimorCallEvents) {
                $excludeActions = [VIEW_ACTION];
                $additionalActions = [];

                if ($isServiceProvider && $canViewVerimorCallEvents) {
                    $additionalActions[] = app('customDataTable')->addAction(
                        'verimor_call_events',
                        'bi-telephone',
                        15,
                        $row->id,
                        trans('admin::cruds.verimor_call_events.title'),
                        'link',
                        'primary',
                        false,
                        route('verimor.verimor_call_events.index', ['user_id' => $row->id])
                    );
                }

                return
                    app('customDataTable')
                        ->routePrefix('auth.users')
                        ->setRouteParameters($routeParamsForActions)
                        ->of($row, UserPermissions::PERMISSION_NAMESPACE)
                        ->excludeActions($excludeActions)
                        ->getDatatableActions($additionalActions);
            })
            ->toJson();
    }
}
