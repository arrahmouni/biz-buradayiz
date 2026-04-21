<?php

namespace Modules\Front\Support;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Modules\Base\Http\Services\BaseFrontService;
use Modules\Platform\Models\Service;

class FrontPublicServices extends BaseFrontService
{
    public const SERVICES_WITH_PACKAGES_CACHE_KEY = 'servicesWithPackages';

    public const CACHE_TTL_SECONDS = 3600;

    /**
     * All enabled, non-deleted services (not limited to search filters).
     *
     * @return list<array{id: int, name: string, description: string|null}>
     */
    public static function all(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();

        return Cache::remember(
            self::cacheKey('all', $locale),
            self::CACHE_TTL_SECONDS,
            fn () => self::load($locale, fn ($q) => $q)
        );
    }

    /**
     * Services configured to appear in public search filters.
     *
     * @return list<array{id: int, name: string, description: string|null}>
     */
    public static function forSearchFilters(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();

        return Cache::remember(
            self::cacheKey('filters', $locale),
            self::CACHE_TTL_SECONDS,
            fn () => self::load($locale, fn ($q) => $q->forSearchFilters())
        );
    }

    public static function flush(): void
    {
        Cache::forget(self::SERVICES_WITH_PACKAGES_CACHE_KEY);

        foreach ((array) config('translatable.locales', ['en']) as $locale) {
            Cache::forget(self::cacheKey('all', $locale));
            Cache::forget(self::cacheKey('filters', $locale));
        }
    }

    private static function cacheKey(string $kind, string $locale): string
    {
        return 'front.public_services.'.$kind.'.'.$locale;
    }

    /**
     * @param  Closure(Builder): Builder  $scope
     * @return list<array{id: int, name: string, description: string|null}>
     */
    private static function load(string $locale, Closure $scope): array
    {
        $previous = app()->getLocale();
        app()->setLocale($locale);

        logger()->info('Loading front public services for locale: '.$locale);

        try {
            $query = Service::query()->orderBy('id');
            $query = $scope($query);

            return $query->get()->map(static function (Service $service) {
                $name = $service->smartTrans('name');
                $description = $service->smartTrans('description');

                return [
                    'id' => (int) $service->id,
                    'icon' => $service->icon ?? 'fas fa-building',
                    'name' => ! empty($name) ? $name : (string) $service->id,
                    'description' => ! empty($description) ? $description : null,
                ];
            })->values()->all();
        } finally {
            app()->setLocale($previous);
        }
    }
}
