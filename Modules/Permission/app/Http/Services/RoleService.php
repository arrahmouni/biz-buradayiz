<?php

namespace Modules\Permission\Http\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\Base\Http\Services\BaseCrudService;
use Modules\Permission\Models\Role as CrudModel;
use Modules\Permission\Enums\permissions\RolePermissions;
use Yajra\DataTables\Facades\DataTables;

class RoleService extends BaseCrudService
{
    protected $modelClass = CrudModel::class;

    protected $modelScopes = ['abilities'];
    
    public function createModel(array $data) : CrudModel
    {
        $translations = $this->createTranslations($data, 'title', ['description']);

        $model = DB::transaction(function () use($data, $translations){
            $model = CrudModel::create([
                'name' => $data['code'],
            ]);

            $model->update($translations);

            $this->syncRolePermissions($model, $data);

            return $model;
        });

        return $model;
    }

    public function updateModel(CrudModel $model, array $data) : CrudModel
    {
        DB::transaction(function () use($data, $model){
            $this->updateTranslations($model, $data, 'title', ['description']);
            $this->syncRolePermissions($model, $data);
        });

        return $model;
    }

    private function syncRolePermissions(CrudModel $model, array $data) : void
    {
        isset($data['permissions']) ? $model->abilities()->sync($data['permissions']) : $model->abilities()->detach();
    }


    public function getDataTable(array $data) : JsonResponse
    {

        $model = CrudModel::with('abilities');

        if($this->shouldShowTrash($data, RolePermissions::VIEW_TRASH)) {
            $model = $model->onlyTrashed();
        }

        return DataTables::of($model)
            ->filter(function ($query) use($data){
                if(isset($data['search']['value']) && !empty($data['search']['value'])){
                    $query->simpleSearch($data['search']['value']);
                }
            })
            ->addColumn('permissions', function($model) {
                $permissions = [];

                foreach($model->abilities->groupBy('ability_group_id') as $group) {
                    $abilityGroup = $group->first()?->abilityGroup;
                    if(!$abilityGroup) {
                        continue;
                    }
                    $permissions[] = [
                        'title'         => $abilityGroup->smartTrans('title'),
                        'icon'          => $abilityGroup->icon,
                        'permissions'   => $group->map(function($ability) {
                            return [
                                'id'    => $ability->id,
                                'code'  => $ability->name,
                                'title' => $ability->smartTrans('title')
                            ];
                        })->toArray()
                    ];
                }

                return $permissions;
            })
            ->addColumn('actions', function($model) {
                $excludeActions = [VIEW_ACTION];

                return
                    app('customDataTable')
                    ->routePrefix('permission.roles')
                    ->of($model, RolePermissions::PERMISSION_NAMESPACE)
                    ->excludeActions($excludeActions)
                    ->getDatatableActions();
            })
            ->toJson();
    }
}
