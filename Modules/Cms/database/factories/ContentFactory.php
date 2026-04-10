<?php

namespace Modules\Cms\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Cms\Enums\contents\BaseContentTypes;
use Modules\Cms\Models\Content;
use Modules\Cms\Traits\ContentTrait;

class ContentFactory extends Factory
{
    use ContentTrait;

    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Content::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        // get random elemnt from $typeList form keys
        $type = $this->faker->randomElement(array_keys(self::$typeList));

        if ($type == BaseContentTypes::PAGES) {
            return [];
        }

        return [
            'type' => $type,
            'can_be_deleted' => 1,
            'link' => self::typeHasField($type, 'link') ? $this->faker->url : null,
            'custom_properties' => self::typeHasField($type, 'select') ? ['placement' => 'home'] : null,
            'published_at' => self::typeHasField($type, 'published_at') ? $this->faker->dateTimeBetween('-1 year', 'now') : null,
        ];
    }

    public function configure()
    {
        ini_set('memory_limit', '1024M');

        return $this->afterCreating(function (Content $content) {
            $type = $content->type;
            $transModel = [];

            $localeMapping = [
                'en' => 'en_US',
                // 'ar' => 'ar_SA',
                'tr' => 'tr_TR',
            ];

            foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties) {
                $fakerLocale = $localeMapping[$localeCode] ?? 'en_US';
                $faker = \Faker\Factory::create($fakerLocale);

                $title = $faker->realText(20);
                $longDescription = self::typeHasField($type, 'long_description') ? $faker->realText(150) : null;
                $slug = null;

                $transModel[$localeCode] = $content->translations()->create(array_filter([
                    'locale' => $localeCode,
                    'title' => $title,
                    'slug' => $slug,
                    'short_description' => self::typeHasField($type, 'short_description') ? $faker->realText(100) : null,
                    'long_description' => $longDescription,
                ], fn ($value) => $value !== null));
            }

            if (! self::typeHasField($type, 'image')) {
                return;
            }

            $randomImagePath = public_path('modules/admin/metronic/demo/media/products/'.rand(1, 22).'.png');

            foreach ($transModel as $locale => $translation) {
                $translation->addMedia($randomImagePath)
                    ->preservingOriginal()
                    ->toMediaCollection(Content::MEDIA_COLLECTION);
            }
        });
    }
}
