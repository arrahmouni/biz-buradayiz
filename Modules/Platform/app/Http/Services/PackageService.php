<?php

namespace Modules\Platform\Http\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\Base\Http\Services\BaseCrudService;
use Modules\Platform\Enums\permissions\PackagePermissions;
use Modules\Platform\Models\Package as CrudModel;
use Yajra\DataTables\Facades\DataTables;

class PackageService extends BaseCrudService
{
    protected $modelClass = CrudModel::class;

    protected $unnecessaryFieldsForCrud = [
        'name',
        'description',
        'features',
        'service_ids',
    ];

    public function createModel(array $data): CrudModel
    {
        $modelData = $this->prepareModelData($data);
        if (! empty($modelData['is_free_tier'])) {
            $modelData['price'] = 0;
        }

        $translations = $this->createTranslations($data, 'name', ['description', 'features']);

        $serviceIds = $data['service_ids'] ?? [];

        $model = DB::transaction(function () use ($modelData, $translations, $serviceIds) {
            $model = CrudModel::create($modelData);

            $model->update($translations);

            $model->services()->sync($serviceIds);

            return $model;
        });

        return $model;
    }

    public function updateModel(CrudModel $model, array $data): CrudModel
    {
        $modelData = $this->prepareModelData($data);
        unset($modelData['is_free_tier']);
        if ($model->is_free_tier) {
            unset($modelData['price']);
        }

        $serviceIds = $data['service_ids'] ?? [];

        DB::transaction(function () use ($data, $model, $modelData, $serviceIds) {
            $model->update($modelData);
            $this->updateTranslations($model, $data, 'name', ['description', 'features']);
            $model->services()->sync($serviceIds);
        });

        return $model;
    }

    public function getDataTable(array $data): JsonResponse
    {
        $model = CrudModel::query()
            ->with(['services.translations']);

        if ($this->hasWithDisabled()) {
            $model = $model->withDisabled();
        }

        if ($this->shouldShowTrash($data, PackagePermissions::VIEW_TRASH)) {
            $model = $model->onlyTrashed();
        }

        return DataTables::of($model)
            ->filter(function ($query) use ($data) {
                if (isset($data['search']['value']) && ! empty($data['search']['value'])) {
                    $query->simpleSearch($data['search']['value']);
                }
                if (isset($data['advanced_search']) && ! empty($data['advanced_search'])) {
                    $query->advancedSearch($data['advanced_search']);
                }
            })
            ->addColumn('name', function ($model) {
                return $model->smartTrans('name');
            })
            ->addColumn('free_tier_badge', function ($model) {
                if ($model->is_free_tier) {
                    return [
                        'label' => trans('admin::cruds.packages.free_tier_badge'),
                        'color' => 'success',
                    ];
                }

                return [
                    'label' => trans('admin::cruds.packages.standard_tier_badge'),
                    'color' => 'light',
                ];
            })
            ->addColumn('price_display', function ($model) {
                return number_format((float) $model->price, 2).' '.$model->currency;
            })
            ->addColumn('billing_period', function ($model) {
                return trans('admin::cruds.packages.billing_periods.'.$model->billing_period->value);
            })
            ->addColumn('services_list', function ($model) {
                $names = $model->services->map(fn ($s) => $s->smartTrans('name'))->filter()->values();

                return $names->isEmpty() ? '—' : $names->implode(', ');
            })
            ->addColumn('actions', function ($model) {
                $excludeActions = [VIEW_ACTION];

                return
                    app('customDataTable')
                        ->routePrefix('platform.packages')
                        ->of($model, PackagePermissions::PERMISSION_NAMESPACE)
                        ->excludeActions($excludeActions)
                        ->getDatatableActions();
            })
            ->toJson();
    }
}
