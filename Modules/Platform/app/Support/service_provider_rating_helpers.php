<?php

use Modules\Platform\Support\ServiceProviderRatingHelper;

if (! function_exists('sync_service_provider_rating')) {
    /**
     * Recalculate and persist approved-review rating aggregates for a service provider user.
     */
    function sync_service_provider_rating(int $userId): void
    {
        ServiceProviderRatingHelper::syncStoredRatingForUser($userId);
    }
}
