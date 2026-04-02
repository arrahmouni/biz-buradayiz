<?php

namespace Modules\Zms\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use Modules\Zms\Models\City;
use Modules\Zms\Http\Requests\UpdateCity;
use Modules\Base\Http\Controllers\BaseCrudController;
use Modules\Zms\Http\Services\CityService;
use Modules\Zms\Enums\permissions\CountryPermissions;

class CityController extends BaseCrudController
{
    protected $module = 'zms';

    protected $model;

    protected $crudService;

    protected static $permissionClass = CountryPermissions::class;

    protected $routePrefix = 'zms.states';

    protected $updateRequest = UpdateCity::class;

    protected static $hasPermission = true;

    protected $hasSoftDelete = false;

    protected $hasDisabled = true;

    protected $hasBulkActions = false;

    public function __construct(City $model, CityService $crudService)
    {
        $this->model            = $model;
        $this->crudService      = $crudService;

        parent::__construct();
    }

    /**
     * Send a listing of the resource to ajax.
     */
    public function datatable(Request $request)
    {
        $request->merge(['state_id' => $request->state_id]);
        return parent::datatable($request);
    }

    public function getModelForAjax(Request $request)
    {
        if (! $request->filled('state_id')) {
            return $this->model::query()->whereRaw('0 = 1');
        }

        $this->data['model'] = $this->model::query()->where('state_id', $request->state_id);

        if ($request->has('q')) {
            $term = trim($request->q);
            $this->data['model'] = $this->data['model']->simpleSearch($term);
        }

        return $this->data['model'];
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function update(Request $request)
    {
        $this->data['model']         = $this->crudService->getModel($request->model);

        app('adminHelper')->addBreadcrumbs($this->data['model']->state?->country?->name, route('zms.countries.update', $this->data['model']->state?->country_id));

        app('adminHelper')->addBreadcrumbs($this->data['model']->state?->name, route($this->routePrefix . '.update', $this->data['model']->state_id));

        app('adminHelper')->addBreadcrumbs(trans('admin::cruds.cities.edit'));

        return view($this->module . '::' . $this->model::VIEW_PATH . '.update', $this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function postUpdate(Request $request)
    {
        app($this->updateRequest);

        $this->data['model'] = $this->crudService->getModel(id: $request->model, withTrashed: $this->hasSoftDelete, withDisabled: $this->hasDisabled);

        try
        {
            $this->crudService->updateModel($this->data['model'],  app($this->updateRequest)->validated());
        }
        catch(Exception $e)
        {
            return sendExceptionResponse($e);
        }

        return sendSuccessResponse(route($this->routePrefix . '.update', ['model' => $this->data['model']->state_id]));
    }
}
