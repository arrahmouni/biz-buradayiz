<?php

return [
    'base_crud'                     => [
        'title'                     => [
            'label'                 => 'Başlık [:locale]',
            'placeholder'           => 'Lütfen bir başlık girin',
            'help'                  => 'Bir başlık girin (gerekli)',
        ],
        'name'                      => [
            'label'                 => 'Ad [:locale]',
            'placeholder'           => 'Lütfen bir ad girin',
            'help'                  => 'Bir ad girin (gerekli)',
        ],
        'description'               => [
            'label'                 => 'Açıklama [:locale]',
            'placeholder'           => 'Lütfen bir açıklama girin',
            'help'                  => 'Bir açıklama girin',
        ],
        'long_description'          => [
            'label'                 => 'Genel Açıklama [:locale]',
            'placeholder'           => 'Lütfen genel bir açıklama girin',
            'help'                  => 'Genel bir açıklama girin',
            'subText'               => 'İçeriğin genel görünürlüğünü iyileştirmek için genel bir açıklama belirleyin.',
        ],
        'email'                     => [
            'label'                 => 'E-posta',
            'placeholder'           => 'E-posta Adresi',
            'help'                  => 'Lütfen E-posta Adresini girin',
        ],
        'current_password'          => [
            'label'                 => 'Mevcut Şifre',
            'placeholder'           => 'Mevcut Şifre',
            'help'                  => 'Lütfen mevcut şifrenizi giriniz',
        ],
        'password'                  => [
            'label'                 => 'Şifre',
            'placeholder'           => 'Şifre',
            'help'                  => 'Şifre en az 8 karakter uzunluğunda olmalı ve en az bir büyük harf, bir küçük harf, bir sayı ve bir özel karakter içermelidir. örnek: Abc@1234',
        ],
        'password_confirmation'      => [
            'label'                 => 'Şifre Doğrulaması',
            'placeholder'           => 'Şifre Doğrulaması',
            'help'                  => 'Lütfen yukarıdakiyle aynı şifreyi girin',
        ],
        'image'                     => [
            'label'                 => 'Resim',
            'placeholder'           => 'Lütfen bir resim seçin',
            'help'                  => 'Bir resim seçin',
            'subText'               => 'İzin verilen dosya türleri: :types | Maksimum dosya boyutu: :size MB',
        ],
        'image_lang'                => [
            'label'                 => 'Resim [:locale]',
            'placeholder'           => 'Lütfen bir resim seçin',
            'help'                  => 'Bir resim seçin',
            'subText'               => 'İzin verilen dosya türleri: :types | Maksimum dosya boyutu: :size MB',
        ],
        'status'                    => [
            'label'                 => 'Durum',
            'placeholder'           => 'Lütfen bir durum seçin',
            'help'                  => 'Bir durum seçin (gerekli)',
        ],
        'lang'                      => [
            'label'                 => 'Dil',
            'placeholder'           => 'Lütfen bir Dil seçin',
            'help'                  => 'Bir Dil Seçin (gerekli)',
        ],
        'date_range'                => [
            'label'                 => 'Tarih Aralığı',
            'placeholder'           => 'Lütfen bir tarih aralığı seçin',
            'help'                  => 'Bir tarih aralığı seçin',
        ],
        'phone'                     => [
            'label'                 => 'Telefon',
            'placeholder'           => 'Lütfen bir Telefon girin',
            'help'                  => 'Telefon Giriniz (gerekli)',
        ],
    ],

    'role_crud'                     => [
        'code'                      => [
            'label'                 => 'Kod',
            'placeholder'           => 'Lütfen bir kod girin',
            'help'                  => 'Kod benzersiz olmalı ve yalnızca büyük harfler ve alt çizgilerden oluşmalıdır. örnek: ADMIN, CUSTOMER_SERVICE',
        ],
    ],

    'permission_crud'               => [
        'permission_type'           => [
            'label'                 => 'İzin Türü',
            'placeholder'           => 'Lütfen bir izin türü seçin',
            'help'                  => 'Bir izin türü seçin',
            'data'                  => [
                'group_permission'  => 'Grup İzinleri (CRUD)',
                'sigle_permission'  => 'Tek İzinler',
            ],
        ],
        'permission_group'          => [
            'label'                 => 'İzin Grubu',
            'placeholder'           => 'Lütfen bir İzin grubu seçin',
            'help'                  => 'Lütfen izin eklemek için bir İzin grubu seçin (gerekli)',
        ],
        'permission_name'           => [
            'label'                 => 'İzin Adı',
            'placeholder'           => 'Lütfen bir izin adı girin',
            'help'                  => 'İzin adı, ait olduğu grubun adıyla bitmelidir. örnek: CREATE_ROLE',
        ],
        'code'                      => [
            'label'                 => 'Kod',
            'placeholder'           => 'Lütfen bir kod girin',
            'help'                  => 'Kod benzersiz olmalı ve yalnızca büyük harfler ve alt çizgilerden oluşmalıdır. örnek: CONTENT, USER_MANAGEMENT',
        ],
        'icon'                      => [
            'label'                 => 'Simge',
            'placeholder'           => 'Lütfen bir simge girin',
            'help'                  => 'Font Awesome simgesi sınıf adı. örnek: fas fa-user, fas fa-cogs',
        ],
    ],

    'country_crud'                  => [
        'native_name'               => [
            'label'                 => 'Yerel Ad',
            'placeholder'           => 'Lütfen Yerel Ad girin',
            'help'                  => 'Yerel Ad Girin (gerekli)',
        ],
        'iso2'                      => [
            'label'                 => 'ISO2',
            'placeholder'           => 'Lütfen bir ISO2 girin',
            'help'                  => 'Bir ISO2 girin (gerekli)',
        ],
        'iso3'                      => [
            'label'                 => 'ISO3',
            'placeholder'           => 'Lütfen bir ISO3 girin',
            'help'                  => 'ISO3 girin (gerekli)',
        ],
        'phone_code'                => [
            'label'                 => 'Telefon Kodu',
            'placeholder'           => 'Lütfen bir Telefon Kodu girin',
            'help'                  => 'Telefon Kodu Girin (gerekli)',
        ],
        'currency'                  => [
            'label'                 => 'Para birimi',
            'placeholder'           => 'Lütfen bir Para Birimi girin',
            'help'                  => 'Para Birimi Girin (gerekli)',
        ],
        'currency_symbol'           => [
            'label'                 => 'Para Birimi Sembolü',
            'placeholder'           => 'Lütfen bir Para Birimi Sembolü girin',
            'help'                  => 'Para Birimi Simgesini Girin (gerekli)',
        ],
        'lat'                       => [
            'label'                 => 'Enlem',
            'placeholder'           => 'Lütfen bir Enlem girin',
            'help'                  => 'Enlem girin (gerekli)',
        ],
        'lng'                       => [
            'label'                 => 'Boylam',
            'placeholder'           => 'Lütfen bir Boylam girin',
            'help'                  => 'Boylam girin (gerekli)',
        ],
    ],

    'admin_crud'                    => [
        'full_name'                 => [
            'label'                 => 'Ad Soyad',
            'placeholder'           => 'Lütfen Tam Ad girin',
            'help'                  => 'Tam Ad Girin (gerekli)',
        ],
        'username'                  => [
            'label'                 => 'Kullanıcı adı',
            'placeholder'           => 'Lütfen bir Kullanıcı Adı girin',
            'help'                  => 'Kullanıcı adı benzersiz olmalı ve yalnızca İngilizce harfler, rakamlar ve alt çizgilerden oluşmalıdır. örnek: john_doe',
        ],
        'phone_number'              => [
            'label'                 => 'Telefon Numarası',
            'placeholder'           => 'Telefon Numarası',
            'help'                  => 'Telefon Numarası Girin',
        ],
        'Avatar'                    => [
            'label'                 => 'Avatar',
            'placeholder'           => 'Lütfen bir Avatar seçin',
            'help'                  => 'Bir Avatar seçin',
            'subText'               => 'İzin verilen dosya türleri: :types | Maksimum dosya boyutu: :size MB',
        ],
        'gender'                    => [
            'label'                 => 'Cinsiyet',
            'placeholder'           => 'Lütfen Cinsiyet seçiniz',
            'help'                  => 'Cinsiyet Seçiniz (gerekli)',
        ],
    ],

    'setting_crud'                  => [
        'group'                     => [
            'label'                 => 'Grup',
            'placeholder'           => 'Lütfen bir grup seçin',
            'help'                  => 'Bir grup seçin (gerekli)',
        ],
        'type'                      => [
            'label'                 => 'Tur',
            'placeholder'           => 'Lütfen bir tür seçin',
            'help'                  => 'Bir tür seçin (gerekli)',
        ],
        'key'                       => [
            'label'                 => 'Anahtar',
            'placeholder'           => 'Lütfen bir anahtar girin',
            'help'                  => 'Anahtar benzersiz olmalı ve yalnızca küçük harfler, sayılar ve alt çizgiler içermelidir. örnek: site_başlığı, site_açıklaması',
        ],
        'value'                     => [
            'label'                 => 'Değer',
            'placeholder'           => 'Lütfen bir değer girin',
            'help'                  => 'Bir değer girin (gerekli)',
        ],
        'options'                   => [
            'label'                 => 'Seçenekler',
            'placeholder'           => 'Lütfen seçenekleri girin',
            'help'                  => 'Seçenekler JSON formatında girilir. örnek: {"anahtar": "değer"}. Bu alan, seçim türü için gereklidir',
        ],
        'order'                     => [
            'label'                 => 'Sira',
            'placeholder'           => 'Lutfen bir sira girin',
            'help'                  => 'Sira girin (gerekli)',
        ],
        'is_required'               => [
            'label'                 => 'Gereklidir ?',
            'placeholder'           => 'Lütfen gerekli olanı seçin',
            'help'                  => 'Seçmek gerekli mi?',
        ],
        'translatable'              => [
            'label'                 => 'Değerin çevirisi var mı?',
            'placeholder'           => 'Lütfen çevrilebilir olanı seçin',
            'help'                  => 'Çevrilebilir olanı seçin (gerekli)',
        ],
        'trans_value'               => [
            'label'                 => 'Çeviri Değeri [:locale]',
            'placeholder'           => 'Lütfen bir çeviri değeri girin',
            'help'                  => 'Bir çeviri değeri girin',
        ],
    ],

    'content_category_crud'         => [
        'slug'                      => [
            'label'                 => 'Slug',
            'placeholder'           => 'Lutfen bir slug girin',
            'subText'               => 'Slug, kategorinin URL\'sini oluşturmak için kullanılır. Kategoriyi oluşturduktan sonra bilgiyi değiştiremezsiniz.',
            'help'                  => 'Slug benzersiz olmali ve yalnızca küçük harfler ve tire içermelidir. örnek: haberler, makaleler, bloglar',
        ],

        'parent_id'                 => [
            'label'                 => 'Ana Kategori',
            'placeholder'           => 'Bir ust kategori secin (istege bagli)',
            'help'                  => 'Bir ust kategori secin (istege bagli)',
        ],
        'can_be_deleted'            => [
            'label'                 => 'Silinebilir',
            'placeholder'           => 'Lutfen Silinebilir alanini secin',
            'help'                  => 'Silinebilir\'i seçin',
        ],
    ],

    'contents_crud'                 => [
        'published_at'              => [
            'label'                 => 'Yayınlanma Tarihi',
            'placeholder'           => 'Lütfen bir tarih seçin',
            'help'                  => 'Bir tarih seçin (gerekli)',
        ],
        'image'                     => [
            'label'                 => 'İçerik Resmi [:locale]',
            'help'                  => 'Gorsel boyutunun: :dimensions ve gorsel turunun: :types olmasi tercih edilir.',
            'subText'               => 'Gorsel boyutunun: :dimensions ve gorsel turunun: :types olmasi tercih edilir.',
            'placeholder'           => '',
        ],
        'slug'                      => [
            'label'                 => 'Slug',
            'placeholder'           => 'Lutfen bir slug girin',
            'help'                  => 'Slug, içeriğin URL\'sini oluşturmak için kullanılır. İçeriği oluşturduktan sonra bilgiyi değiştiremezsiniz.',
            'subText'               => 'Slug benzersiz olmali ve yalnızca küçük harfler ve tire içermelidir. örnek: haberler, makaleler, bloglar',
        ],
        'sliders'                   => [
            'placement'             => [
                'label'             => 'Slider Konumu',
                'placeholder'       => 'Lütfen bir yerleşim seçin',
                'help'              => 'Bir yerleşim seçin (gerekli)',
            ],
            'link'                  => [
                'label'             => 'Bağlantı',
                'placeholder'       => 'https://example.com',
                'help'              => 'Bir bağlantı girin (isteğe bağlı)',
            ],
        ],
        'blogs'                     => [
            'tags'                  => [
                'label'             => 'Etiketler',
                'placeholder'       => 'Lütfen etiketleri seçin',
                'help'              => 'Etiketleri seçin',
            ],
        ],
    ],

    'category_crud'                 => [
        'image'                     => [
            'label'                 => 'Kategori Resmi [:locale]',
            'help'                  => 'Gorsel boyutunun: :dimentions ve gorsel turunun: :types olmasi tercih edilir.',
            'subText'               => 'Gorsel boyutunun: :dimentions ve gorsel turunun: :types olmasi tercih edilir.',
            'placeholder'           => '',
        ],
    ],

    'user_crud'                     => [
        'first_name'                => [
            'label'                 => 'Ad',
            'placeholder'           => 'Lutfen bir ad girin',
            'help'                  => 'Bir ad girin (gerekli)',
        ],

        'last_name'                 => [
            'label'                 => 'Soyad',
            'placeholder'           => 'Lutfen bir soyad girin',
            'help'                  => 'Bir soyad girin (gerekli)',
        ],

        'image'                     => [
            'subText'               => 'İzin verilen dosya türleri: :types | Maksimum dosya boyutu: :size MB',
        ],

        'service_id'                => [
            'label'                 => 'Hizmet türü',
            'placeholder'           => 'Bir hizmet seçin',
            'help'                  => 'Hizmet türünü seçin (gerekli)',
        ],

        'country_id'                => [
            'label'                 => 'Ülke',
            'placeholder'           => 'Bir ülke seçin',
            'help'                  => 'Ülke seçin (hizmet sağlayıcılar için gerekli)',
        ],

        'state_id'                  => [
            'label'                 => 'Şehir',
            'placeholder'           => 'Bir şehir seçin',
            'help'                  => 'Şehir seçin (hizmet sağlayıcılar için gerekli)',
        ],

        'city_id'                   => [
            'label'                 => 'İlçe',
            'placeholder'           => 'Bir ilçe seçin',
            'help'                  => 'İlçe seçin (hizmet sağlayıcılar için gerekli)',
        ],
    ],

    'notification_template_crud'    => [
        'name'                      => [
            'label'                 => 'Ad',
            'placeholder'           => 'Lutfen bir ad girin',
            'help'                  => 'Bir ad girin (gerekli)',
            'subText'               => 'Ad benzersiz olmalı ve yalnızca küçük harfler, rakamlar ve alt çizgilerden oluşmalıdır. örnek: user_registered, order_created',
        ],
        'priority'                  => [
            'label'                 => 'Öncelik',
            'placeholder'           => 'Lütfen bir Öncelik seçin',
            'help'                  => 'Bir Öncelik Seçin (gerekli)',
        ],
        'channels'                  => [
            'label'                 => 'Kanallar',
            'placeholder'           => 'Lütfen Kanalları seçin',
            'help'                  => 'Kanalları Seçin (gerekli)',
        ],
        'variables'                 => [
            'label'                 => 'Değişkenler',
            'placeholder'           => 'Lütfen Değişkenleri seçin',
            'help'                  => 'Değişkenleri Seçin (gerekli)',
            'subText'               => 'Sablonda kullanilacak degiskenleri girin. ornek: user_name, order_number',
        ],
        'short_template'            => [
            'label'                 => 'Kısa Şablon',
            'placeholder'           => 'Lütfen Kısa Şablon girin',
            'help'                  => 'Kısa Şablon Girin (gerekli)',
            'subText'               => 'Kısa şablon SMS ve push bildirimleri için kullanılır.',
        ],
        'long_template'             => [
            'label'                 => 'Uzun Şablon',
            'placeholder'           => 'Lütfen Uzun bir Şablon girin',
            'help'                  => 'Uzun Şablon Girin (gerekli)',
            'subText'               => 'E-posta bildirimleri için uzun şablon kullanılır.',
        ],
    ],

    'notification_crud'             => [
        'body'                      => [
            'label'                 => 'Icerik',
            'placeholder'           => 'Lütfen bir Gövde girin',
            'help'                  => 'Bir Gövde Girin (gerekli)',
        ],
        'link'                      => [
            'label'                 => 'Bağlantı',
            'placeholder'           => 'https://example.com',
            'help'                  => 'Bir baglanti girin (istege bagli)',
            'subText'               => 'Kullanıcı fcm_web ve fcm_mobile kanallarına ilişkin bildirime tıkladığında yönlendirilecek bağlantı.',
        ],
        'recipient'                 => [
            'label'                 => 'Alıcı',
            'placeholder'           => 'Lütfen bir Alıcı seçin',
            'help'                  => 'Alıcı Seçin (gerekli)',
        ],
        'notification_template'     => [
            'label'                 => 'Bildirim Şablonu',
            'placeholder'           => 'Lütfen bir Bildirim Şablonu seçin',
            'help'                  => 'Bir Bildirim Şablonu Seçin (gerekli)',
        ],
        'added_by'                  => [
            'label'                 => 'Ekleyen',
            'placeholder'           => 'Lütfen bir Ekleyen seçin',
            'help'                  => 'Ekleyen birini seçin (gerekli)',
        ],
        'groups'                    => [
            'label'                 => 'Gruplar',
            'placeholder'           => 'Lütfen Grupları seçin',
            'help'                  => 'Grupları Seçin (gerekli)',
        ],
        'users'                     => [
            'label'                 => 'Kullanıcılar',
            'placeholder'           => 'Lütfen Kullanıcıları seçin',
            'help'                  => 'Kullanıcıları Seçin',
            'subText'               => 'Hiçbir kullanıcı seçilmezse bildirim seçilen gruplardaki tüm kullanıcılara gönderilecektir.',
        ],
        'admins'                    => [
            'label'                 => 'Yöneticiler',
            'placeholder'           => 'Lütfen Yöneticileri seçin',
            'help'                  => 'Yöneticileri seçin',
            'subText'               => 'Hiçbir yönetici seçilmezse bildirim tüm yöneticilere gönderilecektir.',
        ],
        'channels'                  => [
            'label'                 => 'Kanallar',
            'placeholder'           => 'Lütfen Kanalları seçin',
            'help'                  => 'Kanalları Seçin (gerekli)',
            'subText'               => 'Uyarı: FCM_MOBILE veya FCM_WEB\'i seçerseniz ve bildirimi tüm kullanıcılara göndermek istiyorsanız, diğer kanalları (e-posta veya SMS gibi) seçemezsiniz. Bunun nedeni, bildirimin FCM\'ye özel bir konu aracılığıyla gönderilecek olmasıdır.',
        ],
    ],

    'contactus_crud'                => [
        'first_name'                => [
            'label'                 => 'Ad',
            'placeholder'           => 'Lutfen bir ad girin',
            'help'                  => 'Bir ad girin (gerekli)',
        ],
        'last_name'                 => [
            'label'                 => 'Soyad',
            'placeholder'           => 'Lutfen bir soyad girin',
            'help'                  => 'Bir soyad girin (gerekli)',
        ],
        'message'                   => [
            'label'                 => 'Mesaj',
            'placeholder'           => 'Lütfen bir Mesaj girin',
            'help'                  => 'Mesaj Girin (gerekli)',
        ],
        'reply'                     => [
            'label'                 => 'Yanit',
            'placeholder'           => 'Lütfen bir Yanıt girin',
            'help'                  => 'Bir Yanıt Girin',
        ],
        'ip_address'                => [
            'label'                 => 'IP Adresi',
            'placeholder'           => 'Lütfen bir IP Adresi girin',
            'help'                  => 'Bir IP Adresi girin (gerekli)',
        ],
        'user_agent'                => [
            'label'                 => 'Kullanici Araci',
            'placeholder'           => 'Lütfen bir Kullanici Araci girin',
            'help'                  => 'Bir Kullanici Araci girin (gerekli)',
        ],
        'submission_date'           => [
            'label'                 => 'Gönderim Tarihi',
            'placeholder'           => 'Lütfen bir Gönderim Tarihi seçin',
            'help'                  => 'Gönderim Tarihi Seçin (gerekli)',
        ],
    ],

    'api_log_crud'                  => [
        'service_name'              => [
            'label'                 => 'Hizmet Adı',
            'placeholder'           => 'Lütfen bir Hizmet Adı girin',
            'help'                  => 'Hizmet Adı Girin (gerekli)',
        ],
        'method'                    => [
            'label'                 => 'Metod',
            'placeholder'           => 'Lütfen bir Metod girin',
            'help'                  => 'Bir Metod Girin (gerekli)',
        ],
        'endpoint'                  => [
            'label'                 => 'Endpoint',
            'placeholder'           => 'Lütfen bir Uç Nokta girin',
            'help'                  => 'Bir Uç Nokta Girin (gerekli)',
        ],
        'request'                   => [
            'label'                 => 'Istek',
            'placeholder'           => 'Lütfen bir Talep girin',
            'help'                  => 'Bir Talep Girin (gerekli)',
        ],
        'response'                  => [
            'label'                 => 'Yanit',
            'placeholder'           => 'Lütfen bir Yanıt girin',
            'help'                  => 'Bir Yanıt Girin (gerekli)',
        ],
        'status'                    => [
            'label'                 => 'Durum',
            'placeholder'           => 'Lutfen bir durum secin',
            'help'                  => 'Bir durum secin (gerekli)',
        ],
        'status_code'               => [
            'label'                 => 'Durum Kodu',
            'placeholder'           => 'Lütfen bir Durum Kodu girin',
            'help'                  => 'Durum Kodu Girin (gerekli)',
        ],
    ],
];
