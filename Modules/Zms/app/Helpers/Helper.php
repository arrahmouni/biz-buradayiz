<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Modules\Zms\Models\Country;

if (! function_exists('getCountryInfo')) {
    /**
     * Get the country information.
     */
    function getCountryInfo($iso3 = 'TUR', $default = 'Turkey')
    {
        $iso3 = strtoupper($iso3);

        $country = Cache::get($iso3);

        if (! Schema::hasTable('countries')) {
            return $default;
        }

        if (! $country) {
            $country = Country::where('iso3', $iso3)->first();

            Cache::forever($iso3, $country);
        }

        return $country;
    }
}

if (! function_exists('getCurrencySymbol')) {
    /**
     * Currency symbol for display: match package/store currency when possible, else default country from getCountryInfo().
     */
    function getCurrencySymbol(?string $currencyCode = null, string $iso3 = 'TUR'): string
    {
        if (Schema::hasTable('countries')) {
            if (filled($currencyCode)) {
                $code = strtoupper($currencyCode);
                $byCurrency = Country::where('currency', $code)->first();
                if ($byCurrency) {
                    return filled($byCurrency->currency_symbol)
                        ? (string) $byCurrency->currency_symbol
                        : (string) $byCurrency->currency;
                }
            }

            $country = getCountryInfo($iso3);
            if ($country instanceof Country) {
                return filled($country->currency_symbol)
                    ? (string) $country->currency_symbol
                    : (string) $country->currency;
            }
        }

        return filled($currencyCode) ? strtoupper($currencyCode) : 'TRY';
    }
}

if (! function_exists('getCurreny')) {
    /**
     * Get the currency symbol.
     */
    function getCurreny($iso3 = 'TUR', $default = 'TRY')
    {
        $iso3 = strtoupper($iso3);

        $currency = Cache::get($iso3.'_currency');

        if (! Schema::hasTable('countries')) {
            return $default;
        }

        if (! $currency) {
            $country = getCountryInfo($iso3);
            $currency = $country->currency;

            Cache::forever($iso3.'_currency', $currency);
        }

        return $currency;
    }
}

if (! function_exists('getCountryPhoneCode')) {
    /**
     * Get the country phone code.
     */
    function getCountryPhoneCode($iso3 = 'TUR', $default = '90')
    {
        $iso3 = strtoupper($iso3);

        $phoneCode = Cache::get($iso3.'_phone_code');

        if (! Schema::hasTable('countries')) {
            return $default;
        }

        if (! $phoneCode) {
            $country = getCountryInfo($iso3);
            $phoneCode = $country->phone_code;

            Cache::forever($iso3.'_phone_code', $phoneCode);
        }

        return $phoneCode;
    }
}

if (! function_exists('getCurrencySelectOptions')) {
    /**
     * Distinct currency codes from countries for select options (code => label).
     *
     * @return array<string, string>
     */
    function getCurrencySelectOptions(): array
    {
        $cacheKey = 'country_currency_select_options';

        $options = Cache::get($cacheKey);

        if ($options !== null) {
            return $options;
        }

        if (! Schema::hasTable('countries')) {
            $options = ['TRY' => 'TRY'];
            Cache::forever($cacheKey, $options);

            return $options;
        }

        $rows = Country::query()
            ->whereNotNull('currency')
            ->where('currency', '!=', '')
            ->orderBy('currency')
            ->get(['currency', 'currency_symbol']);

        if ($rows->isEmpty()) {
            $options = ['TRY' => 'TRY'];
        } else {
            $options = $rows->unique('currency')->mapWithKeys(function (Country $country) {
                $code = $country->currency;
                $label = $code;
                if (! empty($country->currency_symbol)) {
                    $label .= ' ('.$country->currency_symbol.')';
                }

                return [$code => $label];
            })->all();
        }

        Cache::forever($cacheKey, $options);

        return $options;
    }
}
