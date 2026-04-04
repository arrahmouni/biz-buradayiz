<?php

namespace Modules\Platform\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Platform\Enums\BillingPeriod;
use Modules\Platform\Models\Package;

class PackageFactory extends Factory
{
    protected $model = Package::class;

    public function definition(): array
    {
        return [
            'price' => fake()->randomFloat(2, 10, 500),
            'currency' => 'TRY',
            'billing_period' => fake()->randomElement(BillingPeriod::cases()),
            'sort_order' => 0,
            'connections_count' => fake()->numberBetween(1, 50),
            'is_free_tier' => false,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Package $package) {

            $localeMapping = [
                'en' => 'en_US',
                // 'ar' => 'ar_SA',
                'tr' => 'tr_TR',
            ];
            foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties) {
                $fakerLocale = $localeMapping[$localeCode] ?? 'en_US';
                $faker = \Faker\Factory::create($fakerLocale);

                $package->translations()->create([
                    'locale' => $localeCode,
                    'name' => $faker->name,
                    'slug' => $faker->slug,
                    'description' => $faker->text,
                    'features' => $faker->text,
                ]);
            }
        });
    }
}
