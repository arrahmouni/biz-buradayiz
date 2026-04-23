<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

if (! function_exists('isProd')) {
    function isProd()
    {
        return app()->environment('production');
    }
}

if (! function_exists('isDev')) {
    function isDev()
    {
        return app()->environment('local') || app()->environment('development') || app()->environment('staging');
    }
}

if (! function_exists('isLocal')) {
    function isLocal()
    {
        return app()->environment('local');
    }
}

if (! function_exists('isStaging')) {
    function isStaging(): bool
    {
        return app()->environment('staging');
    }
}

if (! function_exists('createTranslateArray')) {

    /**
     * To Create Translation Array To Insert Directly When Creating Model
     * ex: ['en' => ['title' => 'Test'], 'ar' => ['title', 'تجربة']]
     */
    function createTranslateArray(string $field, string $key, string $module = 'permission'): array
    {
        $translatedData = [];
        $locales = config('translatable.locales', LaravelLocalization::getSupportedLanguagesKeys());

        foreach ($locales as $langCode) {
            $translatedData[$langCode] = [
                $field => trans("{$module}::".$key, [], $langCode),
            ];
        }

        return $translatedData;
    }
}

if (! function_exists('getRandomColorPair')) {

    /**
     * Get Random Color Pair
     *
     * @return array
     */
    function getRandomColorPair()
    {
        $colors = [
            ['bg-light-danger', 'text-danger'],
            ['bg-light-primary', 'text-primary'],
            ['bg-light-success', 'text-success'],
            ['bg-light-info', 'text-info'],
            ['bg-light-warning', 'text-warning'],
            ['bg-light-dark', 'text-dark'],
        ];

        return $colors[array_rand($colors)];
    }
}

if (! function_exists('getFormattedDate')) {

    /**
     * Get Formatted Date
     */
    function getFormattedDate(?string $date, string $format = 'Y-m-d H:i:s'): string
    {
        return ! empty($date) ? Carbon::parse($date)->locale(app()->getLocale())->translatedFormat($format) : DEFAULT_DATE;
    }
}

if (! function_exists('getFileUrl')) {

    function getFileUrl($path, $default = null, $disk = 'public')
    {
        $fileUrl = $default ? asset($default) : asset('modules/admin/metronic/demo/media/svg/files/blank-image.svg');

        if ($path && Storage::disk($disk)->exists($path)) {
            $fileUrl = Storage::disk($disk)->url($path);
        }

        return $fileUrl;
    }
}

if (! function_exists('getImageTypes')) {

    /**
     * Get Image Types For Img Tag
     */
    function getImageTypes(bool $forDisplay = false, array $types = []): string
    {
        $types = empty($types) ? config('base.file.image.accepted_types') : $types;

        if ($forDisplay) {
            return implode(' | ', array_map(function ($type) {
                return '.'.$type;
            }, $types));
        }

        return implode(', ', array_map(function ($type) {
            return match ($type) {
                'svg' => 'image/svg+xml',
                default => 'image/'.$type,
            };
        }, $types));
    }
}
