<?php

namespace Modules\Seo\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Seo\Models\Seo;
use Modules\Seo\Models\SeoStaticPage;

/**
 * Default SEO copy for registry static pages (home, contact, blog, FAQ, login).
 * Safe to re-run: overwrites translations for these entries only.
 */
class SeoStaticPagesMetaSeeder extends Seeder
{
    public function run(): void
    {
        $payloads = $this->definitions();

        foreach ($payloads as $pageKey => $byLocale) {
            $static = SeoStaticPage::query()->where('key', $pageKey)->first();
            if (! $static) {
                continue;
            }

            DB::transaction(function () use ($static, $byLocale) {
                $seo = Seo::query()->firstOrCreate(
                    [
                        'model_type' => $static->getMorphClass(),
                        'model_id' => $static->getKey(),
                    ],
                    []
                );

                foreach ($byLocale as $locale => $fields) {
                    foreach ($fields as $field => $value) {
                        $seo->{"{$field}:{$locale}"} = $value;
                    }
                }

                $seo->save();
            });
        }
    }

    /**
     * @return array<string, array<string, array<string, string|null>>>
     */
    protected function definitions(): array
    {
        return [
            'home' => [
                'en' => [
                    'meta_title' => 'Biz Buradayiz | 24/7 Roadside Assistance & Towing Near You',
                    'meta_description' => 'Find verified roadside help in minutes: towing, flat tire, battery jump-start, fuel delivery, and lockout support. Compare providers and request assistance anytime, day or night.',
                    'meta_keywords' => 'roadside assistance, towing service, 24/7 breakdown help, flat tire, battery jump, fuel delivery, car recovery, emergency towing',
                    'og_title' => 'Biz Buradayiz — Fast roadside help, 24/7',
                    'og_description' => 'Connect with nearby towing and roadside professionals. Transparent options when you need help on the road.',
                ],
                'tr' => [
                    'meta_title' => 'Biz Buradayız | 7/24 Yol Yardımı ve Çekici — Yakınınızdaki Hizmetler',
                    'meta_description' => 'Çekici, lastik, akü takviye, yakıt ve kilit açma için güvenilir yol yardımını dakikalar içinde bulun. Gece gündüz yakınınızdaki hizmet sağlayıcılarıyla eşleşin.',
                    'meta_keywords' => 'yol yardımı, çekici, 7/24 yol yardım, lastik değişimi, akü takviye, yakıt, oto kurtarma, acil çekici',
                    'og_title' => 'Biz Buradayız — Hızlı yol yardımı, 7/24',
                    'og_description' => 'Yakınınızdaki çekici ve yol yardım ekipleriyle buluşun. Yolda kaldığınızda net seçenekler.',
                ],
            ],
            'contact' => [
                'en' => [
                    'meta_title' => 'Contact Biz Buradayiz | 24/7 Roadside Support',
                    'meta_description' => 'Reach our team for partnerships, billing, or urgent roadside questions. We respond quickly and can guide you to the right help on the road.',
                    'meta_keywords' => 'contact Biz Buradayiz, roadside support, towing inquiry, customer service',
                    'og_title' => 'Contact us — Biz Buradayiz',
                    'og_description' => 'Questions about our platform or need assistance? Send a message or call — we are here to help.',
                ],
                'tr' => [
                    'meta_title' => 'İletişim | Biz Buradayız — 7/24 Yol Yardımı Desteği',
                    'meta_description' => 'İş ortaklığı, faturalama veya acil yol yardımı sorularınız için bize ulaşın. Hızlı yanıt verir, doğru desteğe yönlendiririz.',
                    'meta_keywords' => 'Biz Buradayız iletişim, yol yardımı destek, çekici talebi, müşteri hizmetleri',
                    'og_title' => 'Bize ulaşın — Biz Buradayız',
                    'og_description' => 'Platform hakkında sorunuz mu var veya yardıma mı ihtiyacınız var? Mesaj gönderin veya arayın.',
                ],
            ],
            'blog' => [
                'en' => [
                    'meta_title' => 'Roadside Safety & Towing Blog | Biz Buradayiz',
                    'meta_description' => 'Practical guides: what to do when you break down, winter driving, battery care, choosing a towing provider, and staying safe on the shoulder.',
                    'meta_keywords' => 'roadside safety, towing tips, breakdown checklist, car battery, winter driving, emergency kit',
                    'og_title' => 'Blog — safer roads with Biz Buradayiz',
                    'og_description' => 'Short reads on breakdowns, maintenance, and how to get help faster when it matters.',
                ],
                'tr' => [
                    'meta_title' => 'Yol Güvenliği ve Çekici Blogu | Biz Buradayız',
                    'meta_description' => 'Arızada yapılacaklar, kış sürüşü, akü bakımı, çekici seçimi ve emniyetli bekleme için pratik rehberler.',
                    'meta_keywords' => 'yol güvenliği, çekici ipuçları, arıza kontrol listesi, akü bakımı, kış lastiği, acil set',
                    'og_title' => 'Blog — Biz Buradayız ile daha güvenli yol',
                    'og_description' => 'Arıza, bakım ve acil durumda daha hızlı yardım için kısa okumalar.',
                ],
            ],
            'faq' => [
                'en' => [
                    'meta_title' => 'FAQ | Roadside Assistance & Towing | Biz Buradayiz',
                    'meta_description' => 'Answers about how Biz Buradayiz works, service areas, response times, pricing basics, and what to expect when you request roadside help.',
                    'meta_keywords' => 'roadside assistance FAQ, towing questions, how it works, service area, pricing',
                    'og_title' => 'Frequently asked questions',
                    'og_description' => 'Clear answers on using Biz Buradayiz to find towing and roadside assistance.',
                ],
                'tr' => [
                    'meta_title' => 'Sık Sorulan Sorular | Yol Yardımı ve Çekici | Biz Buradayız',
                    'meta_description' => 'Biz Buradayız nasıl çalışır, hizmet bölgeleri, süreler, ücretlendirme ve yol yardımı talebinde neler bekleyeceğiniz hakkında yanıtlar.',
                    'meta_keywords' => 'yol yardımı SSS, çekici soruları, nasıl çalışır, hizmet alanı, ücret',
                    'og_title' => 'Sık sorulan sorular',
                    'og_description' => 'Çekici ve yol yardımı bulmak için Biz Buradayız’ı kullanmaya dair net yanıtlar.',
                ],
            ],
            'login' => [
                'en' => [
                    'meta_title' => 'Partner Login | Biz Buradayiz for Service Providers',
                    'meta_description' => 'Secure sign-in for partner fleets and roadside providers. Manage requests, availability, and account settings.',
                    'meta_keywords' => 'Biz Buradayiz login, partner portal, fleet login, provider dashboard',
                    'og_title' => 'Partner sign-in — Biz Buradayiz',
                    'og_description' => 'Access your provider workspace to manage roadside and towing operations.',
                    'robots' => 'noindex, nofollow',
                ],
                'tr' => [
                    'meta_title' => 'İş Ortağı Girişi | Biz Buradayız Hizmet Sağlayıcılar',
                    'meta_description' => 'Filo ve yol yardımı iş ortakları için güvenli giriş. Talepleri, müsaitliği ve hesap ayarlarını yönetin.',
                    'meta_keywords' => 'Biz Buradayız giriş, iş ortağı paneli, filo girişi, sağlayıcı paneli',
                    'og_title' => 'İş ortağı girişi — Biz Buradayız',
                    'og_description' => 'Yol yardımı ve çekici operasyonlarınızı yönetmek için çalışma alanınıza erişin.',
                    'robots' => 'noindex, nofollow',
                ],
            ],
        ];
    }
}
