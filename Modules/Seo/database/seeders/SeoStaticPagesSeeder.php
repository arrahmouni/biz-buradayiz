<?php

namespace Modules\Seo\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Seo\Models\SeoStaticPage;

class SeoStaticPagesSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('seo.static_pages', []) as $row) {
            $key = $row['key'] ?? null;
            if (! $key) {
                continue;
            }

            SeoStaticPage::query()->updateOrCreate(
                ['key' => $key],
                [
                    'path_hint' => $row['path_hint'] ?? null,
                    'sort_order' => $row['sort_order'] ?? 0,
                ]
            );
        }
    }
}
