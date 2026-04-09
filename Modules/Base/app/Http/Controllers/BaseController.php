<?php

namespace Modules\Base\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Admin\Resources\PaginateResource;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends Controller
{
    public $data = [];

    public $supportedLang;

    public function __construct()
    {
        $this->supportedLang = view()->shared('_ALL_LOCALE_');
    }

    /**
     * Paginate a query builder and format rows for AJAX (select2 + response helper).
     *
     * @param  Builder|\Illuminate\Database\Query\Builder  $model
     * @param  string|null  $pageName  Query-string page parameter (defaults to data.model_plural or "page")
     * @return JsonResponse|Response
     */
    public function formatDataForAjax(Request $request, mixed $model, ?string $pageName = null)
    {
        $pageName ??= $this->data['model_plural'] ?? 'page';

        $result = [];

        $this->data['page'] = $request->has('page') ? $request->page : 1;

        $this->data['paginate'] = $model->paginate(NUMBER_OF_RECORDS_PER_PAGE, ['*'], $pageName, $this->data['page']);

        foreach ($this->data['paginate'] as $modelItem) {
            $result['items'][] = $modelItem->formAjaxArray();
        }

        if ($request->has('is_select') && $request->is_select == 'true') {
            $result['results'] = $result['items'] ?? [];
            unset($result['items']);

            $result['pagination']['more'] = $this->data['paginate']->hasMorePages();
            $result['total'] = $this->data['paginate']->total();

            return response()->json($result);
        }

        $result['pagination'] = new PaginateResource($this->data['paginate']);

        return app('response')
            ->success()
            ->withDefaultMessage('data_fetched_success')
            ->withData($result)
            ->send(isInternal: false, asAjax: true);
    }
}
