<?php

namespace Modules\Zms\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Base\Http\Controllers\BaseApiController;
use Modules\Zms\Http\Services\CityService;
use Modules\Zms\Models\City;
use Modules\Zms\Resources\CityResource;

class CityController extends BaseApiController
{
    protected $model;

    protected $modelService;

    protected $modelResource = CityResource::class;

    protected $isPaginate = false;

    public function __construct(City $model, CityService $modelService)
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
