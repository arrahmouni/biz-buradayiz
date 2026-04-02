<?php

namespace Modules\Permission\Http\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\Base\Http\Services\BaseCrudService;
use Modules\Permission\Models\AbilityGroup as CrudModel;
use Modules\Permission\Enums\permissions\AbilityPermissions;
use Yajra\DataTables\Facades\DataTables;

class PermissionService extends BaseCrudService
{
    protected $modelClass = CrudModel::class;
    public function createModel(array $data) : CrudModel
    {
        if($data['permission_type'] == 'group_permission')
        {
            $model = new CrudModel();

            $model = $this->createModelForGroupPermission($model, $data);
        }
        else
        {
            $model = CrudModel::where('code', $data['permission_group_code'])->firstOrFail();

            $model = $this->createModelForSinglePermission($model, $data);
        }

        return $model;
    }

    public function createModelForGroupPermission(CrudModel $model, array $data) : CrudModel
    {
        $translations = $this->createTranslations($data, 'title', ['description']);

        DB::transaction(function () use ($data, $model, $translations) {
            $model->code = $data['ability_group_code'];
            $model->icon = $data['ability_group_icon'];

            $model->save();

            $model->update($translations);

            $this->createAbilites($model, $data);
        });

        return $model;
    }

    public function createModelForSinglePermission(CrudModel $model, array $data) : CrudModel
    {
        $translations = $this->createTranslations($data, 'title', ['description']);

        DB::transaction(function () use ($data, $model, $translations) {

            $ability = $model->abilities()->firstOrCreate(
                [
                    'name' => $data['permission_name']
                ]
            );

            $ability->update($translations);

            $model->save();
        });

        return $model;
    }

    public function updateModel($data, $model)
    {
        DB::transaction(function () use ($data, $model) {
            $model->icon = $data['ability_group_icon'];
            $this->updateTranslations($model, $data, 'title', ['description']);

            $existsAbilities = $this->createAbilites($model, $data);
            $model->abilities()->whereNotIn('name', $existsAbilities)->delete();
        });

        return $model;
    }

    private function createAbilites($model, $data) : array
    {
        $existsAbilities = [];

        foreach ($data['abilities'] as $key => $abilityCode) {
            if(in_array($key, $data['ability_types'])) {
                $existsAbilities[] = $abilityCode;
                $abilityData = createTranslateArray(field: 'title', key: 'cruds.' . strtolower($key) . '.title', module: 'admin');

                $model->abilities()->firstOrCreate(
                    [
                        'name' => $abilityCode
                    ],
                    $abilityData
                );
            }
        }

        return $existsAbilities;
    }

    public function getModel(int|null $id = null, $withTrashed = false, $withDisabled = false)
    {
        if ($id === null) {
            // Return query builder when no ID is provided (for backward compatibility)
            $model = CrudModel::query();
            return $this->applyModelScopes($model);
        }

        // Use parent method when ID is provided
        return parent::getModel($id, $withTrashed, $withDisabled);
    }

    public function getDataTable(array $data) : JsonResponse
    {

        $model = CrudModel::query();

        if($this->shouldShowTrash($data, AbilityPermissions::VIEW_TRASH)) {
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
                    ->routePrefix('permission.permissions')
                    ->of($model, AbilityPermissions::PERMISSION_NAMESPACE)
                    ->excludeActions($excludeActions)
                    ->getDatatableActions();
            })
            ->toJson();
    }
}
