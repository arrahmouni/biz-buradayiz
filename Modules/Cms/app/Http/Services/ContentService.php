<?php

namespace Modules\Cms\Http\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\Base\Http\Services\BaseCrudService;
use Modules\Cms\Models\Content as CrudModel;
use Yajra\DataTables\Facades\DataTables;

class ContentService extends BaseCrudService
{
    protected $modelClass = CrudModel::class;
    /**
     * The unnecessary fields for crud.
     * Example: if the data has translation fields, you can add them here. As a ('title', 'description')
     */
    protected $unnecessaryFieldsForCrud = [
        'title',
        'short_description',
        'long_description',
        'image',
        'placement',
        'appear_in_footer',
        'tags',
    ];

    /**
     * The custom properties for the model to store in custom_properties attribute.
     */
    protected $customProperties = [
        'placement',
        'appear_in_footer',
    ];

    /**
     * Create a new Model instance.
     *
     * @param array $data
     * @return CrudModel
     */
    public function createModel(array $data): CrudModel
    {
        $modelData    = $this->prepareModelData($data);
        $translations = $this->createTranslations($data, 'title', ['short_description', 'long_description']);

        $model = DB::transaction(function () use($data, $modelData, $translations){
            // Create the model instance
            $model = CrudModel::create($modelData);

            // Store custom properties
            $this->createOrUpdateCustomProperties($model, $data);

            // Store Content Tags
            $this->storeContentTags($model, $data);

            // Create translations
            $model->update($translations);

            // Upload image
            $this->uploadImageForTransModel($model, $data, CrudModel::MEDIA_COLLECTION);

            return $model;
        });

        return $model;
    }

    /**
     * Update a Model instance.
     *
     * @param CrudModel $model
     * @param array $data
     * @return CrudModel
     */
    public function updateModel(CrudModel $model, array $data) : CrudModel
    {
        $modelData = $this->prepareModelData($data);

        DB::transaction(function () use($data, $model, $modelData) {
            // Update the model instance
            $model->update($modelData);

            // Update custom properties
            $this->createOrUpdateCustomProperties($model, $data);

            // Store Content Tags
            $this->storeContentTags($model, $data);

            // Update translations
            $this->updateTranslations($model, $data, 'title', ['short_description', 'long_description']);

            // Upload image
            $this->uploadImageForTransModel($model, $data, CrudModel::MEDIA_COLLECTION);
        });

        return $model;
    }

    /**
     * Store the custom properties for the model.
     *
     * @param CrudModel $model
     * @param array $data
     * @return void
     */
    private function createOrUpdateCustomProperties(CrudModel $model, array $data)
    {
        $customProperties = [];

        foreach ($this->customProperties as $property) {
            if (! array_key_exists($property, $data)) {
                continue;
            }

            $value = $data[$property];
            if ($property === 'appear_in_footer') {
                $value = filter_var($value, FILTER_VALIDATE_BOOL);
            }

            $customProperties[$property] = $value;
        }

        if ($customProperties === []) {
            return;
        }

        $model->custom_properties = array_merge($model->custom_properties ?? [], $customProperties);
        $model->save();
    }

    /**
     * Store Content Tags for the model.
     *
     * @param CrudModel $model
     * @param array $data
     * @return CrudModel
     */
    private function storeContentTags(CrudModel $model, array $data)
    {
        if(isset($data['tags'])) {
            $model->tags()->sync($data['tags']);
        } else {
            $model->tags()->sync([]);
        }

        return $model;
    }


    public function getDataTable(array $data) : JsonResponse
    {

        $model = CrudModel::query();

        if($this->hasWithDisabled()) {
            $model = $model->withDisabled();
        }

        $model = $model->byType($data['type']);
        $permissionClass    = 'Modules\\Cms\\Enums\\permissions\\' . $data['type'] . 'Permissions';

        if($this->shouldShowTrash($data, $permissionClass::VIEW_TRASH)) {
            $model = $model->onlyTrashed();
        }

        return DataTables::of($model)
            ->filter(function ($query) use ($data) {
                if(isset($data['search']['value']) && !empty($data['search']['value'])){
                    $query->simpleSearch($data['search']['value'], $data['type']);
                }
                if(isset($data['advanced_search']) && !empty($data['advanced_search'])){
                    $query->advancedSearch($data['advanced_search'], $data['type']);
                }
            })
            ->addColumn('placement', function ($model) {
                return $model->placement_position;
            })
            ->addColumn('image_url', function ($model) {
                if(CrudModel::typeHasField($model->type, 'image')) {
                    return $model->transImageUrl(CrudModel::MEDIA_COLLECTION, app()->getLocale(), 'thumb-100');
                }
            })
            ->addColumn('orginal_image_url', function ($model) {
                if(CrudModel::typeHasField($model->type, 'image')) {
                    return $model->transImageUrl(CrudModel::MEDIA_COLLECTION, app()->getLocale());
                }
            })
            ->addColumn('actions', function ($model) use($data, $permissionClass) {
                $excludeActions = [VIEW_ACTION];

                if(! $model->can_be_deleted) {
                    $excludeActions = array_merge($excludeActions, [SOFT_DELETE_ACTION, DISABLE_ACTION]);
                }

                return
                    app('customDataTable')
                    ->routePrefix('cms.contents')
                    ->setRouteParameters(['type' => $data['type']])
                    ->of($model, $permissionClass::PERMISSION_NAMESPACE)
                    ->excludeActions($excludeActions)
                    ->getDatatableActions();
            })
            ->toJson();
    }

    public function getDataForApi($data, $isCollection = false)
    {
        $modelCollection = CrudModel::byType($data['type'] ?? null);

        if($isCollection) {
            if (isset($data['q']) && !empty($data['q'])) {
                $term = trim($data['q']);

                $modelCollection = $modelCollection->simpleSearch($term, $data['type'] ?? null);
            }

            return $modelCollection;
        }

        if(isset($data['slug'])) {
            $model = CrudModel::findBySlug($data['slug']);
        }

        if(! isset($model) || empty($model)) {
            $model = CrudModel::byType($data['type'])->findOrFail($data['slug']);
        }

        return $model;
    }
}
