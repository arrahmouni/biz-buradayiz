<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Modules\Config\Constatnt;
use Modules\Config\Support\SettingsSnapshot;
use Modules\Zms\Models\City;
use Modules\Zms\Models\Country;
use Modules\Zms\Models\State;

if (! function_exists('schemaTableExists')) {

    /**
     * Cache Schema::hasTable calls in-process to avoid repeated metadata queries.
     */
    function schemaTableExists(string $table): bool
    {
        static $exists = [];

        if (! array_key_exists($table, $exists)) {
            $exists[$table] = Schema::hasTable($table);
        }

        return $exists[$table];
    }
}

if (! function_exists('getSetting')) {

    /**
     * Get setting value from the cached all-settings snapshot (one DB round-trip on cold cache).
     */
    function getSetting(string $key, mixed $default = null): mixed
    {
        if (! schemaTableExists('settings')) {
            return $default;
        }

        return SettingsSnapshot::resolveValue($key, $default);
    }
}

if (! function_exists('youtubeEmbedSrcFromUrl')) {

    /**
     * Normalize a YouTube watch, short, or embed URL to a safe iframe embed src, or null if invalid.
     */
    function youtubeEmbedSrcFromUrl(?string $url): ?string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return null;
        }

        if (! preg_match('#^https?://#i', $url)) {
            $url = 'https://'.$url;
        }

        $parts = parse_url($url);
        if ($parts === false || empty($parts['host'])) {
            return null;
        }

        $host = strtolower($parts['host']);
        $allowed = ['youtube.com', 'youtube-nocookie.com', 'youtu.be'];
        $hostOk = false;
        foreach ($allowed as $suffix) {
            if ($host === $suffix || str_ends_with($host, '.'.$suffix)) {
                $hostOk = true;
                break;
            }
        }
        if (! $hostOk) {
            return null;
        }

        $path = trim($parts['path'] ?? '', '/');
        $id = '';

        if (str_contains($host, 'youtu.be') && $path !== '') {
            $id = explode('/', $path)[0];
        } elseif (str_contains($path, 'embed/')) {
            $id = substr($path, (int) strpos($path, 'embed/') + strlen('embed/'));
            $id = explode('/', $id)[0];
        } elseif (str_contains($path, 'shorts/')) {
            $id = substr($path, (int) strpos($path, 'shorts/') + strlen('shorts/'));
            $id = explode('/', $id)[0];
        } else {
            parse_str($parts['query'] ?? '', $query);
            $id = isset($query['v']) ? (string) $query['v'] : '';
        }

        $id = trim($id);
        if ($id === '' || ! preg_match('/^[a-zA-Z0-9_-]{6,}$/', $id)) {
            return null;
        }

        return 'https://www.youtube.com/embed/'.$id;
    }
}

if (! function_exists('ipWhoPayloadForPublicIp')) {

    /**
     * Cached ipwho.is JSON for a public IP, or null for private/invalid IP or API failure.
     *
     * @return array<string, mixed>|null
     */
    function ipWhoPayloadForPublicIp(?string $ip = null): ?array
    {
        $ip = $ip ?? request()->ip();

        if ($ip === null || $ip === '' || ! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return null;
        }

        $cacheKey = 'ipwho_payload:'.sha1($ip);

        return Cache::remember($cacheKey, 86_400, function () use ($ip) {
            try {
                $response = Http::timeout(3)
                    ->connectTimeout(2)
                    ->acceptJson()
                    ->get('https://ipwho.is/'.$ip);
            } catch (Throwable $e) {
                return null;
            }

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();
            if (! is_array($data) || empty($data['success'])) {
                return null;
            }

            return $data;
        });
    }
}

if (! function_exists('matchZmsStateForIpRegion')) {

    /**
     * Match a ZMS state using ipwho "region" (and optional "region_code") against native and translated names.
     */
    function matchZmsStateForIpRegion(Country $country, string $region, ?string $regionCode = null): ?State
    {
        $region = trim($region);
        $regionCode = $regionCode !== null ? trim($regionCode) : null;

        if ($region === '' && ($regionCode === null || $regionCode === '')) {
            return null;
        }

        if ($region !== '') {
            $state = State::query()
                ->where('country_id', $country->id)
                ->where(function ($q) use ($region) {
                    $q->whereRaw('LOWER(native_name) = LOWER(?)', [$region])
                        ->orWhereHas('translations', function ($tq) use ($region) {
                            $tq->whereRaw('LOWER(name) = LOWER(?)', [$region]);
                        });
                })
                ->first();

            if ($state !== null) {
                return $state;
            }

            $escaped = addcslashes($region, '%_\\');

            $state = State::query()
                ->where('country_id', $country->id)
                ->where(function ($q) use ($escaped) {
                    $q->where('native_name', 'LIKE', '%'.$escaped.'%')
                        ->orWhereHas('translations', function ($tq) use ($escaped) {
                            $tq->where('name', 'LIKE', '%'.$escaped.'%');
                        });
                })
                ->first();

            if ($state !== null) {
                return $state;
            }
        }

        if ($regionCode !== null && $regionCode !== '') {
            $code = strtoupper($regionCode);

            return State::query()
                ->where('country_id', $country->id)
                ->where(function ($q) use ($code) {
                    $q->whereRaw('LOWER(native_name) = LOWER(?)', [$code])
                        ->orWhereHas('translations', function ($tq) use ($code) {
                            $tq->whereRaw('LOWER(name) = LOWER(?)', [$code]);
                        });
                })
                ->first();
        }

        return null;
    }
}

if (! function_exists('matchZmsCityForIpCityName')) {

    /**
     * Match a city under a given state using ipwho "city" against native and translated names.
     */
    function matchZmsCityForIpCityName(State $state, string $cityName): ?City
    {
        $cityName = trim($cityName);
        if ($cityName === '') {
            return null;
        }

        $city = City::query()
            ->where('state_id', $state->id)
            ->where(function ($q) use ($cityName) {
                $q->whereRaw('LOWER(native_name) = LOWER(?)', [$cityName])
                    ->orWhereHas('translations', function ($tq) use ($cityName) {
                        $tq->whereRaw('LOWER(name) = LOWER(?)', [$cityName]);
                    });
            })
            ->first();

        if ($city !== null) {
            return $city;
        }

        $escaped = addcslashes($cityName, '%_\\');

        return City::query()
            ->where('state_id', $state->id)
            ->where(function ($q) use ($escaped) {
                $q->where('native_name', 'LIKE', '%'.$escaped.'%')
                    ->orWhereHas('translations', function ($tq) use ($escaped) {
                        $tq->where('name', 'LIKE', '%'.$escaped.'%');
                    });
            })
            ->first();
    }
}

if (! function_exists('matchZmsCityForIpCityNameInCountry')) {

    /**
     * Match a city anywhere in a country when state resolution failed or city sits under another state label.
     */
    function matchZmsCityForIpCityNameInCountry(Country $country, string $cityName): ?City
    {
        $cityName = trim($cityName);
        if ($cityName === '') {
            return null;
        }

        $city = City::query()
            ->whereHas('state', function ($q) use ($country) {
                $q->where('country_id', $country->id);
            })
            ->where(function ($q) use ($cityName) {
                $q->whereRaw('LOWER(native_name) = LOWER(?)', [$cityName])
                    ->orWhereHas('translations', function ($tq) use ($cityName) {
                        $tq->whereRaw('LOWER(name) = LOWER(?)', [$cityName]);
                    });
            })
            ->first();

        if ($city !== null) {
            return $city;
        }

        $escaped = addcslashes($cityName, '%_\\');

        return City::query()
            ->whereHas('state', function ($q) use ($country) {
                $q->where('country_id', $country->id);
            })
            ->where(function ($q) use ($escaped) {
                $q->where('native_name', 'LIKE', '%'.$escaped.'%')
                    ->orWhereHas('translations', function ($tq) use ($escaped) {
                        $tq->where('name', 'LIKE', '%'.$escaped.'%');
                    });
            })
            ->first();
    }
}

if (! function_exists('resolveFrontSearchDefaultCountryIdFromIp')) {

    /**
     * Resolve the default front search country id from the client IP: geolocate to ISO2,
     * match a row in countries, otherwise use the front_search_default_country_id setting.
     */
    function resolveFrontSearchDefaultCountryIdFromIp(?string $ip = null): int
    {
        $fallback = (int) getSetting(Constatnt::FRONT_SEARCH_DEFAULT_COUNTRY_ID, 225);
        $data = ipWhoPayloadForPublicIp($ip);

        if ($data === null || empty($data['country_code'])) {
            return $fallback;
        }

        if (! schemaTableExists('countries')) {
            return $fallback;
        }

        $iso2 = strtoupper((string) $data['country_code']);

        $country = Country::query()
            ->where('iso2', $iso2)
            ->first();

        return $country !== null ? (int) $country->id : $fallback;
    }
}

if (! function_exists('getStateFromIp')) {

    /**
     * Resolve a state/province record from the client IP using ipwho.is region data and the states table.
     */
    function getStateFromIp(?string $ip = null): ?State
    {
        $data = ipWhoPayloadForPublicIp($ip);

        if ($data === null || empty($data['country_code'])) {
            return null;
        }

        if (! schemaTableExists('countries') || ! schemaTableExists('states')) {
            return null;
        }

        $country = Country::query()
            ->where('iso2', strtoupper((string) $data['country_code']))
            ->first();

        if ($country === null) {
            return null;
        }

        $region = (string) ($data['region'] ?? '');
        $regionCode = isset($data['region_code']) ? (string) $data['region_code'] : null;

        return matchZmsStateForIpRegion($country, $region, $regionCode);
    }
}

if (! function_exists('getCityFromIp')) {

    /**
     * Resolve a city record from the client IP using ipwho.is city data and the cities table.
     */
    function getCityFromIp(?string $ip = null): ?City
    {
        $data = ipWhoPayloadForPublicIp($ip);
        if ($data === null || empty($data['country_code'])) {
            return null;
        }

        if (! schemaTableExists('countries') || ! schemaTableExists('cities')) {
            return null;
        }

        $country = Country::query()
            ->where('iso2', strtoupper((string) $data['country_code']))
            ->first();

        if ($country === null) {
            return null;
        }

        $cityName = trim((string) ($data['city'] ?? ''));
        if ($cityName === '') {
            return null;
        }

        $region = (string) ($data['region'] ?? '');
        $regionCode = isset($data['region_code']) ? (string) $data['region_code'] : null;

        $state = matchZmsStateForIpRegion($country, $region, $regionCode);

        if ($state !== null) {
            $city = matchZmsCityForIpCityName($state, $cityName);
            if ($city !== null) {
                return $city;
            }
        }

        return matchZmsCityForIpCityNameInCountry($country, $cityName);
    }
}

if (! function_exists('phoneToTelHref')) {

    /**
     * Build a tel: URI from a human-entered phone string (digits and optional leading +).
     */
    function phoneToTelHref(string $phone): string
    {
        $clean = preg_replace('/[^0-9+]/', '', trim($phone));

        if ($clean === '' || $clean === '+') {
            return '#';
        }

        return 'tel:'.$clean;
    }
}
