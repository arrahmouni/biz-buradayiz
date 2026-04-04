<?php

namespace Modules\Platform\Database\Seeders;

use Illuminate\Database\Seeder;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Platform\Enums\BillingPeriod;
use Modules\Platform\Models\Package;
use Modules\Platform\Models\PackageTranslation;
use Modules\Platform\Models\Service;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $services = Service::query()->orderBy('id')->get();
        if ($services->isEmpty()) {
            return;
        }

        $locales = LaravelLocalization::getSupportedLanguagesKeys();

        $definitions = [
            [
                'price' => 99.00,
                'currency' => 'TRY',
                'billing_period' => BillingPeriod::Monthly,
                'sort_order' => 10,
                'connections_count' => 5,
                'service_ids' => [$services->first()->id],
                'translations' => [
                    'en' => [
                        'name' => 'Starter',
                        'description' => 'Entry plan for a single service line.',
                        'features' => "Profile listing\nBasic support",
                    ],
                    'tr' => [
                        'name' => 'Başlangıç',
                        'description' => 'Tek hizmet hattı için giriş paketi.',
                        'features' => "Profil listesi\nTemel destek",
                    ],
                ],
            ],
            [
                'price' => 249.00,
                'currency' => 'TRY',
                'billing_period' => BillingPeriod::Yearly,
                'sort_order' => 20,
                'connections_count' => 25,
                'service_ids' => $services->take(min(2, $services->count()))->pluck('id')->all(),
                'translations' => [
                    'en' => [
                        'name' => 'Pro',
                        'description' => null,
                        'features' => "All Starter features\nPriority placement",
                    ],
                    'tr' => [
                        'name' => 'Pro',
                        'description' => null,
                        'features' => "Başlangıç özellikleri\nÖncelikli yerleşim",
                    ],
                ],
            ],
        ];

        foreach ($definitions as $def) {
            $translations = $def['translations'];
            $serviceIds = $def['service_ids'];
            unset($def['translations'], $def['service_ids']);

            $package = Package::query()->create($def);

            foreach ($locales as $locale) {
                $t = $translations[$locale] ?? $translations['en'];
                PackageTranslation::query()->create([
                    'package_id' => $package->id,
                    'locale' => $locale,
                    'name' => $t['name'],
                    'description' => $t['description'] ?? null,
                    'features' => $t['features'] ?? null,
                ]);
            }

            $package->services()->sync($serviceIds);
        }
    }
}
