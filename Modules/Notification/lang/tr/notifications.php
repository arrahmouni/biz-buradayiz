<?php

return [
    'notifications'                                             => 'Bildirimler',
    'mark_all_as_read'                                          => 'Tumunu okundu olarak isaretle',
    'notification_templates'                                    => [
        'welcome_in_our_platform'                               => [
            'title'                                             => 'Platformumuza hoş geldiniz',
            'description'                                       => 'Bu kullanici icin bir hos geldiniz mesajidir',
            'short_template'                                    => '{{username}} platformumuza hos geldiniz',
            'long_template'                                     => '{{username}} platformumuza hos geldiniz',
        ],
        'priority'                                              => [
            'low'                                               => 'Düşük',
            'medium'                                            => 'Orta',
            'high'                                              => 'Yüksek',
            'default'                                           => 'Varsayılan',
        ],
    ],
    'statuses'                                                  => [
        'delivered'                                             => 'Teslim Edildi',
        'pending'                                               => 'Beklemede',
        'seen'                                                  => 'Goruldu',
        'failed'                                                => 'Basarisiz',
        'read'                                                  => 'Okundu',
    ],
    'added_by'                                                  => [
        'system'                                                => 'Sistem',
        'admin'                                                 => 'Yonetici',
    ],
];
