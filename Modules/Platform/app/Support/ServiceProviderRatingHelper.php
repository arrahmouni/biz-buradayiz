<?php

namespace Modules\Platform\Support;

use Modules\Auth\Models\User;
use Modules\Platform\Models\Review;

final class ServiceProviderRatingHelper
{
    /**
     * Aggregate rating from approved, non-deleted reviews for a service provider (user).
     *
     * @return array{average: float|null, count: int}
     */
    public static function calculateApprovedAggregate(int $userId): array
    {
        $row = Review::query()
            ->where('user_id', $userId)
            ->approved()
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as review_count')
            ->first();

        $count = (int) ($row->review_count ?? 0);

        if ($count === 0) {
            return ['average' => null, 'count' => 0];
        }

        $average = round((float) $row->avg_rating, 2);

        return ['average' => $average, 'count' => $count];
    }

    public static function syncStoredRatingForUser(int $userId): void
    {
        $aggregate = self::calculateApprovedAggregate($userId);

        User::query()->whereKey($userId)->update([
            'review_rating_average' => $aggregate['average'],
            'approved_reviews_count' => $aggregate['count'],
        ]);
    }
}
