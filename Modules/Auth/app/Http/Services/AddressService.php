<?php

namespace Modules\Auth\Http\Services;

use Illuminate\Support\Facades\DB;
use Modules\Auth\Models\Address as CrudModel;
use Modules\Base\Http\Services\BaseCrudService;
use Modules\Base\Http\Services\Traits\ApiServiceTrait;

class AddressService extends BaseCrudService
{
    use ApiServiceTrait;

    protected $modelClass = CrudModel::class;

    protected $apiRelations = ['country', 'state', 'city'];

    /**
     * Create a new Model instance.
     *
     * @param array $data
     * @return CrudModel
     */
    public function createModel(array $data): CrudModel
    {
        $modelData = $this->prepareModelData($data);

        $model = DB::transaction(function () use($modelData) {
            // Create the model
            $model = CrudModel::create($modelData);

            $model->load('country', 'state', 'city');

            return $model;
        });

        return $model;
    }

    /**
     * Update the Model instance.
     */
    public function updateModel(CrudModel $model, array $data): CrudModel
    {
        $modelData = $this->prepareModelData($data);

        $model = DB::transaction(function () use($model, $modelData) {
            // Update the model
            $model->update($modelData);

            $model->load('country', 'state', 'city');

            return $model;
        });

        return $model;
    }

    protected function applyApiConditions($query, array $data)
    {
        if (isset($data['user_id'])) {
            return $query->where('user_id', $data['user_id']);
        }

        return $query;
    }

    protected function handleApiCollection($query, array $data)
    {
        return $query->latest();
    }
}
