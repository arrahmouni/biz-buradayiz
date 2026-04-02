<?php

namespace Modules\Crm\Http\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\Base\Http\Services\BaseCrudService;
use Modules\Crm\Models\Subscribe as CrudModel;
use Modules\Crm\Enums\permissions\SubscribePermissions;
use Yajra\DataTables\Facades\DataTables;

class SubscribeService extends BaseCrudService
{
    protected $modelClass = CrudModel::class;

    /**
     * Create a new Model instance.
     *
     * @param array $data
     * @return CrudModel
     */
    public function createModel(array $data): CrudModel
    {
        $modelData = $this->prepareModelData($data);

        $model = DB::transaction(function () use($modelData){
            $model = CrudModel::create($modelData);

            return $model;
        });

        return $model;
    }


    public function getDataTable(array $data) : JsonResponse
    {

        $model = CrudModel::query();

        if($this->shouldShowTrash($data, SubscribePermissions::VIEW_TRASH)) {
            $model = $model->onlyTrashed();
        }

        return DataTables::of($model)
            ->filter(function ($query) use ($data) {
                if(isset($data['search']['value']) && !empty($data['search']['value'])){
                    $query->simpleSearch($data['search']['value']);
                }
            })
            ->addColumn('actions', function ($model) {
                $excludeActions = [VIEW_ACTION, UPDATE_ACTION];

                return
                    app('customDataTable')
                    ->routePrefix('crm.subscribes')
                    ->of($model, SubscribePermissions::PERMISSION_NAMESPACE)
                    ->excludeActions($excludeActions)
                    ->getDatatableActions();
            })
            ->toJson();
    }
}
