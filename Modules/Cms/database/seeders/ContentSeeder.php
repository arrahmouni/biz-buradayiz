<?php

namespace Modules\Cms\database\seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Cms\Enums\contents\BaseContentTypes;
use Modules\Cms\Enums\contents\BasePageSlugs;
use Modules\Cms\Models\Content;

class ContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedBasePageContent();
        $this->seedFaqs();
        $this->seedBlogs();
        // $this->seedFakeContent();
    }

    private function seedBasePageContent()
    {
        Content::updateOrCreate(
            [
                'type' => BaseContentTypes::PAGES,
                'sub_type' => BasePageSlugs::PRIVACY_POLICY,
            ],
            [
                'can_be_deleted' => false,
                'custom_properties' => ['appear_in_footer' => true],
            ] + $this->basePageTranslations('privacy_policy'),
        );

        Content::updateOrCreate(
            [
                'type' => BaseContentTypes::PAGES,
                'sub_type' => BasePageSlugs::TERMS_AND_CONDITIONS,
            ],
            [
                'can_be_deleted' => false,
                'custom_properties' => ['appear_in_footer' => true],
            ] + $this->basePageTranslations('terms_and_conditions'),
        );

        Content::updateOrCreate(
            [
                'type' => BaseContentTypes::PAGES,
                'sub_type' => BasePageSlugs::ABOUT_US,
            ],
            [
                'can_be_deleted' => false,
                'custom_properties' => ['appear_in_footer' => true],
            ] + $this->basePageTranslations('about_us'),
        );
    }

    private function seedFaqs(): void
    {
        foreach (self::faqCatalog() as $index => $entry) {
            $seedKey = 'roadside_faq_'.$index;

            $content = Content::updateOrCreate(
                [
                    'type' => BaseContentTypes::FAQS,
                    'custom_properties->seed_key' => $seedKey,
                ],
                [
                    'can_be_deleted' => true,
                    'link' => null,
                    'published_at' => null,
                    'custom_properties' => ['seed_key' => $seedKey],
                ]
            );

            foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties) {
                $localePayload = $entry[$localeCode] ?? $entry['en'];
                $title = $localePayload['title'];
                $slug = Str::slug($title, '-', $localeCode === 'tr' ? 'tr' : 'en').'-'.$content->id;

                $translation = $content->translateOrNew($localeCode);
                $translation->title = $title;
                $translation->slug = $slug;
                $translation->short_description = null;
                $translation->long_description = $localePayload['long_description'];
            }

            $content->save();
        }
    }

    private function seedBlogs(): void
    {
        $pexels = 'https://images.pexels.com/photos/%d/pexels-photo-%d.jpeg?auto=compress&cs=tinysrgb&w=800&h=500&fit=crop';

        foreach (self::blogCatalog($pexels) as $index => $entry) {
            $seedKey = 'car_blog_'.$index;
            $imageUrl = $entry['image'];

            $content = Content::updateOrCreate(
                [
                    'type' => BaseContentTypes::BLOGS,
                    'custom_properties->seed_key' => $seedKey,
                ],
                [
                    'can_be_deleted' => true,
                    'link' => null,
                    'published_at' => Carbon::parse($entry['published_at']),
                    'custom_properties' => ['seed_key' => $seedKey],
                ]
            );

            foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties) {
                $localePayload = $entry[$localeCode] ?? $entry['en'];
                $title = $localePayload['title'];
                $slug = Str::slug($title, '-', $localeCode === 'tr' ? 'tr' : 'en').'-'.$content->id;

                $translation = $content->translateOrNew($localeCode);
                $translation->title = $title;
                $translation->slug = $slug;
                $translation->short_description = $localePayload['short_description'];
                $translation->long_description = $localePayload['long_description'];
            }

            $content->save();
            $content->load('translations');

            foreach ($content->translations as $translation) {
                $translation->clearMediaCollection(Content::MEDIA_COLLECTION);
                $translation->addMediaFromUrl($imageUrl)
                    ->toMediaCollection(Content::MEDIA_COLLECTION);
            }
        }
    }

    /**
     * @return list<array{en: array{title: string, long_description: string}, tr: array{title: string, long_description: string}}>
     */
    private static function faqCatalog(): array
    {
        return [
            [
                'en' => [
                    'title' => 'Why is my car overheating and what should I do?',
                    'long_description' => '<p>Overheating is often caused by low coolant, a stuck thermostat, a failed water pump, or a blocked radiator. If the temperature warning comes on, turn off the A/C, turn on the heater to help shed heat, and pull over safely as soon as you can. Do not open a hot radiator cap. If smoke or steam is heavy or the needle is in the red zone, stop driving and call for assistance.</p>',
                ],
                'tr' => [
                    'title' => 'Aracım neden aşırı ısınıyor, ne yapmalıyım?',
                    'long_description' => '<p>Aşırı ısınma genelde düşük soğutma suyu, termostat arızası, su pompası veya radyatör tıkanıklığından kaynaklanır. Uyarı lambası yandığında klimayı kapatın, ısıyı azaltmak için kaloriferi açın ve güvenli bir yere çekin. Sıcakken radyatör kapağını açmayın. Yoğun buhar/duman varsa veya gösterge kırmızıda ise aracı sürmeyi bırakıp yardım isteyin.</p>',
                ],
            ],
            [
                'en' => [
                    'title' => 'My car will not start—battery or alternator?',
                    'long_description' => '<p>If the starter clicks or turns slowly and lights dim, the battery is often weak or terminals are corroded. If the engine starts but the battery warning stays on, or the lights fade while idling, the charging system (alternator or belt) may be at fault. A simple voltage check at a workshop can tell them apart. Jump-starting only helps if the battery is the problem.</p>',
                ],
                'tr' => [
                    'title' => 'Aracım çalışmıyor—akü mü alternatör mü?',
                    'long_description' => '<p>Marş tıklıyor veya yavaş dönüyorsa ve farlar sönüyorsa akü zayıf veya kutup başları oksitlenmiş olabilir. Motor çalışıyorsa ama şarj uyarısı yanıyorsa veya rölantide ışıklar kararıyorsa şarj sistemi (alternatör veya kayış) şüpheli olur. Bir serviste voltaj ölçümü ile ayrım yapılır. Takviye sadece akü sorununda işe yarar.</p>',
                ],
            ],
            [
                'en' => [
                    'title' => 'What do squealing or grinding brakes mean?',
                    'long_description' => '<p>A high-pitched squeal when braking can be wear indicators on brake pads. A grinding sound often means metal-on-metal and needs immediate attention. Pulsation in the pedal may point to warped discs. Driving with worn brakes increases stopping distance and risks rotor damage—have pads and discs inspected as soon as possible.</p>',
                ],
                'tr' => [
                    'title' => 'Frenlerde cızırtı veya gıcırtı ne anlama gelir?',
                    'long_description' => '<p>Fren sırasında tiz cızırtı genelde balata aşınma uyarısıdır. Gıcırtı sesi ise metal-metal temasına işaret eder ve geciktirilmemelidir. Pedalda titreşim ise disk eğriliğini düşündürür. Aşınmış frenle durma mesafesi uzar ve disklere zarar verir; en kısa sürede kontrol ettirin.</p>',
                ],
            ],
            [
                'en' => [
                    'title' => 'The check engine light is on—can I keep driving?',
                    'long_description' => '<p>A steady yellow or amber light usually means the engine computer stored a fault code; you can often drive carefully to a workshop unless performance is clearly wrong. A flashing light under load often signals misfire and can damage the catalytic converter—ease off the throttle and avoid hard acceleration until it is diagnosed. A diagnostic scan identifies the code.</p>',
                ],
                'tr' => [
                    'title' => 'Arıza lambası yandı—sürmeye devam edebilir miyim?',
                    'long_description' => '<p>Sabit sarı lamba genelde hata kodu kaydıdır; performans bozulmadıkça dikkatli şekilde servise gidilebilir. Yük altında yanıp sönen lamba genelde ateşleme sorununa işaret eder ve katalizöre zarar verebilir—gazı hafifletin ve teşhis alınana kadar zorlamayın. Arıza kodu cihazla okunur.</p>',
                ],
            ],
            [
                'en' => [
                    'title' => 'How do I stay safe with a flat tire on the road?',
                    'long_description' => '<p>Signal early, slow down smoothly, and stop on level ground away from traffic. Use the parking brake and warning triangle or hazards. If you have a spare and the right tools, follow the owner’s manual for jacking points—never crawl under an improperly supported car. Run-flat or sealant kits have limits; when in doubt, call a tire or roadside service.</p>',
                ],
                'tr' => [
                    'title' => 'Yolda lastiğim patladığında nasıl güvende kalırım?',
                    'long_description' => '<p>Erken sinyal verin, yumuşakça yavaşlayıp trafikten uzak, düz bir yere çekin. El freni, üçgen ve dörtlüleri kullanın. Stepne ve ekipman varsa kriko noktalarını kullanım kılavuzuna göre kullanın—desteksiz aracın altına girmeyin. Run-flat ve tamir kitlerinin sınırları vardır; emin değilseniz lastik veya yol yardımı arayın.</p>',
                ],
            ],
            [
                'en' => [
                    'title' => 'How do I stay safe with a flat tire on the road?',
                    'long_description' => '<p>Signal early, slow down smoothly, and stop on level ground away from traffic. Use the parking brake and warning triangle or hazards. If you have a spare and the right tools, follow the owner’s manual for jacking points—never crawl under an improperly supported car. Run-flat or sealant kits have limits; when in doubt, call a tire or roadside service.</p>',
                ],
                'tr' => [
                    'title' => 'Yolda lastiğim patladığında nasıl güvende kalırım?',
                    'long_description' => '<p>Erken sinyal verin, yumuşakça yavaşlayıp trafikten uzak, düz bir yere çekin. El freni, üçgen ve dörtlüleri kullanın. Stepne ve ekipman varsa kriko noktalarını kullanım kılavuzuna göre kullanın—desteksiz aracın altına girmeyin. Run-flat ve tamir kitlerinin sınırları vardır; emin değilseniz lastik veya yol yardımı arayın.</p>',
                ],
            ],
        ];
    }

    /**
     * @param  non-empty-string  $pexels
     * @return list<array{
     *     published_at: string,
     *     image: non-empty-string,
     *     en: array{title: string, short_description: string, long_description: string},
     *     tr: array{title: string, short_description: string, long_description: string}
     * }>
     */
    private static function blogCatalog(string $pexels): array
    {
        $u = static fn (int $id): string => sprintf($pexels, $id, $id);

        return [
            [
                'published_at' => '2025-03-03 10:00:00',
                'image' => $u(3807275),
                'en' => [
                    'title' => 'Oil changes: intervals, viscosity, and reading the dipstick',
                    'short_description' => 'Why “every 10,000 km” is not universal, how grade labels work, and simple habits that catch low oil early.',
                    'long_description' => '<p>Engine oil lubricates, cools internal parts, and carries contaminants to the filter. Running low or past its service life increases wear and can trigger warning lights.</p><p>Follow the <strong>owner’s manual</strong> first: severe conditions—short trips, dust, towing, or sustained high speeds—often shorten intervals compared with gentle highway use. The label on the bottle (for example 5W-30) describes cold-flow and high-temperature thickness; the manual specifies what your engine was designed to use.</p><p>Check the dipstick on level ground with the engine off and cooled enough to be safe: wipe once, reinsert fully, then read the level between the marks. A sudden drop between checks suggests a leak or consumption worth investigating.</p><p>If the oil-pressure warning illuminates while driving, stop as soon as it is safe—continuing can destroy the engine in minutes.</p>',
                ],
                'tr' => [
                    'title' => 'Yağ bakımı: periyot, viskozite ve çubuk kontrolü',
                    'short_description' => '“Her 10.000 km” kuralının her araç için geçerli olmadığı, etiketlerin anlamı ve erken uyarı alışkanlıkları.',
                    'long_description' => '<p>Motor yağı sürtünmeyi azaltır, iç parçaları soğutur ve kirleri süzgece taşır. Seviye düşük veya ömrü geçmiş yağ, aşınmayı artırır ve uyarı lambalarını tetikleyebilir.</p><p>Önce <strong>kullanım kılavuzuna</strong> bakın: kısa mesafe, toz, çekme veya sürekli yüksek hız gibi zorlu kullanım, hafif otoyol kullanımına göre aralığı kısaltır. Şişe üzerindeki derece (ör. 5W-30) soğuk akış ve sıcaklıkta kalınlığı tanımlar; motorunuzun hangi yağı istediği kılavuzda yazar.</p><p>Çubuğu düz zeminde, motor kapalı ve güvenli soğudukça kontrol edin: bir kez silin, tam oturtun, sonra min–max aralığını okuyun. Kontroller arasında ani düşüş kaçak veya yakıt tüketimini düşündürür.</p><p>Sürüşte yağ basıncı uyarısı yanıyorsa güvenli şekilde durun—devam etmek dakikalar içinde motora zarar verebilir.</p>',
                ],
            ],
            [
                'published_at' => '2025-02-26 15:30:00',
                'image' => $u(627682),
                'en' => [
                    'title' => 'TPMS lights and tire pressure when the weather swings',
                    'short_description' => 'Why pressure drops in the cold, how to reset false alerts, and when low pressure is a puncture.',
                    'long_description' => '<p>Tire pressure monitoring systems (TPMS) warn when a tire is significantly under-inflated. Air contracts in cold weather, so a light after the first frost is common even without a leak.</p><p>Inflate to the <strong>vehicle placard</strong> pressure (door jamb or manual), not always the maximum molded on the tire sidewall. Check pressures when tires are cold—before a long drive heats them up.</p><p>If one corner drops repeatedly, inspect for nails, rim damage, or a faulty valve. After adjusting pressure, some cars need a TPMS reset or a short drive to clear the warning; consult the manual for the exact procedure.</p><p>Correct pressure improves handling, reduces uneven wear, and helps fuel economy on long trips.</p>',
                ],
                'tr' => [
                    'title' => 'TPMS uyarısı ve hava sıcaklığı değişince lastik basıncı',
                    'short_description' => 'Soğukta basıncın düşmesi, yanlış alarmların sıfırlanması ve patlak şüphesi.',
                    'long_description' => '<p>Lastik basınç izleme sistemi (TPMS), basıncın belirgin şekilde düştüğünde uyarır. Soğuk havada hava büzüşür; ilk dondan sonra lamba, kaçak olmadan da yanabilir.</p><p>Şişirmeyi <strong>araç etiketindeki</strong> (kapı direği veya kılavuz) değere göre yapın; yanakta yazan maksimum her zaman hedef basınç değildir. Lastikler soğukken ölçün—uzun sürüş ısıtınca değer yükselir.</p><p>Tek bir köşe sürekli düşüyorsa çivi, jant hasarı veya supap kaçağını kontrol edin. Basınç ayarından sonra bazı araçlarda TPMS sıfırlama veya kısa sürüş gerekir; prosedür kılavuzdadır.</p><p>Doğru basınç tutun yol tutuşunu, düzensiz aşınmayı ve uzun yolda yakıt tüketimini iyileştirir.</p>',
                ],
            ],
            [
                'published_at' => '2025-02-19 12:00:00',
                'image' => $u(1638459),
                'en' => [
                    'title' => 'Jump-starting safely: cable order, sparks, and hybrid notes',
                    'short_description' => 'A methodical sequence protects electronics and people—plus what to do if the car still will not start.',
                    'long_description' => '<p>Jump-starting transfers energy from a donor battery to a depleted one. Rushing the hookup risks sparks near the battery, which can ignite vented hydrogen gas.</p><p><strong>Typical sequence:</strong> red clamp to the dead battery’s positive terminal; other red to the donor’s positive; black to the donor’s negative; final black to an unpainted engine ground on the stalled car (not the dead battery’s negative post if the manual warns against it). Start the donor, wait a minute, then try the stalled vehicle.</p><p>Remove clamps in reverse order, without letting metal parts touch each other or either positive terminal.</p><p>Hybrids and some modern cars have specific jump points under the hood—follow the manual. If the engine cranks strongly but will not fire, the issue may not be the battery; if it dies immediately after a successful jump, test charging and parasitic drain.</p>',
                ],
                'tr' => [
                    'title' => 'Güvenli takviye: kablo sırası, kıvılcım ve hibrit notları',
                    'short_description' => 'Sistematik bağlantı elektronik ve can güvenliğini korur; takviyeye rağmen çalışmıyorsa ne yapılmalı?',
                    'long_description' => '<p>Takviye, verici aküden boşalmış aküye enerji aktarır. Acele bağlantı, akü yakınında kıvılcıma ve gaz riskine yol açabilir.</p><p><strong>Tipik sıra:</strong> kırmızı kelepçe boş akünün artı kutbuna; diğer kırmızı vericinin artısına; siyah vericinin eksisine; son siyah, çalışmayan araçta boyanmamış motor gövdesine (kılavuz eksiyi aküde istemiyorsa ölü akünün eksi kutbuna değil). Vericiyi çalıştırın, kısa bekleyin, sonra marş deneyin.</p><p>Kelepçeleri ters sırada sökün; metal parçalar birbirine veya artı kutba değmesin.</p><p>Hibrit ve bazı modern araçlarda kaput altında özel takviye noktaları vardır—kılavuza uyun. Marş güçlü ama tutmuyorsa sorun akü olmayabilir; takviye sonrası hemen ölüyorsa şarj ve kaçak akım test edilmelidir.</p>',
                ],
            ],
            [
                'published_at' => '2025-02-12 09:00:00',
                'image' => $u(3802510),
                'en' => [
                    'title' => 'Engine overheating: causes, warning signs, and what to do',
                    'short_description' => 'Learn why coolant loss, thermostats, and pumps trigger overheating—and the safe steps to take before serious engine damage.',
                    'long_description' => '<p>Your temperature gauge and warning light exist to protect the engine. When coolant cannot circulate or shed heat, cylinder heads can warp and gaskets can fail.</p><p><strong>Common causes</strong> include leaks, a stuck thermostat, a worn water pump, a clogged radiator or AC condenser fins, and cooling fan failure. Towing heavy loads uphill in hot weather pushes marginal systems over the edge.</p><p><strong>At the first sign of overheating</strong>, reduce load: turn off the A/C, set heat to full with fans high (it pulls heat from the engine), and find a safe place to stop. After the engine cools—only then—check coolant level in the reservoir if you know how. If the problem returns on a short drive, have the system pressure-tested rather than repeatedly topping up.</p><p>Professional help is the right call if you see heavy steam, oil in the coolant, or repeated boil-over. Keeping service records for coolant changes and belt replacement helps prevent surprises.</p>',
                ],
                'tr' => [
                    'title' => 'Motor aşırı ısınması: nedenler, belirtiler ve yapılacaklar',
                    'short_description' => 'Soğutma suyu kaybı, termostat ve pompa gibi nedenlerle ısınan motoru nasıl güvenle yöneteceğinizi öğrenin.',
                    'long_description' => '<p>Gösterge ve uyarı lambası motoru korumak içindir. Soğutma suyu dolaşamaz veya ısıyı atamazsa silindir kafaları eğilebilir, contalar zarar görebilir.</p><p><strong>Yaygın nedenler</strong> kaçaklar, sıkışmış termostat, yıpranmış su pompası, tıkanmış radyatör veya fan arızasıdır. Sıcak havada ağır yükle çıkış bu sınırı zorlar.</p><p><strong>İlk belirtilerde</strong> yükü azaltın: klimayı kapatın, kaloriferi ve fanı sonuna alın ve güvenli durun. Motor soğuduktan sonra—sadece o zaman—rezervuar seviyesini biliyorsanız kontrol edin. Kısa sürüşte tekrarlıyorsa sürekli su eklemek yerine basınç testi yaptırın.</p><p>Yoğun buhar, yağlı soğutma suyu veya sürekli taşma varsa uzman yardımı alın. Periyodik soğutma suyu ve kayış bakımı sürprizleri azaltır.</p>',
                ],
            ],
            [
                'published_at' => '2025-02-05 11:30:00',
                'image' => $u(1149831),
                'en' => [
                    'title' => 'Dead battery vs bad alternator: how to tell the difference',
                    'short_description' => 'Symptoms, quick checks, and why jump-starting only fixes one of these problems.',
                    'long_description' => '<p>Both a weak battery and a failing alternator can leave you stranded, but they fail in different ways.</p><p>A <strong>battery</strong> that is old or drained may crank slowly, show dim headlights at rest, or fail after short trips. Corroded terminals can mimic a dead battery—cleaning them sometimes restores full current.</p><p>An <strong>alternator</strong> that undercharges often triggers a battery or charging warning after the engine starts. Lights may brighten when you rev the engine. If the battery is new but keeps going flat, the charging circuit or a parasitic drain is suspect.</p><p>Jump-starting helps when the battery lacks charge but the alternator can replenish it on the drive. If the car dies again minutes after a jump, prioritize alternator and belt inspection. A workshop can measure resting voltage, charging voltage under load, and perform a load test on the battery.</p>',
                ],
                'tr' => [
                    'title' => 'Bitmiş akü mü arızalı alternatör mü: nasıl ayırt edilir?',
                    'short_description' => 'Belirtiler, hızlı kontroller ve takviyenin yalnızca bir sorunu çözmesinin nedeni.',
                    'long_description' => '<p>Zayıf akü ve arızalı alternatör yolda bırakabilir; farklı şekillerde kendini gösterir.</p><p><strong>Akü</strong> yaşlandığında veya boşaldığında marş yavaşlar, duruşta farlar solar veya kısa yolculuklardan sonra çalışmaz. Oksitlenmiş kutup başları aküyü taklit eder—temizlemek bazen yeterlidir.</p><p><strong>Alternatör</strong> yetersiz şarj ediyorsa motor çalışınca şarj uyarısı çıkabilir. Motor yükseldikçe ışıklar parlaklaşabilir. Akü yeniyken sürekli boşalıyorsa şarj devresi veya kaçak akım şüphelidir.</p><p>Takviye, akü boş ama alternatör yol boyunca doldurabiliyorsa işe yarar. Takviyeden dakikalar sonra yine ölüyorsa alternatör ve kayış önceliklidir. Serviste dinlenme voltajı, yük altı şarj ve akü yük testi yapılabilir.</p>',
                ],
            ],
            [
                'published_at' => '2025-01-28 14:00:00',
                'image' => $u(1592932),
                'en' => [
                    'title' => 'Brake noise explained: pads, discs, and when to worry',
                    'short_description' => 'From harmless dust squeal to metal-on-metal grinding—what each sound means for your safety.',
                    'long_description' => '<p>Brakes convert motion into heat. As pads wear, friction material thins and hardware can loosen, which changes the sounds you hear.</p><p>A brief squeal in the morning after rain or cold is often surface rust on the discs and usually clears after a few stops. Continuous squeal, especially with a wear indicator, means the pad lining is near its limit.</p><p><strong>Grinding</strong> usually means the backing plate is contacting the disc. That destroys rotors quickly and lengthens stopping distances—schedule service immediately.</p><p>Pulsation or vibration through the pedal and steering can indicate warped discs or uneven pad transfer. Regular inspections—pad thickness, fluid level, and hose condition—keep predictable braking in emergency situations.</p>',
                ],
                'tr' => [
                    'title' => 'Fren sesleri: balata, disk ve ne zaman endişelenmeli?',
                    'short_description' => 'Toz cızırtısından metal gıcırtısına—her ses güvenliğiniz için ne anlama gelir?',
                    'long_description' => '<p>Frenler hareketi ısıya çevirir. Balata inceldikçe ses değişir.</p><p>Yağmurdan sonra kısa süreli cızırtı genelde disk yüzeyindeki pasın birkaç frenle silinmesidir. Sürekli cızırtı, özellikle aşınma uyarısıyla, balatanın sınırda olduğunu gösterir.</p><p><strong>Gıcırtı</strong> çoğunlukla metal tabanın diske değmesidir; diskleri hızla yıpratır ve fren mesafesini uzatır—hemen servis alın.</p><p>Pedal ve direksiyonda titreşim eğri disk veya dengesiz transferi düşündürür. Kalınlık, hidrolik ve hortum kontrolü acil durumlarda tutarlı fren için önemlidir.</p>',
                ],
            ],
            [
                'published_at' => '2025-01-15 10:15:00',
                'image' => $u(210019),
                'en' => [
                    'title' => 'Check engine light: steady vs flashing and next steps',
                    'short_description' => 'Understand OBD warnings, misfire risk, and when to stop driving.',
                    'long_description' => '<p>The malfunction indicator lamp (MIL) ties to dozens of sensors and actuators. The same light can mean a loose fuel cap or a serious misfire, so context matters.</p><p>A <strong>steady</strong> light with normal power and no new smells often allows a prompt—but not months-long—visit to read codes. Many shops scan for free or a small fee; the code points the technician to tests, not always to the exact part.</p><p>A <strong>flashing</strong> light, especially under acceleration, often indicates raw fuel entering the exhaust from misfire. That can overheat the catalytic converter. Reduce throttle, avoid high RPM, and arrange diagnosis soon.</p><p>Ignoring long-term MIL on with poor fuel economy may mask failing oxygen sensors or ignition parts that also increase emissions. Pairing a scan with live data shortens diagnostic time.</p>',
                ],
                'tr' => [
                    'title' => 'Arıza lambası: sabit ve yanıp sönen; sonraki adımlar',
                    'short_description' => 'OBD uyarıları, ateşleme riski ve ne zaman sürmeyi bırakmalısınız.',
                    'long_description' => '<p>Arıza lambası onlarca sensör ve eylemciyle bağlıdır. Aynı lamba gevşek depo kapağı veya ciddi ateşleme sorununu gösterebilir.</p><p><strong>Sabit</strong> lamba, güç normal ve koku yoksa genelde kısa süre içinde kod okumaya gidilebilir. Kod teknisyene test yolu gösterir, her zaman parça adı vermez.</p><p><strong>Yanıp sönen</strong> lamba, özellikle gazda, ham yakıtın egzoza gitmesine ve katalizörün aşırı ısınmasına işaret edebilir. Gazı kısın, yüksek devirden kaçının ve teşhis alın.</p><p>Uzun süre ihmal edilen lamba, O2 sensörü veya ateşleme parçaları gibi hem tüketimi hem emisyonu kötüleştiren arızaları gizleyebilir. Canlı veri ile tarama süreyi kısaltır.</p>',
                ],
            ],
            [
                'published_at' => '2025-01-08 08:45:00',
                'image' => $u(244206),
                'en' => [
                    'title' => 'Flat tire on the highway: step-by-step safety checklist',
                    'short_description' => 'Where to stop, how to use your spare, and when to call a professional.',
                    'long_description' => '<p>A blowout or slow leak on a fast road is stressful. The priority is controlling the vehicle and getting away from live lanes.</p><p><strong>1.</strong> Grip the wheel firmly; do not slam the brakes. Ease off the accelerator and let drag slow you down.<br><strong>2.</strong> Signal and move to the shoulder or emergency lane. Park as flat as possible; avoid soft verges that can hide sinkage.<br><strong>3.</strong> Hazards on, parking brake set, passengers out of the vehicle side away from traffic if it is unsafe inside.<br><strong>4.</strong> If changing a wheel, use only the jack points in the manual. Place wheel chocks if you have them. Never put limbs under the car while it is lifted.<br><strong>5.</strong> Compact spares and sealant kits are temporary—observe speed limits and replace or repair the primary tire promptly.</p><p>If traffic, weather, or missing tools make the situation risky, calling roadside tire service is the better choice.</p>',
                ],
                'tr' => [
                    'title' => 'Otoyolda patlak lastik: adım adım güvenlik listesi',
                    'short_description' => 'Nerede durmalı, stepneyi nasıl kullanmalı ve ne zaman profesyonel çağırmalısınız.',
                    'long_description' => '<p>Hızlı yolda patlak veya kaçak streslidir. Önce aracı kontrol edip canlı şeritten uzaklaşın.</p><p><strong>1.</strong> Direksiyonu sıkı tutun; frene köklemeyin. Gazı bırakıp sürtünme ile yavaşlayın.<br><strong>2.</strong> Sinyal verip banket veya acil şeride geçin. Mümkünse düz zemin seçin.<br><strong>3.</strong> Dörtlü ve el freni; trafik içinde güvenli değilse yolcuları trafikten uzak taraftan indirin.<br><strong>4.</strong> Lastik değiştirirken yalnızca kılavuzdaki kriko noktalarını kullanın. Takoz varsa kullanın. Kalkık aracın altına uzanmayın.<br><strong>5.</strong> İnce stepne ve tamir köpüğü geçicidir; hız sınırına uyun ve asıl lastiği hemen onarın.</p><p>Trafik, hava veya eksik ekipman riskliyse yol yardımı veya lastik servisi daha doğru seçimdir.</p>',
                ],
            ],
            [
                'published_at' => '2025-01-03 09:30:00',
                'image' => $u(3807323),
                'en' => [
                    'title' => 'Wiper blades: streaks, chatter, and when to replace them',
                    'short_description' => 'Clean glass and fresh rubber matter as much as washer fluid—especially in road salt and pollen season.',
                    'long_description' => '<p>Worn wipers reduce visibility in rain, snow melt, and dirty spray from trucks. Streaks usually mean torn or hardened rubber; chatter across the glass often points to a bent arm, wrong adapter, or residue on the windshield.</p><p>Try <strong>cleaning the blade edge and glass</strong> with a mild automotive glass cleaner first. If rubber is cracked or the frame corroded, replacement is the fix. Many vehicles use different lengths left and right—match the manual or an in-store guide.</p><p>In freezing climates, lift wipers before frost or use a de-icer fluid rated for your climate; tearing rubber off frozen glass is common.</p><p>Pair good blades with a washer reservoir topped up with the correct fluid—not plain water in winter—to keep the view clear on the highway.</p>',
                ],
                'tr' => [
                    'title' => 'Silecek lastikleri: çizgi, zıplama ve ne zaman değiştirmeli?',
                    'short_description' => 'Temiz cam ve sağlam lastik, özellikle tuz ve polen mevsiminde cam suyundan az değildir.',
                    'long_description' => '<p>Yıpranmış silecekler yağmur, eriyen kar ve kamyon sıçratmasında görüşü azaltır. Çizgiler genelde yırtık veya sertleşmiş lastiği gösterir; camda zıplama eğri kol, yanlış aparat veya cam üzerinde kalıntıyı düşündürür.</p><p>Önce <strong>lastik kenarını ve camı</strong> hafif oto cam temizleyici ile temizlemeyi deneyin. Lastik çatlamış veya taşıyıcı paslanmışsa değişim gerekir. Birçok araçta sağ–sol uzunluklar farklıdır—kılavuz veya mağaza kataloğuna uyun.</p><p>Don olayında donmuş cama yapışık sileceği zorlamak lastiği koparabilir; mümkünse önceden kaldırın veya iklimine uygun çözücü sıvı kullanın.</p><p>İyi sileceği, kışta düz su yerine doğru cam suyu ile destekleyin; otoyolda görüşü açık tutar.</p>',
                ],
            ],
            [
                'published_at' => '2024-12-27 11:00:00',
                'image' => $u(1148940),
                'en' => [
                    'title' => 'Yellow, foggy headlights: polish, seal, or replace?',
                    'short_description' => 'UV haze steals throw distance at night—here is how to decide between DIY restoration and new housings.',
                    'long_description' => '<p>Polycarbonate lenses oxidize in sun and road chemicals, scattering light and making high beams look dim even when bulbs are new.</p><p>Light <strong>restoration kits</strong> sand and polish the outer layer, then add UV protectant. They work best on mild haze; deep cracks, moisture inside the lamp, or broken mounts mean replacement assemblies are safer.</p><p>After polishing, re-aim headlights if the pattern changed, or have a workshop verify cut-off lines—glare into oncoming traffic helps nobody.</p><p>If you frequently drive dark rural roads, fixing lenses is as much a courtesy to other drivers as it is a visibility upgrade for you.</p>',
                ],
                'tr' => [
                    'title' => 'Sararmış, mat farlar: cilalama mı, değişim mi?',
                    'short_description' => 'UV matlığı gece menzili düşürür—ev tipi restorasyon ile komple değişim arasında nasıl seçim yapılır?',
                    'long_description' => '<p>Polikarbon lensler güneş ve yol kimyasıyla oksitlenir; ışığı saçar ve ampul yeniyken bile uzun far zayıf görünür.</p><p>Hafif <strong>restorasyon setleri</strong> dış yüzeyi zımparalar, cilarlar ve UV koruyucu uygular. Bu yöntem hafif matlıkta işe yarar; derin çatlak, fanus içi nem veya kırık bağlantıda komple far daha güvenlidir.</p><p>Cilalama sonrası ışı deseni değiştiyse far ayarı veya serviste kesim çizgisi kontrolü yapın—karşı şeridi rahatsız eden glare kimseye fayda etmez.</p><p>Sık karanlık yol kullanıyorsanız lens bakımı hem sizin görüşünüz hem karşı trafik nezaketi içindir.</p>',
                ],
            ],
            [
                'published_at' => '2024-12-18 08:00:00',
                'image' => $u(3807335),
                'en' => [
                    'title' => 'Serpentine belt squeal: tensioner wear and why it matters',
                    'short_description' => 'A chirp at startup can mean a slipping belt driving the alternator, pump, and steering assist—do not ignore long squeals under load.',
                    'long_description' => '<p>The serpentine belt powers the alternator, water pump on many engines, power steering pump or electric-assist feed, and often the A/C compressor. When it slips, you hear a brief squeal—usually worse when cold or when the steering is at full lock.</p><p>Causes include a <strong>worn automatic tensioner</strong> that no longer holds steady pressure, a glazed or cracked belt, or a seized pulley bearing that loads the belt unevenly.</p><p>Occasional chirp on the first start after rain may be harmless moisture; continuous squeal while driving or heavy smoke from the belt path means stop and inspect—overheating and sudden loss of charging or cooling can follow a thrown belt.</p><p>A workshop can check belt deflection or tensioner travel and spin each pulley for noise. Replacing belt and tensioner together is common when mileage is high.</p>',
                ],
                'tr' => [
                    'title' => 'V kayışı/arka kayış cızırtısı: gergi aşınması ve risk',
                    'short_description' => 'Marşta çıkan cızırtı alternatör, pompa ve direksiyonu çeviren kayışta kayma olabilir—yük altında uzun süren cızırtıyı ihmal etmeyin.',
                    'long_description' => '<p>Çok kanallı (serpantin) kayış alternatörü, birçok motorda su pompasını, hidrolik veya destekli direksiyonu ve çoğu zaman klimayı çevirir. Kaydığında genelde soğukta veya direksiyon son kilitte kısa cızırtı duyulur.</p><p>Nedenler arasında <strong>yıpranmış otomatik gergi</strong>, matlaşmış veya çatlamış kayış veya rulmanı sıkışmış bir kasnak sayılabilir.</p><p>Yağmurdan sonra ilk marştaki tek seferlik ses zararsız olabilir; sürüşte sürekli cızırtı veya kayış hattından yoğun duman varsa durup bakın—kayış atması şarj veya soğutma kaybına ve ısınmaya yol açabilir.</p><p>Serviste gergi hareketi, kasnak dönüşü ve gerekirse kayış+gergi birlikte değişimi değerlendirilir; yüksek kilometrede ikisi birlikte yenilenir.</p>',
                ],
            ],
        ];
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
