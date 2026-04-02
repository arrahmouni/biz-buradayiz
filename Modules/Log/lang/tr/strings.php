<?php

return [
    'services'                      => [
        'firebase'                  => 'Firebase',
        'google'                    => 'Google',
        'sendgrid'                  => 'Sendgrid',
        'aws'                       => 'AWS',
    ],

    'statuses'                      => [
        'success'                   => 'Başarı',
        'failed'                    => 'Basarisiz',
    ],

    'system'                        => 'Sistem',
    'details'                       => 'Detaylar',
    'view_modal_title'              => [
        'api_log'                   => 'API Günlüğü Ayrıntıları',
        'activity_log'              => 'Etkinlik Günlüğü Ayrıntıları',
    ],
    'audit'                         => [
        'events'                    => [
            'created'               => 'Oluşturuldu',
            'updated'               => 'Güncellendi',
            'deleted'               => 'Silindi',
            'restored'              => 'Geri yüklendi',
            'role_changed'          => 'Rol Değiştirildi',
            'update_translation'    => 'Çeviriyi Güncelle',
        ],
        'metadata'                  => ':audit_created_at, :user_full_name [:audit_ip_address] <strong class="text-decoration-underline">:audit_event</strong> tarihinde bu kayıt <a class="text-decoration-underline" href=":audit_url" target="_blank">:audit_url</a> aracılığıyla',
        'modified'                  => '<strong class="text-decoration-underline text-warning">:attribute</strong> şu tarihten itibaren değiştirildi:',
    ],
];
