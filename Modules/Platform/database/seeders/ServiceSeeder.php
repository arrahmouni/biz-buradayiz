<?php

namespace Modules\Platform\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Platform\Models\Package;
use Modules\Platform\Models\Service;
use Modules\Platform\Models\ServiceTranslation;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $services = [
            [
                'icon' => 'fas fa-truck',
                'translations' => [
                    'en' => ['name' => 'Towing service', 'description' => '7/24 towing service'],
                    'tr' => ['name' => 'Çekiç Hizmeti', 'description' => '7/24 oto çekici'],
                ],
            ],
            [
                'icon' => 'fas fa-car',
                'translations' => [
                    'en' => ['name' => 'Tyre change', 'description' => 'Flat tyre change'],
                    'tr' => ['name' => 'Lastik Değişimi', 'description' => 'Patlak lastik değişimi'],
                ],
            ],
            [
                'icon' => 'fas fa-car-battery',
                'translations' => [
                    'en' => ['name' => 'Battery boost', 'description' => 'Fast battery boost'],
                    'tr' => ['name' => 'Akü Takviyesi', 'description' => 'Hızlı akü takviyesi'],
                ],
            ],
            [
                'icon' => 'fas fa-gas-pump',
                'translations' => [
                    'en' => ['name' => 'Fuel delivery', 'description' => 'Emergency fuel delivery'],
                    'tr' => ['name' => 'Yakıt Teslimi', 'description' => 'Acil yakıt teslimi'],
                ],
            ],

        ];

        foreach ($services as $service) {
            $serviceModel = Service::create([
                'show_in_search_filters' => true,
                'icon' => $service['icon'] ?? null,
            ]);
            foreach ($service['translations'] as $locale => $translation) {
                ServiceTranslation::create([
                    'service_id' => $serviceModel->id,
                    'locale' => $locale,
                    'name' => $translation['name'],
                    'description' => $translation['description'],
                ]);
            }

            Package::factory()
                ->count(3)
                ->sequence(
                    ['is_popular' => true],
                    ['is_popular' => false],
                    ['is_popular' => false],
                )
                ->create();
        }
    }
}
