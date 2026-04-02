<?php

namespace Modules\Zms\Http\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\Base\Http\Services\BaseCrudService;
use Modules\Base\Http\Services\Traits\ApiServiceTrait;
use Modules\Zms\Models\Country as CrudModel;
use Modules\Zms\Enums\permissions\CountryPermissions;
use Yajra\DataTables\Facades\DataTables;

class CountryService extends BaseCrudService
{
    use ApiServiceTrait;

    protected $modelClass = CrudModel::class;

    protected $apiRelations = ['states.cities'];

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
        $model = CrudModel::query();

        if($this->hasWithDisabled()) {
            $model = $model->withDisabled();
        }

        $model = $model->withCount('states');

        if($this->shouldShowTrash($data, CountryPermissions::VIEW_TRASH)) {
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
                    ->routePrefix('zms.countries')
                    ->of($model,  CountryPermissions::PERMISSION_NAMESPACE)
                    ->excludeActions($excludeActions)
                    ->getDatatableActions();
            })
            ->toJson();
    }
}
