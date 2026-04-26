<?php

namespace Modules\Front\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\Config\Constatnt;

class FeaturedProviderService
{
    public function getFeatured(Builder $baseQuery, int $count): Collection
    {
        if ($count <= 0) {
            return collect();
        }

        $eagerLoadKeys = array_keys($baseQuery->getEagerLoads());
        $queryWithoutEager = (clone $baseQuery)->withoutEagerLoads();

        $newProviderHours = (int) getSetting(Constatnt::NEW_PROVIDER_HOURS, 24);
        $newProviders = collect();

        if ($newProviderHours > 0) {
            $cutoff = now()->subHours($newProviderHours);

            $newProviders = (clone $queryWithoutEager)
                ->where('approved_at', '>=', $cutoff)
                ->orderByDesc('approved_at')
                ->limit($count)
                ->get();
        }

        $remaining = $count - $newProviders->count();

        if ($remaining <= 0) {
            if ($newProviders->isNotEmpty() && $eagerLoadKeys !== []) {
                $newProviders->load($eagerLoadKeys);
            }

            return $newProviders;
        }

        $excludeIds = $newProviders->pluck('id')->all();

        $rest = (clone $queryWithoutEager)
            ->when($excludeIds !== [], fn (Builder $q) => $q->whereNotIn('id', $excludeIds))
            ->orderByDesc('ranking_score')
            ->limit($remaining)
            ->get();

        $merged = $newProviders->merge($rest);
        if ($merged->isNotEmpty() && $eagerLoadKeys !== []) {
            $merged->load($eagerLoadKeys);
        }

        return $merged;
    }
}
