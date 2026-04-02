<?php

namespace Modules\Zms\Http\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\Base\Http\Services\BaseCrudService;
use Modules\Zms\Models\State as CrudModel;
use Modules\Zms\Enums\permissions\CountryPermissions;
use Yajra\DataTables\Facades\DataTables;

class StateService extends BaseCrudService
{
    protected $modelClass = CrudModel::class;

    public function updateModel(CrudModel $model, array $data) : CrudModel
    {
        $transData['name'] = $data['name'] ?? [];

        unset($data['name']);

        DB::transaction(function () use($data, $model, $transData){
            $model->update($data);
            $this->updateTranslations($model, $transData, 'name');
        });

        return $model;
    }


    public function getDataTable(array $data) : JsonResponse
    {
        $model = CrudModel::where('country_id', $data['country_id'] ?? null)->withCount('cities');

        return DataTables::of($model)
            ->filter(function ($query) use ($data) {
                if(isset($data['search']['value']) && !empty($data['search']['value'])){
                    $query->simpleSearch($data['search']['value']);
                }
            })
            ->addColumn('actions', function ($model) use ($data){
                $excludeActions = [VIEW_ACTION];

                return
                    app('customDataTable')
                    ->routePrefix('zms.states')
                    ->of($model, CountryPermissions::PERMISSION_NAMESPACE)
                    ->excludeActions($excludeActions)
                    ->getDatatableActions();
            })
            ->toJson();
    }
}
