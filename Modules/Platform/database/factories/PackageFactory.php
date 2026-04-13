<?php

namespace Modules\Platform\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Platform\Enums\BillingPeriod;
use Modules\Platform\Models\Package;
use Modules\Platform\Models\Service;

class PackageFactory extends Factory
{
    private const PACKAGES_PER_SERVICE = 3;

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
            'is_popular' => false,
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
                    'description' => Str::limit($faker->words(8, true), 30, ''),
                    'features' => collect([
                        $faker->words(4, true),
                        $faker->words(3, true),
                        $faker->words(5, true),
                    ])->implode(', '),
                ]);
            }

            $eligibleServiceIds = Service::query()
                ->orderBy('id')
                ->withCount('packages')
                ->having('packages_count', '<', self::PACKAGES_PER_SERVICE)
                ->pluck('id')
                ->all();

            if ($eligibleServiceIds !== []) {
                $package->services()->sync($eligibleServiceIds);
            }
        });
    }
}
