<?php

namespace Modules\Platform\Observers;

use Modules\Platform\Models\Review;

class ReviewObserver
{
    public function created(Review $review): void
    {
        sync_service_provider_rating((int) $review->user_id);
    }

    public function updated(Review $review): void
    {
        $previous = $review->getPrevious();
        if (array_key_exists('user_id', $previous)) {
            sync_service_provider_rating((int) $previous['user_id']);
        }

        sync_service_provider_rating((int) $review->user_id);
    }

    public function deleted(Review $review): void
    {
        sync_service_provider_rating((int) $review->user_id);
    }

    public function restored(Review $review): void
    {
        sync_service_provider_rating((int) $review->user_id);
    }

    public function forceDeleted(Review $review): void
    {
        sync_service_provider_rating((int) $review->user_id);
    }
}
