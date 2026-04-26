<?php

namespace Modules\Verimor\Http\Services;

use Illuminate\Http\JsonResponse;
use Modules\Base\Http\Services\BaseCrudService;
use Modules\Verimor\Enums\permissions\VerimorCallEventPermissions;
use Modules\Verimor\Models\VerimorCallEvent as CrudModel;
use Yajra\DataTables\Facades\DataTables;

class VerimorCallEventService extends BaseCrudService
{
    protected $modelClass = CrudModel::class;

    protected $modelScopes = ['user', 'packageSubscription'];

    public function getDataTable(array $data): JsonResponse
    {
        $model = CrudModel::query()
            ->select('verimor_call_events.*')
            ->with($this->modelScopes);

        return DataTables::of($model)
            ->filter(function ($query) use ($data) {
                if (! empty($data['scoped_user_id'])) {
                    $query->where('verimor_call_events.user_id', (int) $data['scoped_user_id']);
                }
                if (isset($data['search']['value']) && $data['search']['value'] !== '') {
                    $query->simpleSearch($data['search']['value']);
                }
                if (! empty($data['advanced_search'])) {
                    $query->advancedSearch($data['advanced_search']);
                }
            })
            ->addColumn('provider_user', function (CrudModel $model) {
                $user = $model->user;
                if ($user === null) {
                    return null;
                }

                return [
                    'image_url' => $user->image_url,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                ];
            })
            ->addColumn('actions', function (CrudModel $model) {
                $datatable = app('customDataTable')
                    ->routePrefix('verimor.verimor_call_events')
                    ->of($model, VerimorCallEventPermissions::PERMISSION_NAMESPACE);

                $modalAction = $datatable->viewAsModal(
                    route('verimor.verimor_call_events.viewAsModal', ['model' => $model->id]),
                    $model->id,
                    'verimorCallEventViewModal',
                    trans('verimor::strings.view_modal_title.call_event'),
                );

                $additionalActions = empty($modalAction) ? [] : [$modalAction];

                return $datatable->getDatatableActions(additionalActions: $additionalActions, withMainCrudActions: false);
            })
            ->rawColumns(['actions'])
            ->toJson();
    }
}
