<?php

namespace Modules\Auth\Http\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Enums\AdminStatus;
use Modules\Auth\Models\User as CrudModel;
use Modules\Base\Http\Services\BaseCrudService;
use Modules\Auth\Enums\permissions\UserPermissions;
use Yajra\DataTables\Facades\DataTables;

class UserCrudService extends BaseCrudService
{
    protected $modelClass = CrudModel::class;

    public function createModel(array $data) : CrudModel
    {
        $modelData = $this->prepareModelData($data);

        $model = DB::transaction(function () use($data, $modelData){
            $model = CrudModel::create($modelData);

            return $model;
        });

        return $model;
    }

    public function updateModel(CrudModel $model, array $data) : CrudModel
    {
        if(is_null($data['password'])){
            unset($data['password']);
        }

        $modelData = $this->prepareModelData($data);

        DB::transaction(function () use($data, $model, $modelData){
            $model->update($modelData);

            if($data['status'] != AdminStatus::ACTIVE){
                $this->removeFcmToken($model);
            }
        });

        return $model;
    }

    private function removeFcmToken(CrudModel $model) : void
    {
        foreach($model->fcmTokens ?? [] as $token){
            $token->delete();
        }
    }


    public function getDataTable(array $data) : JsonResponse
    {

        $model = CrudModel::query();

        if($this->hasWithDisabled()) {
            $model = $model->withDisabled();
        }

        if($this->shouldShowTrash($data, UserPermissions::VIEW_TRASH)) {
            $model = $model->onlyTrashed();
        }

        return DataTables::of($model)
            ->filter(function ($query) use ($data) {
                if(isset($data['search']['value']) && !empty($data['search']['value'])){
                    $query->simpleSearch($data['search']['value']);
                }
            })
            ->addColumn('actions', function ($model) {
                $excludeActions = [VIEW_ACTION];

                return
                    app('customDataTable')
                    ->routePrefix('auth.users')
                    ->of($model, UserPermissions::PERMISSION_NAMESPACE)
                    ->excludeActions($excludeActions)
                    ->getDatatableActions();
            })
            ->toJson();
    }
}
