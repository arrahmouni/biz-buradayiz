<?php

namespace Modules\Cms\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
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

    /**
     * Roadside-themed FAQ records (en/tr) for demos and tests.
     */
    public function faq(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => BaseContentTypes::FAQS,
            'can_be_deleted' => true,
            'link' => null,
            'custom_properties' => null,
            'published_at' => null,
        ]);
    }

    /**
     * @return list<array{en: array{title: string, long_description: string}, tr: array{title: string, long_description: string}}>
     */
    private static function faqCatalog(): array
    {
        return [
            [
                'en' => [
                    'title' => 'How quickly can roadside assistance reach me?',
                    'long_description' => '<p>Arrival time depends on your exact location, traffic, and how busy nearby providers are. When you search on Biz Buradayiz we show available units so you can compare options in your area. Many urban requests are fulfilled within minutes; rural areas may take longer.</p>',
                ],
                'tr' => [
                    'title' => 'Yol yardımı ne kadar sürede yanıma gelir?',
                    'long_description' => '<p>Varış süresi tam konumunuza, trafiğe ve yakın ekiplerin yoğunluğuna bağlıdır. Biz Buradayız üzerinden arama yaptığınızda uygun ekipleri görürsünüz. Şehir içinde birçok talep dakikalar içinde karşılanır; kırsal bölgelerde süre uzayabilir.</p>',
                ],
            ],
            [
                'en' => [
                    'title' => 'What services can I request?',
                    'long_description' => '<p>Typical services include towing, battery jump-starts, flat tire help, fuel delivery, and lockout assistance. Available services depend on providers in your district—select your city and service type on the home page to see what is offered near you.</p>',
                ],
                'tr' => [
                    'title' => 'Hangi hizmetleri talep edebilirim?',
                    'long_description' => '<p>Çekici, akü takviyesi, lastik yardımı, yakıt teslimi ve kapı açma gibi hizmetler yaygındır. Sunulan hizmetler bölgenizdeki sağlayıcılara göre değişir; ana sayfadan il, ilçe ve hizmet türünü seçerek yakınınızdaki seçenekleri görebilirsiniz.</p>',
                ],
            ],
            [
                'en' => [
                    'title' => 'Is service available 24 hours a day?',
                    'long_description' => '<p>Many of our partner units operate around the clock, but coverage varies by location. After you choose your area and service, you will see providers that can respond at that time.</p>',
                ],
                'tr' => [
                    'title' => 'Hizmet 7/24 sunuluyor mu?',
                    'long_description' => '<p>Birçok iş ortağımız gece gündüz çalışır; kapsama alanı konuma göre değişir. Bölgenizi ve hizmeti seçtikten sonra o anda yanıt verebilen ekipleri görürsünüz.</p>',
                ],
            ],
            [
                'en' => [
                    'title' => 'How much does roadside assistance cost?',
                    'long_description' => '<p>Pricing is set by the service provider and depends on the job distance, vehicle type, and time of day. You should confirm the price and payment method directly with the unit before work begins.</p>',
                ],
                'tr' => [
                    'title' => 'Yol yardımı ücreti ne kadar?',
                    'long_description' => '<p>Ücret, hizmeti veren firma tarafından belirlenir; mesafe, araç türü ve zaman gibi faktörlere bağlıdır. İşe başlamadan önce fiyat ve ödeme yöntemini ekip ile netleştirmenizi öneririz.</p>',
                ],
            ],
            [
                'en' => [
                    'title' => 'What information should I have ready when I call?',
                    'long_description' => '<p>Have your location (or enable GPS), vehicle make and model, a brief description of the problem, and whether you are in a safe place. This helps the provider reach you faster and bring the right equipment.</p>',
                ],
                'tr' => [
                    'title' => 'Aradığımda hangi bilgileri hazır bulundurmalıyım?',
                    'long_description' => '<p>Konumunuz (veya GPS), araç marka-model, sorunun kısa özeti ve güvenli bir yerde olup olmadığınız gibi bilgileri hazır bulundurun. Böylece ekip size daha hızlı ve doğru ekipmanla ulaşabilir.</p>',
                ],
            ],
        ];
    }

    /**
     * @return array{en: array{title: string, long_description: string}, tr: array{title: string, long_description: string}}
     */
    private static function randomFaqCatalogEntry(): array
    {
        $catalog = self::faqCatalog();

        return $catalog[array_rand($catalog)];
    }

    public function configure()
    {
        ini_set('memory_limit', '1024M');

        return $this->afterCreating(function (Content $content) {
            $type = $content->type;
            $transModel = [];
            $faqEntry = $type === BaseContentTypes::FAQS ? self::randomFaqCatalogEntry() : null;

            $localeMapping = [
                'en' => 'en_US',
                // 'ar' => 'ar_SA',
                'tr' => 'tr_TR',
            ];

            foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties) {
                $fakerLocale = $localeMapping[$localeCode] ?? 'en_US';
                $faker = \Faker\Factory::create($fakerLocale);

                if ($faqEntry !== null) {
                    $localePayload = $faqEntry[$localeCode] ?? $faqEntry['en'];
                    $title = $localePayload['title'];
                    $longDescription = $localePayload['long_description'];
                    $slug = Str::slug($title, '-', $localeCode === 'tr' ? 'tr' : 'en').'-'.$content->id;
                } else {
                    $title = $faker->realText(20);
                    $longDescription = self::typeHasField($type, 'long_description') ? $faker->realText(150) : null;
                    $slug = null;
                }

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
