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
                    'en'        => ['name' => 'Towing service'],
                    'tr'        => ['name' => 'Çekiç Hizmeti']
                ],
            ],
            [
                'translations'  => [
                    'en'        => ['name' => 'Transportation service'],
                    'tr'        => ['name' => 'Taşıma Hizmeti']
                ],
            ],
            [
                'translations'  => [
                    'en'        => ['name' => 'Repair service'],
                    'tr'        => ['name' => 'Onarım Hizmeti']
                ],
            ],

        ];

        foreach ($services as $service) {
            $serviceModel = Service::create();
            foreach ($service['translations'] as $locale => $translation) {
                ServiceTranslation::create([
                    'service_id' => $serviceModel->id,
                    'locale' => $locale,
                    'name' => $translation['name'],
                ]);
            }
        }
    }
}
