<?php

namespace Modules\Zms\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Zms\Models\Country;
use Modules\Zms\Resources\CountryResource;
use Modules\Zms\Http\Services\CountryService;
use Modules\Base\Http\Controllers\BaseApiController;

class CountryController extends BaseApiController
{
    protected $model;

    protected $modelService;

    protected $modelResource = CountryResource::class;

    protected $isPaginate = true;

    public function __construct(Country $model, CountryService $modelService)
    {
        $this->model = $model;
        $this->modelService = $modelService;

        parent::__construct();
    }

    public function mergeDataToRequest(Request $request)
    {
        $request->merge([
            'id'      => $request->id,
        ]);
    }
}
