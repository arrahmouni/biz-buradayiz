<?php

namespace Modules\Front\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class FeaturedProviderService
{
    public function getFeatured(Builder $baseQuery, int $count): Collection
    {
        if ($count <= 0) {
            return collect();
        }

        $newProviderHours = (int) getSetting('new_provider_hours', 24);

        if ($newProviderHours > 0) {
            $cutoff = now()->subHours($newProviderHours);

            $newProviders = (clone $baseQuery)
                ->whereNotNull('approved_at')
                ->where('approved_at', '>=', $cutoff)
                ->orderByDesc('approved_at')
                ->limit($count)
                ->get();

            if ($newProviders->count() >= $count) {
                return $newProviders;
            }
        }

        return (clone $baseQuery)
            ->orderByDesc('ranking_score')
            ->limit($count)
            ->get();
    }
}
