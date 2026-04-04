<?php

return [
    'ability_group' => [
        'role_management' => 'Silindir Yönetimi',
        'user_management' => 'Kullanıcıların Yönetimi',
        'permission_management' => 'Yetkiler Yönetimi',
        'country_management' => 'Ülke Yönetimi',
        'admin_management' => 'Yonetici Yonetimi',
        'setting_management' => 'Ayar Yönetimi',
        'content_category_management' => 'Icerik Kategori Yönetimi',
        'content_management' => 'Icerik Yonetimi',
        'sliders_management' => 'Icerik Yönetimi [Sliderlar]',
        'pages_management' => 'Icerik Yönetimi [Sayfalar]',
        'blogs_management' => 'Icerik Yönetimi [Bloglar]',
        'category_management' => 'Kategori Yönetimi',
        'categories_management' => 'Kategori Yönetimi',
        'content_tag_management' => 'Icerik Etiketi Yönetimi',
        'notification_template_management' => 'Bildirim Sablonu Yönetimi',
        'notification_management' => 'Bildirim Yönetimi',
        'contactus_management' => 'Bize Ulaşın',
        'subscribe_management' => 'Yonetimi\'ye abone ol',
        'api_log_management' => 'API Günlüğü Yonetimi',
        'service_management' => 'Hizmet Yönetimi',
        'package_management' => 'Paket Yönetimi',
    ],

    'roles' => [
        'root' => [
            'name' => 'ROOT',
            'title' => 'Kök',
            'description' => 'Tüm izinler varsayılan olarak bu gruba eklenir',
        ],
        'system_admin' => [
            'name' => 'SYSTEM_ADMIN',
            'title' => 'Sistem Yonetici',
            'description' => 'Tüm güvenli izinler varsayılan olarak bu gruba eklenir',
        ],
        'user' => [
            'name' => 'USER',
            'title' => 'Müşteri / Müşteri',
            'description' => 'Bu grup için varsayılan izin yok',
        ],
        'employee' => [
            'name' => 'EMPLOYEE',
            'title' => 'Çalışan',
            'description' => 'Bu grup için varsayılan izin yok',
        ],
    ],

    'main_roles' => [
        'admins' => 'Yoneticiler',
        'users' => 'Kullanıcılar',
    ],
];
