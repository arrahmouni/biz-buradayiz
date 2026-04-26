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

        $newProviderHours = (int) getSetting(Constatnt::NEW_PROVIDER_HOURS, 24);
        $newProviders = collect();

        if ($newProviderHours > 0) {
            $cutoff = now()->subHours($newProviderHours);

            $newProviders = (clone $baseQuery)
                ->whereNotNull('approved_at')
                ->where('approved_at', '>=', $cutoff)
                ->orderByDesc('approved_at')
                ->limit($count)
                ->get();
        }

        $remaining = $count - $newProviders->count();

        if ($remaining <= 0) {
            return $newProviders;
        }

        $excludeIds = $newProviders->pluck('id')->all();

        $rest = (clone $baseQuery)
            ->when($excludeIds !== [], fn (Builder $q) => $q->whereNotIn('id', $excludeIds))
            ->whereNotNull('approved_at')
            ->orderByDesc('ranking_score')
            ->limit($remaining)
            ->get();

        return $newProviders->merge($rest);
    }
}
