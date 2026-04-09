<?php

namespace Modules\Base\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Admin\Resources\PaginateResource;

class BaseWebController extends BaseController
{
    public $data = [];

    protected $model;

    protected $modelResource;

    protected $modelService;

    protected $modelRequest;

    public $supportedLang;

    public $defaultLocale;

    protected $isPaginate;

    public $locale;

    /**
     * View name for list() (e.g. front::posts.index). Set in child controllers that use list().
     */
    protected string $listView = '';

    /**
     * View name for show() (e.g. front::posts.show). Set in child controllers that use show().
     */
    protected string $showView = '';

    public function __construct()
    {
        $this->defaultLocale = getSetting('app_default_language', 'ar');

        $this->supportedLang = view()->shared('_ALL_LOCALE_KEY_');

        $this->locale = in_array(request()->header('locale', $this->defaultLocale), $this->supportedLang) ? request()->header('locale', $this->defaultLocale) : getSetting('app_default_language', 'ar');

        app()->setLocale($this->locale);

        if ($this->model !== null) {
            $this->data['model_plural'] = Str::plural(Str::snake(class_basename($this->model)));

            $this->data['model_singular'] = Str::singular(Str::snake(class_basename($this->model)));
        }

        parent::__construct();
    }

    /**
     * Merge Data To Request For Collection
     */
    public function mergeDataToRequestForCollection(Request $request)
    {
        $request->merge([]);
    }

    /**
     * Merge Data To Request For Show, Update And Destroy
     */
    public function mergeDataToRequest(Request $request)
    {
        $request->merge([]);
    }

    /**
     * Load collection + pagination into $this->data for list() and ajaxList().
     */
    protected function prepareListCollection(Request $request): void
    {
        $this->mergeDataToRequestForCollection($request);

        $this->data['model'] = $this->modelService->getDataForApi($request->all(), isCollection: true);
        $this->data['page'] = $request->has('page') ? $request->page : 1;
        $this->data['data'] = $this->isPaginate ? $this->data['model']->paginate(NUMBER_OF_RECORDS_PER_PAGE, ['*'], 'page', $this->data['page']) : $this->data['model']->get();
        $this->data['paginate'] = $this->isPaginate ? new PaginateResource($this->data['data']) : null;
    }

    /**
     * Display a listing of the resource.
     */
    public function list(Request $request): View
    {
        if ($this->listView === '') {
            throw new \LogicException(sprintf('%s must define the $listView property to use list().', static::class));
        }

        $this->prepareListCollection($request);

        $this->data[$this->data['model_plural']] = $this->modelResource::collection($this->data['data']);

        return view($this->listView, $this->data);
    }

    /**
     * Send a listing of the resource for AJAX (uses BaseController::formatDataForAjax).
     */
    public function ajaxList(Request $request)
    {
        $this->mergeDataToRequestForCollection($request);

        $this->data['model'] = $this->modelService->getDataForApi($request->all(), isCollection: true);

        return $this->formatDataForAjax($request, $this->data['model']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request): View
    {
        if ($this->showView === '') {
            throw new \LogicException(sprintf('%s must define the $showView property to use show().', static::class));
        }

        $this->mergeDataToRequest($request);

        $this->data['model'] = $this->modelService->getDataForApi($request->all(), isCollection: false);

        $this->data[$this->data['model_singular']] = new $this->modelResource($this->data['model']);

        return view($this->showView, $this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): RedirectResponse
    {
        app($this->modelRequest);

        $result = $this->modelService->createModel(app($this->modelRequest)->validated());

        if (is_array($result) && isset($result['success']) && ! $result['success']) {
            return redirect()->back()->withInput()->withErrors($result['errors'] ?? [])->with('error', $result['message']);
        }

        return redirect()->back()->with('success', trans('response::messages.api_response_messages.created_successfully'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request): RedirectResponse
    {
        app($this->modelRequest);

        $this->mergeDataToRequest($request);

        $this->data['model'] = $this->modelService->getDataForApi($request->all(), isCollection: false);

        $this->modelService->updateModel($this->data['model'], app($this->modelRequest)->validated());

        return redirect()->back()->with('success', trans('response::messages.api_response_messages.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $this->mergeDataToRequest($request);

        $this->data['model'] = $this->modelService->getDataForApi($request->all(), isCollection: false);

        $this->data['model']->delete();

        return redirect()->back()->with('success', trans('response::messages.api_response_messages.deleted_successfully'));
    }
}
