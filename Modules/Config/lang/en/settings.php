<?php

return [
    'groups'                                => [
        'general'                           => [
            'title'                         => 'General',
            'fields'                        => [
                'app_name'                  => 'App Title',
                'app_default_language'      => 'App Default Language',
                'vat_rate'                  => 'VAT Rate (%)',
                'maintenance_mode'          => 'Maintenance Mode',
            ],
        ],
        'social_media'                      => [
            'title'                         => 'Social Media',
            'fields'                        => [
                'facebook'                  => 'Facebook',
                'twitter'                   => 'Twitter',
                'instagram'                 => 'Instagram',
                'linkedin'                  => 'Linkedin',
                'youtube'                   => 'Youtube',
                'tiktok'                    => 'Tiktok',
            ],
        ],
        'contact_info'                      => [
            'title'                         => 'Contact Info',
            'fields'                        => [
                'phone'                     => 'Phone',
                'email'                     => 'Email',
                'address'                   => 'Address',
            ],
        ],
        'emergency'                         => [
            'title'                         => 'Emergency',
            'fields'                        => [
                'emergency_contact_number'  => 'Emergency contact number (public site)',
            ],
        ],
        'media'                             => [
            'title'                         => 'Media',
            'fields'                        => [
                'app_logo'                  => 'Dashboard Logo',
                'app_mobile_logo'           => 'Dashboard Login Page Logo',
                'email_logo'                => 'Email Logo',
                'app_favicon'               => 'Favicon Logo',
                'app_placeholder'           => 'App Placeholder',
                'web_logo'                  => 'Web Logo',
            ],
        ],
        'developers'                        => [
            'title'                         => 'Developers',
            'fields'                        => [
                'clear_cache'               => 'Clear Cache',
                'clear_logs'                => 'Clear Logs',
                'reset_permissions'         => 'Reset Permissions',
                'session_lifetime'          => [
                    'title'                 => 'Session Lifetime',
                    'description'           => 'Session Lifetime in minutes',
                ],
                'allow_debug_for_custom_ip' => 'Allow Debug For Custom IP',
                'custom_ips'                => [
                    'title'                 => 'Custom IPs',
                    'description'           => 'Enter IPs separated by comma example: xxx.xxx.xxx.xxx, yyy.yyy.yyy.yyy',
                ],
            ],
        ],
    ],
];
