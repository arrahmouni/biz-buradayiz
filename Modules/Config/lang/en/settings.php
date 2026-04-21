<?php

return [
    'groups' => [
        'general' => [
            'title' => 'General',
            'fields' => [
                'app_name' => 'App Title',
                'app_default_language' => 'App Default Language',
                'vat_rate' => 'VAT Rate (%)',
                'maintenance_mode' => 'Maintenance Mode',
                'coming_soon_mode' => 'Coming soon page (public site)',
                'website_launch_date' => 'Website launch date',
            ],
        ],
        'social_media' => [
            'title' => 'Social Media',
            'fields' => [
                'facebook' => 'Facebook',
                'twitter' => 'Twitter',
                'instagram' => 'Instagram',
                'linkedin' => 'Linkedin',
                'youtube' => 'Youtube',
                'tiktok' => 'Tiktok',
            ],
        ],
        'contact_info' => [
            'title' => 'Contact Info',
            'fields' => [
                'phone' => 'Phone',
                'email' => 'Email',
                'address' => 'Address',
                'contact_map_embed_url' => 'Contact page map embed URL (Google Maps iframe src)',
            ],
        ],
        'platform' => [
            'title' => 'Platform',
            'fields' => [
                'emergency_contact_number' => 'Emergency contact number (public site)',
                'front_search_default_country_id' => 'Default country ID (public site location search)',
                'provider_register_landing_youtube_url' => 'Provider registration landing — YouTube video URL (watch, youtu.be, or embed link)',
                'provider_subscription_whatsapp_e164' => 'WhatsApp number for provider payment receipts (E.164, e.g. +905551234567)',
                'provider_bank_transfer_instructions' => 'Bank transfer instructions shown on the provider dashboard',
            ],
        ],
        'media' => [
            'title' => 'Media',
            'fields' => [
                'app_logo' => 'Dashboard Logo',
                'app_mobile_logo' => 'Dashboard Login Page Logo',
                'email_logo' => 'Email Logo',
                'app_favicon' => 'Favicon Logo',
                'app_placeholder' => 'App Placeholder',
                'web_logo' => 'Web Logo',
                'loader_logo' => 'Page loader logo',
                'front_hero_background' => 'Public site hero background',
            ],
        ],
        'mobile' => [
            'title' => 'Mobile apps',
            'fields' => [
                'app_store' => 'App Store (iOS) link',
                'google_play' => 'Google Play (Android) link',
            ],
        ],
        'developers' => [
            'title' => 'Developers',
            'fields' => [
                'clear_cache' => 'Clear Cache',
                'clear_logs' => 'Clear Logs',
                'reset_permissions' => 'Reset Permissions',
                'session_lifetime' => [
                    'title' => 'Session Lifetime',
                    'description' => 'Session Lifetime in minutes',
                ],
                'allow_debug_for_custom_ip' => 'Allow Debug For Custom IP',
                'custom_ips' => [
                    'title' => 'Custom IPs',
                    'description' => 'Enter IPs separated by comma example: xxx.xxx.xxx.xxx, yyy.yyy.yyy.yyy',
                ],
            ],
        ],
        'provider_ranking' => [
            'title' => 'Provider Ranking',
            'fields' => [
                'featured_providers_count' => 'Number of featured providers',
                'new_provider_hours' => 'New provider window (hours)',
                'ranking_weight_rating' => 'Rating weight (%)',
                'ranking_weight_activity' => 'Activity weight (%)',
                'ranking_weight_experience' => 'Experience/Seniority weight (%)',
            ],
        ],
    ],
    'ranking_weights_sum_exceeded' => 'The total of ranking weights (Rating + Activity + Experience) must not exceed 100.',
    'validation' => [
        'website_launch_date_required_when_coming_soon' => 'Website launch date is required when the coming soon page is enabled.',
    ],
];
