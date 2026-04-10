<?php

namespace Modules\Cms\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Cms\Models\Content;
use Modules\Cms\Enums\contents\BasePageSlugs;
use Modules\Cms\Enums\contents\BaseContentTypes;

class ContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedBasePageContent();
        $this->seedFakeContent();
    }

    private function seedBasePageContent()
    {
        Content::updateOrCreate(
            [
                'type'      => BaseContentTypes::PAGES,
                'sub_type'  => BasePageSlugs::PRIVACY_POLICY,
            ],
            [
                'can_be_deleted'    => false,
                'custom_properties' => ['appear_in_footer' => true],
            ] + $this->basePageTranslations('privacy_policy'),
        );

        Content::updateOrCreate(
            [
                'type'      => BaseContentTypes::PAGES,
                'sub_type'  => BasePageSlugs::TERMS_AND_CONDITIONS,
            ],
            [
                'can_be_deleted'    => false,
                'custom_properties' => ['appear_in_footer' => true],
            ] + $this->basePageTranslations('terms_and_conditions'),
        );

        Content::updateOrCreate(
            [
                'type'      => BaseContentTypes::PAGES,
                'sub_type'  => BasePageSlugs::ABOUT_US,
            ],
            [
                'can_be_deleted'    => false,
                'custom_properties' => ['appear_in_footer' => true],
            ] + $this->basePageTranslations('about_us'),
        );
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function basePageTranslations(string $pageKey): array
    {
        return array_merge_recursive(
            createTranslateArray('title', 'contents.pages.'.$pageKey.'.title', 'cms'),
            createTranslateArray('long_description', 'contents.pages.'.$pageKey.'.long_description', 'cms'),
        );
    }

    private function seedFakeContent()
    {
        Content::factory()
        ->count(10)
        ->create();
    }
}
