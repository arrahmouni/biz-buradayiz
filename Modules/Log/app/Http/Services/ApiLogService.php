<?php

namespace Modules\Log\Http\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Log\Models\ApiLog as CrudModel;
use Modules\Base\Http\Services\BaseCrudService;
use Modules\Log\Enums\permissions\ApiLogPermissions;
use Yajra\DataTables\Facades\DataTables;

class ApiLogService extends BaseCrudService
{
    protected $modelClass = CrudModel::class;

    public static function log($serviceName, $method, $endpoint, $request, $response, $user = null)
    {
        if (!config('log.enable_api_logs')) {
            return null;
        }
        try {
            $user ??= app()->bound('user') ? app('user') : null;

            $model = DB::transaction(function()  use ($user, $serviceName, $method, $endpoint, $request, $response) {
                return CrudModel::create([
                    'user_type'     => $user ? get_class($user) : null,
                    'user_id'       => $user ? $user->id : null,
                    'service_name'  => $serviceName,
                    'method'        => $method,
                    'endpoint'      => $endpoint,
                    'request'       => json_encode($request),
                    'response'      => json_encode($response->json()),
                    'status'        => $response->successful() ? SUCCESS_STATUS : FAILED_STATUS,
                    'status_code'   => $response->status(),
                ]);

            });
        } catch (\Exception $e) {
            Log::error('Error while logging API request', [
                'error' => $e->getMessage(),
            ]);
        }

        return $model ?? null;
    }


    public function getDataTable(array $data) : JsonResponse
    {
        $model = CrudModel::with('user');

        if($this->shouldShowTrash($data, ApiLogPermissions::VIEW_TRASH)) {
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
            ->addColumn('actions', function ($model) {
                $excludeActions      = [VIEW_ACTION, UPDATE_ACTION];
                $modelId             = $model->id;
                $additionalActions[] = app('customDataTable')->viewAsModal(route('log.api_logs.viewAsModal', ['model' => $modelId]), $modelId, 'apiLogViewModal', trans('log::strings.details'));

                return
                    app('customDataTable')
                    ->routePrefix('log.api_logs')
                    ->of($model, ApiLogPermissions::PERMISSION_NAMESPACE)
                    ->excludeActions($excludeActions)
                    ->getDatatableActions(additionalActions: $additionalActions, withMainCrudActions: true);
            })
            ->toJson();
    }

}
