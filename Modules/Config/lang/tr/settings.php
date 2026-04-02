<?php

return [
    'groups'                                => [
        'general'                           => [
            'title'                         => 'Genel',
            'fields'                        => [
                'app_name'                  => 'Uygulama Başlığı',
                'app_default_language'      => 'Uygulama Varsayılan Dili',
                'vat_rate'                  => 'KDV Oranı (%)',
                'maintenance_mode'          => 'Bakım Modu',
            ],
        ],
        'social_media'                      => [
            'title'                         => 'Sosyal Medya',
            'fields'                        => [
                'facebook'                  => 'Facebook',
                'twitter'                   => 'Twitter',
                'instagram'                 => 'Instagram',
                'linkedin'                  => 'LinkedIn',
                'youtube'                   => 'YouTube',
                'tiktok'                    => 'TikTok',
            ],
        ],
        'contact_info'                      => [
            'title'                         => 'Iletisim Bilgileri',
            'fields'                        => [
                'phone'                     => 'Telefon',
                'email'                     => 'E-posta',
                'address'                   => 'Adres',
            ],
        ],
        'media'                             => [
            'title'                         => 'Medya',
            'fields'                        => [
                'app_logo'                  => 'Uygulama Logosu',
                'app_mobile_logo'           => 'Uygulama Mobil Logosu',
                'email_logo'                => 'E-posta Logosu',
                'app_favicon'               => 'Uygulama Sık Kullanılan Simgesi',
                'app_placeholder'           => 'Uygulama Yer Tutucusu',
            ],
        ],
        'developers'                        => [
            'title'                         => 'Geliştiriciler',
            'fields'                        => [
                'clear_cache'               => 'Önbelleği Temizle',
                'clear_logs'                => 'Günlükleri Temizle',
                'reset_permissions'         => 'Yetkileri Sifirla',
                'session_lifetime'          => [
                    'title'                 => 'Oturum Ömrü',
                    'description'           => 'Dakika cinsinden oturum suresi',
                ],
                'allow_debug_for_custom_ip' => 'Ozel IP icin debug izni ver',
                'custom_ips'                => [
                    'title'                 => 'Özel IP\'ler',
                    'description'           => 'IP\'leri virgul ile ayirarak girin. ornek: xxx.xxx.xxx.xxx, yyy.yyy.yyy.yyy',
                ],
            ],
        ],
    ],
];
