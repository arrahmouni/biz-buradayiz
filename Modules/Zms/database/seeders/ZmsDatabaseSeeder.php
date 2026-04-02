<?php

namespace Modules\Zms\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Zms\Models\City;
use Modules\Zms\Models\State;
use Modules\Zms\Models\Country;
use Modules\Zms\Models\CityTranslation;
use Modules\Zms\Models\StateTranslation;
use Modules\Zms\Models\CountryTranslation;

class ZmsDatabaseSeeder extends Seeder
{
    /**
     * Only geography for this ISO 3166-1 alpha-2 country is seeded (TR = Türkiye).
     * Use an empty string to seed every country from the template files (large import).
     */
    private const SEEDED_COUNTRY_ISO2 = 'TR';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedCountriesAndStatesAndCities();
    }

    public function seedCountriesAndStatesAndCities()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $countries  = Country::count();
        $states     = State::count();

        if ($countries > 0 || $states > 0) {
            return;
        }

        $countries              = require module_path('Zms', 'database/seederTemplates/countries.php');
        $countryTranslations    = require module_path('Zms', 'database/seederTemplates/country_translations.php');
        $states                 = require module_path('Zms', 'database/seederTemplates/states.php');
        $stateTranslations      = require module_path('Zms', 'database/seederTemplates/state_translations.php');
        $cities                 = require module_path('Zms', 'database/seederTemplates/cities.php');
        $cityTranslations       = require module_path('Zms', 'database/seederTemplates/city_translations.php');

        if (self::SEEDED_COUNTRY_ISO2 !== '') {
            [
                $countries,
                $countryTranslations,
                $states,
                $stateTranslations,
                $cities,
                $cityTranslations,
            ] = $this->filterTemplatesToCountry(
                $countries,
                $countryTranslations,
                $states,
                $stateTranslations,
                $cities,
                $cityTranslations,
                self::SEEDED_COUNTRY_ISO2,
            );
        }

        foreach(collect($countries)->chunk(100) as $chunkedCountries) {
            Country::insert($chunkedCountries->toArray());
        }
        foreach(collect($countryTranslations)->chunk(100) as $chunkedCountryTranslations) {
            CountryTranslation::insert($chunkedCountryTranslations->toArray());
        }
        foreach(collect($states)->chunk(100) as $chunkedStates) {
            State::insert($chunkedStates->toArray());
        }
        foreach(collect($stateTranslations)->chunk(100) as $chunkedStateTranslations) {
            StateTranslation::insert($chunkedStateTranslations->toArray());
        }
        foreach(collect($cities)->chunk(100) as $chunkedCities) {
            City::insert($chunkedCities->toArray());
        }
        foreach(collect($cityTranslations)->chunk(100) as $chunkedCityTranslations) {
            CityTranslation::insert($chunkedCityTranslations->toArray());
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $countries
     * @param  array<int, array<string, mixed>>  $countryTranslations
     * @param  array<int, array<string, mixed>>  $states
     * @param  array<int, array<string, mixed>>  $stateTranslations
     * @param  array<int, array<string, mixed>>  $cities
     * @param  array<int, array<string, mixed>>  $cityTranslations
     * @return array{0: array, 1: array, 2: array, 3: array, 4: array, 5: array}
     */
    private function filterTemplatesToCountry(
        array $countries,
        array $countryTranslations,
        array $states,
        array $stateTranslations,
        array $cities,
        array $cityTranslations,
        string $iso2,
    ): array {
        $iso2 = strtoupper($iso2);
        $countries = array_values(array_filter(
            $countries,
            static fn (array $row): bool => ($row['iso2'] ?? '') === $iso2,
        ));

        if ($countries === []) {
            throw new \RuntimeException("Zms seeder: no country with iso2 [{$iso2}] in countries template.");
        }

        $countryId = (int) $countries[0]['id'];
        $countryTranslations = array_values(array_filter(
            $countryTranslations,
            static fn (array $row): bool => (int) $row['country_id'] === $countryId,
        ));

        $states = array_values(array_filter(
            $states,
            static fn (array $row): bool => (int) $row['country_id'] === $countryId,
        ));
        $stateIds = array_fill_keys(array_column($states, 'id'), true);

        $stateTranslations = array_values(array_filter(
            $stateTranslations,
            static fn (array $row): bool => isset($stateIds[(int) $row['state_id']]),
        ));

        $cities = array_values(array_filter(
            $cities,
            static fn (array $row): bool => isset($stateIds[(int) $row['state_id']]),
        ));
        $cityIds = array_fill_keys(array_column($cities, 'id'), true);

        $cityTranslations = array_values(array_filter(
            $cityTranslations,
            static fn (array $row): bool => isset($cityIds[(int) $row['city_id']]),
        ));

        return [
            $countries,
            $countryTranslations,
            $states,
            $stateTranslations,
            $cities,
            $cityTranslations,
        ];
    }
}
