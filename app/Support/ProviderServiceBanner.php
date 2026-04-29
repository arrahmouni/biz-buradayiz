<?php

namespace App\Support;

use Modules\Platform\Models\Service;

final class ProviderServiceBanner
{
    /**
     * Default when service is unknown (roadside/automotive).
     */
    public const FALLBACK_FILENAME = 'cekici-01.png';

    /**
     * Filenames relative to {@see publicServicesDirectory()}.
     *
     * @var array<string, string>
     */
    private const FILENAME_BY_ICON = [
        'fas fa-truck' => 'cekici-01.png',
        'fas fa-car' => 'lastik.png',
        'fas fa-car-battery' => 'aku.jpg',
        'fas fa-gas-pump' => 'yakit.jpg',
    ];

    public static function publicServicesDirectory(): string
    {
        return public_path('images/services');
    }

    public static function absolutePathForFilename(string $filename): string
    {
        return public_path('images/services/'.basename($filename));
    }

    /**
     * Resolve which bundled banner file matches the linked service vertical.
     */
    public static function relativeFilenameForService(?Service $service): string
    {
        if ($service === null) {
            return self::FALLBACK_FILENAME;
        }

        $icon = $service->icon;
        if (is_string($icon) && $icon !== '' && isset(self::FILENAME_BY_ICON[$icon])) {
            return self::FILENAME_BY_ICON[$icon];
        }

        $haystack = strtolower((string) (
            $service->translate('en')?->name
            ?? $service->translate('tr')?->name
            ?? ''
        ));

        if ($haystack === '') {
            return self::FALLBACK_FILENAME;
        }

        if (str_contains($haystack, 'tow')
            || str_contains($haystack, 'çekici')
            || str_contains($haystack, 'çekiç')) {
            return self::FILENAME_BY_ICON['fas fa-truck'];
        }
        if (str_contains($haystack, 'tyre') || str_contains($haystack, 'tire') || str_contains($haystack, 'lastik')) {
            return self::FILENAME_BY_ICON['fas fa-car'];
        }
        if (str_contains($haystack, 'battery') || str_contains($haystack, 'akü')) {
            return self::FILENAME_BY_ICON['fas fa-car-battery'];
        }
        if (str_contains($haystack, 'fuel') || str_contains($haystack, 'yakıt')) {
            return self::FILENAME_BY_ICON['fas fa-gas-pump'];
        }

        return self::FALLBACK_FILENAME;
    }

    /**
     * Absolute path to an existing bundled file under public/images/services.
     */
    public static function absolutePathForService(?Service $service): string
    {
        $path = self::absolutePathForFilename(self::relativeFilenameForService($service));

        if (! is_file($path)) {
            $fallback = self::absolutePathForFilename(self::FALLBACK_FILENAME);
            if ($path !== $fallback && is_file($fallback)) {
                return $fallback;
            }

            throw new \RuntimeException("Service banner file not found: {$path}");
        }

        return $path;
    }
}
