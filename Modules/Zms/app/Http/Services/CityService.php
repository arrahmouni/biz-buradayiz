<?php

namespace Modules\Zms\Http\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\Base\Http\Services\BaseCrudService;
use Modules\Base\Http\Services\Traits\ApiServiceTrait;
use Modules\Zms\Enums\permissions\CountryPermissions;
use Modules\Zms\Models\City as CrudModel;
use Yajra\DataTables\Facades\DataTables;

class CityService extends BaseCrudService
{
    use ApiServiceTrait;

    protected $modelClass = CrudModel::class;

    protected function applyApiConditions($query, array $data)
    {
        if (! empty($data['state_id'])) {
            return $query->where('state_id', $data['state_id']);
        }

        return $query->whereRaw('0 = 1');
    }

    protected function handleApiCollection($query, array $data)
    {
        return $query->orderBy('native_name');
    }

    public function updateModel(CrudModel $model, array $data): CrudModel
    {
        $transData['name'] = $data['name'] ?? [];

        unset($data['name']);

        DB::transaction(function () use ($data, $model, $transData) {
            $model->update($data);
            $this->updateTranslations($model, $transData, 'name');
        });

        return $model;
    }

    public function getDataTable(array $data): JsonResponse
    {

        $model = CrudModel::where('state_id', $data['state_id'] ?? null);

        if ($this->hasWithDisabled()) {
            $model = $model->withDisabled();
        }

        return DataTables::of($model)
            ->filter(function ($query) use ($data) {
                if (isset($data['search']['value']) && ! empty($data['search']['value'])) {
                    $query->simpleSearch($data['search']['value']);
                }
            })
            ->addColumn('actions', function ($model) {
                $excludeActions = [VIEW_ACTION];

                return
                    app('customDataTable')
                        ->routePrefix('zms.cities')
                        ->of($model, CountryPermissions::PERMISSION_NAMESPACE)
                        ->excludeActions($excludeActions)
                        ->getDatatableActions();
            })
            ->toJson();
    }
}
