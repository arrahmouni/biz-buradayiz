<?php

namespace Modules\Zms\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Base\Http\Controllers\BaseApiController;
use Modules\Zms\Http\Services\StateService;
use Modules\Zms\Models\State;
use Modules\Zms\Resources\StateResource;

class StateController extends BaseApiController
{
    protected $model;

    protected $modelService;

    protected $modelResource = StateResource::class;

    protected $isPaginate = false;

    public function __construct(State $model, StateService $modelService)
    {
        $this->model = $model;
        $this->modelService = $modelService;

        parent::__construct();
    }

    public function mergeDataToRequest(Request $request)
    {
        $request->merge([
            'id' => $request->id,
        ]);
    }
}
