<?php

namespace Modules\Log\Http\Services;

use Illuminate\Http\JsonResponse;
use Modules\Base\Http\Services\BaseCrudService;
use Modules\Log\Models\Audit as CrudModel;
use Yajra\DataTables\Facades\DataTables;

class AuditService extends BaseCrudService
{
    protected $modelClass = CrudModel::class;

    public function getDataTable(array $data) : JsonResponse
    {
        $model = CrudModel::where('auditable_type', $data['type'])
        ->where('auditable_id', $data['id'])
        ->with('user');

        return DataTables::of($model)
            ->filter(function ($query) use ($data) {
                if(isset($data['search']['value']) && !empty($data['search']['value'])){
                    $query->simpleSearch($data['search']['value']);
                }
                if(isset($data['advanced_search']) && !empty($data['advanced_search'])){
                    $query->advancedSearch($data['advanced_search']);
                }
            })

            ->addColumn('actions', function ($model) {
                $modelId = $model->id;
                $additionalActions[] = app('customDataTable')->viewAsModal(route('log.activity_log.viewAsModal', ['model' => $modelId]), $modelId, 'activityLogViewModal', trans('log::strings.details'));

                return app('customDataTable')->getDatatableActions(additionalActions: $additionalActions, withMainCrudActions: false);
            })

            ->toJson();
    }
}
