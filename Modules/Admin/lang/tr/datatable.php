<?php

return [
    'datatable'                         => asset('lang/tr/datatable.json'),
    'base_columns'                      => [
        'id'                            => 'ID',
        'image'                         => 'Resim',
        'name'                          => 'Ad',
        'slug'                          => 'Kisa Ad',
        'username'                      => 'Kullanici Adi',
        'gender'                        => 'Cinsiyet',
        'title'                         => 'Baslik',
        'type'                          => 'Tur',
        'status'                        => 'Durum',
        'description'                   => 'Aciklama',
        'email'                         => 'E-posta',
        'phone_number'                  => 'Telefon Numarası',
        'created_at'                    => 'Oluşturulma Tarihi',
        'updated_at'                    => 'Guncellenme Tarihi',
        'deleted_at'                    => 'Silinme Tarihi',
        'created_by'                    => 'Oluşturan',
        'actions'                       => 'Islemler',
        'user_agent'                    => 'Kullanici Araci',
        'ip_address'                    => 'IP Adresi',
    ],
    'buttons'                           => [
        'export'                        => 'Disa Aktar',
        'refresh'                       => 'Yenile',
        'add_new'                       => 'Yeni Ekle',
        'select_action'                 => 'Islem Sec',
    ],
    'roles'                             => [
        'list_title'                    => 'Roller Listesi',
        'columns'                       => [
            'code'                      => 'Kod',
            'permissions'               => 'Yetkiler',
        ]
    ],
    'permissions'                       => [
        'list_title'                    => 'İzin Listesi',
        'columns'                       => [
            'code'                      => 'Kod',
        ]
    ],
    'countries'                         => [
        'list_title'                    => 'Ülke Listesi',
        'columns'                       => [
            'native_name'               => 'Yerel Ad',
            'phone_code'                => 'Telefon Kodu',
            'currency'                  => 'Para birimi',
            'states_count'              => 'Eyalet Sayısı',
            'cities_count'              => 'Şehir Sayısı',
        ]
    ],
    'admins'                            => [
        'list_title'                    => 'Yönetici Listesi',
        'columns'                       => [
            'user'                      => 'Kullanıcı',
            'role'                      => 'Rol',
            'lang'                      => 'Dil',
            'last_login_at'             => 'Son Giriş Tarihi',
            'joined_date'               => 'Katılma Tarihi',
        ]
    ],
    'content_categories'                => [
        'list_title'                    => 'İçerik Kategorileri Listesi',
        'columns'                       => [
            'slug'                      => 'Kisa Ad',
            'parent'                    => 'Ana Kategori',
            'can_be_deleted'            => 'Silinebilir',
        ]
    ],
    'contents'                          => [
        'list_title'                    => 'İçerik Listesi',
        'columns'                       => [
            'category'                  => 'Kategori',
            'updated_by'                => 'Güncelleyen',
            'published_at'              => 'Yayınlanma Tarihi',
        ],
        'sliders'                       => [
            'list_title'                => 'Kaydırıcı Listesi',
            'columns'                   => [
                'placement'             => 'Kaydırıcının Yerleştirilmesi',
            ]
        ],
        'blogs'                         => [
            'list_title'                => 'Blog Listesi',
        ],
        'pages'                         => [
            'list_title'                => 'Sayfa Listesi',
        ],
        'categories'                    => [
            'list_title'                => 'Kategori Listesi',
        ],
        'brands'                        => [
            'list_title'                => 'Marka Listesi',
        ],
        'shapes'                        => [
            'list_title'                => 'Şekiller Listesi',
        ],
        'types_of_tires'                => [
            'list_title'                => 'Lastik Çeşitleri Listesi',
        ],
        'colors'                        => [
            'list_title'                => 'Renk Listesi',
        ],
        'materials'                     => [
            'list_title'                => 'Malzeme Listesi',
        ],
        'proportions'                   => [
            'list_title'                => 'Oranlar Listesi',
        ],
        'gender'                        => [
            'list_title'                => 'Cinsiyet Listesi',
        ],
        'home_page'                     => [
            'list_title'                => 'Ana Sayfa Listesi',
        ],
    ],
    'categories'                        => [
        'list_title'                    => 'Kategori Listesi',
        'columns'                       => [
            'parent'                    => 'Ana Kategori',
            'can_be_deleted'            => 'Silinebilir',
        ]
    ],
    'users'                             => [
        'list_title'                    => 'Kullanıcı Listesi',
        'columns'                       => [
        ],
    ],
    'notification_templates'            => [
        'list_title'                    => 'Bildirim Şablonları Listesi',
        'columns'                       => [
            'priority'                  => 'Öncelik',
            'channels'                  => 'Kanallar',
            'variables'                 => 'Değişkenler',
        ],
    ],
    'notifications'                     => [
        'list_title'                    => 'Bildirim Listesi',
        'columns'                       => [
            'recipient'                 => 'Alıcı',
            'added_by'                  => 'Tarafından eklendi',
            'link'                      => 'Bağlantı',
            'sent_at'                   => 'Gönderilme tarihi',
        ],
    ],
    'contactuses'                       => [
        'list_title'                    => 'Bize Ulaşın Talep Listesi',
        'columns'                       => [
            'message'                   => 'Mesaj',
            'reply'                     => 'Cevap vermek',
            'submission_date'           => 'Gönderim tarihi',
        ],
    ],
    'subscribes'                        => [
        'list_title'                    => 'Abonelikler Listesi',
        'columns'                       => [
            'is_active'                 => 'Aktif',
            'subscription_date'         => 'Abonelik Tarihi',
        ],
    ],
    'content_tags'                      => [
        'list_title'                    => 'İçerik Etiketleri Listesi',
        'columns'                       => [
        ],
    ],
    'api_logs'                          => [
        'list_title'                    => 'API Günlükleri Listesi',
        'columns'                       => [
            'service_name'              => 'Hizmet Adı',
            'method'                    => 'Yöntem',
            'endpoint'                  => 'Uç nokta',
            'status_code'               => 'Durum Kodu',
            'error'                     => 'Hata',
            'request'                   => 'Rica etmek',
            'response'                  => 'Cevap',
        ],
    ],
    'activity_logs'                     => [
        'list_title'                    => 'Etkinlik Günlükleri Listesi',
        'columns'                       => [
            'user_made_action'          => 'Kullanıcı Tarafından Yapılan İşlem',
            'user_type'                 => 'Kullanıcı Türü',
            'event'                     => 'etkinlik',
            'old_values'                => 'Eski Değerler',
            'new_values'                => 'Yeni Değerler',
            'action_date'               => 'Eylem Tarihi',
        ],
    ],
];
