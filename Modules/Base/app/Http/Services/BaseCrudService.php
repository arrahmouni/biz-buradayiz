<?php

namespace Modules\Base\Http\Services;

use OwenIt\Auditing\Auditable;
use Modules\Base\Events\UpdateTranslationEvent;

class BaseCrudService extends BaseService
{
    /**
     * Model class name. Should be set in child services.
     */
    protected $modelClass;

    /**
     * Model scopes/relations to apply when getting model.
     * Can be array of relation names or empty array.
     * Example: ['abilities', 'roles']
     */
    protected $modelScopes = [];

    public function __construct() {
        parent::__construct();
    }

    /**
     * Create translations array for the model.
     *
     * @param array $data
     * @param string $titleField
     * @param string|null $descriptionField
     * @param string|null $longDescriptionField
     * @return array
     */
    protected function createTranslations(array $data, string $titleField, array $otherFields = []) : array
    {
        if(!isset($data[$titleField]) || empty($data[$titleField])) {
            return [];
        }

        $translations = [];

        foreach($data[$titleField] as $locale => $value) {
            if(!empty($value)) {
                $translations[$titleField . ':' . $locale] = $value;

                foreach($otherFields as $field) {
                    $translations[$field . ':' . $locale] = $data[$field][$locale] ?? null;
                }
            }
        }

        return $translations;
    }

    /**
     * Update translations for the model.
     *
     * @param mixed $model
     * @param array $data
     * @param string $titleField
     * @param string|null $descriptionField
     * @return void
     */
    protected function updateTranslations(mixed $model, array $data, string $titleField, array $otherFields = []): void
    {
        if (!isset($data[$titleField]) || empty($data[$titleField])) {
            return;
        }

        $locales            = [];
        $oldTranslations    = $model->getTranslationsArray();
        $modifiedOldValues  = [];
        $modifiedNewValues  = [];

        foreach ($data[$titleField] as $locale => $value) {
            if (!empty($value)) {
                $locales[] = $locale;
                $model->{$titleField . ':' . $locale} = $value;

                foreach (array_merge([$titleField], $otherFields) as $field) {
                    $newValue = $data[$field][$locale] ?? null;
                    $oldValue = $oldTranslations[$locale][$field] ?? null;

                    if ($oldValue !== $newValue) {
                        $modifiedOldValues[$locale][$field] = $oldValue;
                        $modifiedNewValues[$locale][$field] = $newValue;
                    }

                    $model->{$field . ':' . $locale} = $newValue;
                }
            }
        }

        // Remove locales not present in the current data
        $model->translations()->whereNotIn('locale', $locales)->delete();

        $model->save();

        if (!empty($modifiedOldValues) && config('audit.enabled') && in_array(Auditable::class, class_uses($model))) {
            event(new UpdateTranslationEvent($model, $modifiedOldValues, $modifiedNewValues));
        }
    }

    /**
     * Upload image for the model.
     *
     * @param array $data
     * @param mixed $model
     * @param string $collection
     * @param string $field
     * @return mixed
     */
    protected function uploadImageForModel(mixed $model, array $data, string $collection, string $field = 'image'): mixed
    {
        if(isset($data[$field])) {
            $media = $model->getFirstMedia($collection);

            if($media) {
                $media->delete();
            }

            $model->addMedia($data[$field])
                ->toMediaCollection($collection);
        }

        return $model;
    }

    /**
     * Upload image for the translation model.
     *
     * @param array $data
     * @param mixed $model
     * @param string $collection
     * @param string $field
     *
     * @return mixed
     */
    protected function uploadImageForTransModel(mixed $model, array $data, string $collection, string $field = 'image'): mixed
    {
        if(isset($data[$field]) && is_array($data[$field])) {
            foreach($data[$field] as $locale => $image) {
                $translationModel = $model->translations()
                ->where('locale', $locale)
                ->first();

                if($translationModel) {
                    $media = $translationModel->getFirstMedia($collection);

                    if($media) {
                        $media->delete();
                    }

                    $translationModel->addMedia($image)
                    ->withCustomProperties(['locale' => $locale])
                    ->toMediaCollection($collection);
                }
            }
        }

        return $model;
    }

    /**
     * If can view trash and the data has trash key and it's value is show
     *
     * @param array $data
     * @param string $permissionAction
     * @return bool
     */
    protected function shouldShowTrash(array $data, string $permissionAction) : bool
    {
        return isset($data['trash']) && $data['trash'] == 'show' && (app('owner') || app('admin')->can($permissionAction));
    }

    /**
     * Check if the model has withDisabled support (Disableable trait)
     *
     * @return bool
     */
    protected function hasWithDisabled(): bool
    {
        if (!$this->modelClass) {
            return false;
        }

        return method_exists($this->modelClass, 'getDisabledAtColumn') ||
               in_array('Modules\Base\Trait\Disableable', class_uses_recursive($this->modelClass));
    }

    /**
     * Apply custom scopes to the query.
     * Override this method in child services to add custom scopes.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyModelScopes($query)
    {
        return $query;
    }

    /**
     * Get model by ID with optional trashed and disabled records
     *
     * @param int $id
     * @param bool $withTrashed
     * @param bool $withDisabled
     * @return mixed
     */
    public function getModel(int $id, bool $withTrashed = false, bool $withDisabled = false)
    {
        if (!$this->modelClass) {
            throw new \RuntimeException('Model class not set in service');
        }

        $model = $this->modelClass::query();

        // Apply custom scopes from hook method
        $model = $this->applyModelScopes($model);

        // Apply relations from $modelScopes property
        if (!empty($this->modelScopes)) {
            $model = $model->with($this->modelScopes);
        }

        if($withTrashed) {
            $model = $model->withTrashed();
        }

        if($withDisabled) {
            $model = $model->withDisabled();
        }

        return $model->findOrFail($id);
    }
}
