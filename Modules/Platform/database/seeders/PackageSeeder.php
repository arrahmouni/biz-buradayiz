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
        $this->seedFreePackage();
        Package::factory()->count(10)->create();
    }

    private function seedFreePackage(): void
    {
        $services = Service::query()->orderBy('id')->get();
        if ($services->isEmpty()) {
            return;
        }

        $locales = LaravelLocalization::getSupportedLanguagesKeys();
        $serviceIds = $services->pluck('id')->all();

        $package = Package::query()->updateOrCreate(
            ['is_free_tier' => true],
            [
                'price' => 0,
                'currency' => 'TRY',
                'billing_period' => BillingPeriod::Monthly,
                'sort_order' => 0,
                'connections_count' => 1,
            ]
        );

        $translations = [
            'en' => [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Free monthly plan for service providers. One subscription per provider.',
                'features' => "Monthly renewal\nLimited connections\nAll service categories",
            ],
            'tr' => [
                'name' => 'Ücretsiz',
                'slug' => 'ucretsiz',
                'description' => 'Hizmet sağlayıcıları için ücretsiz aylık plan. Sağlayıcı başına tek abonelik.',
                'features' => "Aylık yenileme\nSınırlı bağlantı\nTüm hizmet kategorileri",
            ],
        ];

        foreach ($locales as $locale) {
            $t = $translations[$locale] ?? $translations['en'];
            PackageTranslation::query()->updateOrCreate(
                [
                    'package_id' => $package->id,
                    'locale' => $locale,
                ],
                [
                    'name' => $t['name'],
                    'slug' => $t['slug'] ?? null,
                    'description' => $t['description'] ?? null,
                    'features' => $t['features'] ?? null,
                ]
            );
        }

        $package->services()->sync($serviceIds);

    }
}
