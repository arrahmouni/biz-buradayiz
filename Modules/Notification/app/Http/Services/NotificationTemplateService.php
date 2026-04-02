<?php

namespace Modules\Notification\Http\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\Base\Http\Services\BaseCrudService;
use Modules\Notification\Models\NotificationTemplate as CrudModel;
use Modules\Notification\Enums\permissions\NotificationTemplatePermissions;
use Yajra\DataTables\Facades\DataTables;

class NotificationTemplateService extends BaseCrudService
{
    protected $modelClass = CrudModel::class;
    /**
     * The unnecessary fields for crud.
     * Example: if the data has translation fields, you can add them here. As a ('title', 'description')
     */
    protected $unnecessaryFieldsForCrud = [
        'variables',
        'title',
        'description',
        'short_template',
        'long_template',
    ];

    /**
     * Create a new Model instance.
     *
     * @param array $data
     * @return CrudModel
     */
    public function createModel(array $data): CrudModel
    {
        $modelData              = $this->prepareModelData($data);
        $modelData['variables'] = explode(',', $data['variables']);

        $translations = $this->createTranslations($data, 'title', ['description', 'short_template', 'long_template']);

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
        $modelData              = $this->prepareModelData($data);
        $modelData['variables'] = explode(',', $data['variables']);
        unset($modelData['name']);
        unset($modelData['variables']);

        DB::transaction(function () use($data, $model, $modelData){
            $model->update($modelData);
            $this->updateTranslations($model, $data, 'title', ['description', 'short_template', 'long_template']);
        });

        return $model;
    }


    public function getDataTable(array $data) : JsonResponse
    {
        $model = CrudModel::query();

        if($this->hasWithDisabled()) {
            $model = $model->withDisabled();
        }

        if($this->shouldShowTrash($data, NotificationTemplatePermissions::VIEW_TRASH)) {
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
                    ->routePrefix('notification.notification_templates')
                    ->of($model, NotificationTemplatePermissions::PERMISSION_NAMESPACE)
                    ->excludeActions($excludeActions)
                    ->getDatatableActions();
            })
            ->toJson();
    }
}
