<?php

namespace Modules\Base\Http\Services\Traits;

trait ApiServiceTrait
{
    /**
     * Apply custom scopes to API query.
     * Override this method in child services to add custom scopes.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyApiScopes($query, array $data)
    {
        return $query;
    }

    /**
     * Apply custom conditions (where clauses) to API query.
     * Override this method in child services to add custom conditions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyApiConditions($query, array $data)
    {
        return $query;
    }

    /**
     * Handle collection query logic for API.
     * Override this method in child services to add custom collection logic (e.g., latest()).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function handleApiCollection($query, array $data)
    {
        return $query;
    }

    /**
     * Handle single item query logic for API.
     * Override this method in child services for complex single item logic.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $data
     * @return mixed
     */
    protected function handleApiSingle($query, array $data)
    {
        if (!isset($data['id'])) {
            throw new \InvalidArgumentException('ID is required when isCollection is false');
        }

        return $query->findOrFail($data['id']);
    }

    /**
     * Get data for API
     * This is a generic implementation that can be overridden in child services
     *
     * @param array $data
     * @param bool $isCollection
     * @return mixed
     */
    public function getDataForApi(array $data, bool $isCollection = false)
    {
        if (!$this->modelClass) {
            throw new \RuntimeException('Model class not set in service');
        }

        $modelCollection = $this->modelClass::query();

        // Apply custom scopes
        $modelCollection = $this->applyApiScopes($modelCollection, $data);

        // Apply relations from $apiRelations property if it exists
        if (property_exists($this, 'apiRelations') && !empty($this->apiRelations)) {
            $modelCollection = $modelCollection->with($this->apiRelations);
        }

        // Apply custom conditions
        $modelCollection = $this->applyApiConditions($modelCollection, $data);

        if($isCollection) {
            // Handle search
            if (isset($data['q']) && !empty($data['q'])) {
                $term = trim($data['q']);
                if (method_exists($this->modelClass, 'scopeSimpleSearch')) {
                    $modelCollection = $modelCollection->simpleSearch($term);
                }
            }

            // Handle collection-specific logic
            return $this->handleApiCollection($modelCollection, $data);
        }

        // Handle single item logic
        return $this->handleApiSingle($modelCollection, $data);
    }
}
