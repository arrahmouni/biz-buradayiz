<?php

namespace Modules\Platform\Http\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Base\Http\Services\BaseCrudService;
use Modules\Platform\Enums\permissions\ServicePermissions;
use Modules\Platform\Models\Service as CrudModel;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class ServiceService extends BaseCrudService
{
    protected $modelClass = CrudModel::class;

    protected $unnecessaryFieldsForCrud = [
        'name',
        'description',
    ];

    /**
     * Create a new Model instance.
     *
     * @param array $data
     * @return CrudModel
     */
    public function createModel(array $data): CrudModel
    {
        $modelData = $this->prepareModelData($data);

        $translations = $this->createTranslations($data, 'name', ['description']);

        $model = DB::transaction(function () use($modelData, $translations){
            $model = CrudModel::create($modelData);

            $model->update($translations);

            return $model;
        });

        return $model;
    }

    /**
     * Update a Model instance.
     *
     * @param CrudModel $model
     * @param array $data
     * @return CrudModel
     */
    public function updateModel(CrudModel $model, array $data) : CrudModel
    {
        $modelData = $this->prepareModelData($data);

        DB::transaction(function () use($data, $model, $modelData){
            $model->update($modelData);
            $this->updateTranslations($model, $data, 'name', ['description']);
        });

        return $model;
    }

    /**
     * Get DataTable data.
     *
     * @param array $data
     * @return JsonResponse
     */
    public function getDataTable(array $data) : JsonResponse
    {
        $model = CrudModel::query();

        if($this->hasWithDisabled()) {
            $model = $model->withDisabled();
        }

        if($this->shouldShowTrash($data, ServicePermissions::VIEW_TRASH)) {
            $model = $model->onlyTrashed();
        }

        return DataTables::of($model)
            ->filter(function ($query) use ($data) {
                if(isset($data['search']['value']) && !empty($data['search']['value'])){
                    $query->simpleSearch($data['search']['value']);
                }
                if(isset($data['advanced_search']) && !empty($data['advanced_search'])){
                    $query->advancedSearch($data['advanced_search']);
                }
            })
            ->addColumn('name', function ($model) {
                return $model->smartTrans('name');
            })
            ->addColumn('description', function ($model) {
                $text = $model->smartTrans('description');

                return $text !== '' && $text !== null ? Str::limit(strip_tags((string) $text), 120) : '—';
            })
            ->addColumn('show_in_search_filters', function ($model) {
                return $model->show_in_search_filters
                    ? trans('admin::confirmations.yes')
                    : trans('admin::confirmations.no');
            })

            ->addColumn('actions', function ($model) {
                $excludeActions = [VIEW_ACTION];

                return
                    app('customDataTable')
                    ->routePrefix('platform.services')
                    ->of($model, ServicePermissions::PERMISSION_NAMESPACE)
                    ->excludeActions($excludeActions)
                    ->getDatatableActions();
            })
            ->toJson();
    }

}
