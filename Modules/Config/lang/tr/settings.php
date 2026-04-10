<?php

return [
    'groups' => [
        'general' => [
            'title' => 'Genel',
            'fields' => [
                'app_name' => 'Uygulama Başlığı',
                'app_default_language' => 'Uygulama Varsayılan Dili',
                'vat_rate' => 'KDV Oranı (%)',
                'maintenance_mode' => 'Bakım Modu',
            ],
        ],
        'social_media' => [
            'title' => 'Sosyal Medya',
            'fields' => [
                'facebook' => 'Facebook',
                'twitter' => 'Twitter',
                'instagram' => 'Instagram',
                'linkedin' => 'LinkedIn',
                'youtube' => 'YouTube',
                'tiktok' => 'TikTok',
            ],
        ],
        'contact_info' => [
            'title' => 'Iletisim Bilgileri',
            'fields' => [
                'phone' => 'Telefon',
                'email' => 'E-posta',
                'address' => 'Adres',
                'contact_map_embed_url' => 'İletişim sayfası harita gömme URL’si (Google Haritalar iframe src)',
            ],
        ],
        'platform' => [
            'title' => 'Platform',
            'fields' => [
                'emergency_contact_number' => 'Acil iletişim numarası (herkese açık site)',
                'front_search_default_country_id' => 'Varsayılan ülke kimliği (herkese açık site konum araması)',
            ],
        ],
        'media' => [
            'title' => 'Medya',
            'fields' => [
                'app_logo' => 'Kontrol Paneli Logosu',
                'app_mobile_logo' => 'Kontrol Paneli Giriş Logosu',
                'email_logo' => 'E-posta Logosu',
                'app_favicon' => 'Favicon Simgesi',
                'app_placeholder' => 'Uygulama Yer Tutucusu',
                'web_logo' => 'Web Logosu',
            ],
        ],
        'mobile' => [
            'title' => 'Mobil uygulamalar',
            'fields' => [
                'app_store' => 'App Store (iOS) bağlantısı',
                'google_play' => 'Google Play (Android) bağlantısı',
            ],
        ],
        'developers' => [
            'title' => 'Geliştiriciler',
            'fields' => [
                'clear_cache' => 'Önbelleği Temizle',
                'clear_logs' => 'Günlükleri Temizle',
                'reset_permissions' => 'Yetkileri Sifirla',
                'session_lifetime' => [
                    'title' => 'Oturum Ömrü',
                    'description' => 'Dakika cinsinden oturum suresi',
                ],
                'allow_debug_for_custom_ip' => 'Ozel IP icin debug izni ver',
                'custom_ips' => [
                    'title' => 'Özel IP\'ler',
                    'description' => 'IP\'leri virgul ile ayirarak girin. ornek: xxx.xxx.xxx.xxx, yyy.yyy.yyy.yyy',
                ],
            ],
        ],
    ],
];
