<?php

namespace Modules\Platform\Database\Seeders;

use Illuminate\Database\Seeder;
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
                'translations'  => [
                    'en'        => ['name' => 'Towing service', 'description' => 'Towing service description'],
                    'tr'        => ['name' => 'Çekiç Hizmeti', 'description' => 'Çekiç Hizmeti açıklaması']
                ],
            ],
            [
                'translations'  => [
                    'en'        => ['name' => 'Transportation service', 'description' => 'Transportation service description'],
                    'tr'        => ['name' => 'Taşıma Hizmeti', 'description' => 'Taşıma Hizmeti açıklaması']
                ],
            ],
            [
                'translations'  => [
                    'en'        => ['name' => 'Repair service', 'description' => 'Repair service description'],
                    'tr'        => ['name' => 'Onarım Hizmeti', 'description' => 'Onarım Hizmeti açıklaması']
                ],
            ],

        ];

        foreach ($services as $service) {
            $serviceModel = Service::create([
                'show_in_search_filters' => true,
            ]);
            foreach ($service['translations'] as $locale => $translation) {
                ServiceTranslation::create([
                    'service_id' => $serviceModel->id,
                    'locale' => $locale,
                    'name' => $translation['name'],
                    'description' => $translation['description'],
                ]);
            }
        }
    }
}
