<?php

namespace Modules\Config\Support;

use Illuminate\Support\Facades\Cache;
use Modules\Config\Enums\SettingTypes;
use Modules\Config\Models\Setting;

/**
 * Caches a single in-memory and cache-store snapshot of all settings rows
 * to avoid N+1 queries. Values are resolved at read time for the current locale.
 */
final class SettingsSnapshot
{
    public const CACHE_KEY = 'config.settings.snapshot.v1';

    private static ?array $requestSnapshot = null;

    public static function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
        self::$requestSnapshot = null;
    }

    /**
     * @return array<string, array{type: string, translatable: bool, value: mixed, translations: list<array{locale: string, trans_value: mixed}>}>
     */
    public static function allRows(): array
    {
        if (self::$requestSnapshot !== null) {
            return self::$requestSnapshot;
        }

        self::$requestSnapshot = Cache::rememberForever(
            self::CACHE_KEY,
            function (): array {
                return self::loadFromDatabase();
            }
        );

        return self::$requestSnapshot;
    }

    public static function resolveValue(string $key, mixed $default = null, ?string $locale = null): mixed
    {
        $map = self::allRows();
        if (! array_key_exists($key, $map)) {
            return $default;
        }
        $row = $map[$key];
        $locale ??= app()->getLocale();

        if ($row['value'] === null) {
            return $default;
        }

        if ($row['translatable']) {
            return self::translatedValue($row, $locale);
        }

        if ($row['type'] === SettingTypes::IMAGE || $row['type'] === SettingTypes::FILE) {
            return asset('storage/'.$row['value']);
        }

        return $row['value'];
    }

    private static function translatedValue(array $row, string $locale): mixed
    {
        $translations = $row['translations'] ?? [];
        foreach ($translations as $trans) {
            if (($trans['locale'] ?? null) === $locale) {
                return $trans['trans_value'] ?? null;
            }
        }
        if ($translations === []) {
            return null;
        }
        $first = $translations[0];

        return $first['trans_value'] ?? null;
    }

    private static function loadFromDatabase(): array
    {
        $rows = [];
        $settings = Setting::query()
            ->with('translations')
            ->get();

        foreach ($settings as $data) {
            $rows[$data->key] = [
                'type' => $data->type,
                'translatable' => (bool) $data->translatable,
                'value' => $data->value,
                'translations' => $data->translations
                    ->map(fn ($t) => [
                        'locale' => $t->locale,
                        'trans_value' => $t->trans_value,
                    ])->values()->all(),
            ];
        }

        return $rows;
    }
}
