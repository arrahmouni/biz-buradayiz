<?php

return [
    'enable_notification_in_admin_panel'    => env('NOTIFICATION_ENABLE_IN_ADMIN_PANEL', true),

    'firebase_url'                          => env('FIREBASE_URL', ''),
    'firebase_credentials'                  => storage_path('firebase/firebase-auth.json'),
    'fallback_locale'                       => env('NOTIFICATION_FALLBACK_LOCALE', 'ar'),
    'firebase_config'                       => [
        'api_key'                           => env('FIREBASE_API_KEY'),
        'auth_domain'                       => env('FIREBASE_AUTH_DOMAIN'),
        'project_id'                        => env('FIREBASE_PROJECT_ID'),
        'storage_bucket'                    => env('FIREBASE_STORAGE_BUCKET'),
        'messaging_sender_id'               => env('FIREBASE_MESSAGING_SENDER_ID'),
        'app_id'                            => env('FIREBASE_APP_ID'),
        'measurement_id'                    => env('FIREBASE_MEASUREMENT_ID'),
    ],

    'firebase_subscribe_to_topic_url'       => env('FIREBASE_SUBSCRIBE_TO_TOPIC_URL', 'https://iid.googleapis.com/iid/v1:batchAdd'),
    'firebase_unsubscribe_from_topic_url'   => env('FIREBASE_UNSUBSCRIBE_FROM_TOPIC_URL', 'https://iid.googleapis.com/iid/v1:batchRemove'),
    'firebase_token_info_url'               => env('FIREBASE_TOKEN_INFO_URL', 'https://iid.googleapis.com/iid/info/'),

    'sendgrid'                              => [
        'url'                               => env('SENDGRID_URL', 'https://api.sendgrid.com/v3/mail/send'),
        'api_key'                           => env('SENDGRID_API_KEY'),
        'from_email'                        => env('MAIL_FROM_ADDRESS', 'nsxezkcvwkuqqafeie@ytnhy.com'),
        'from_name'                         => env('MAIL_FROM_NAME', 'Example App'),
        'template_id'                       => env('SENDGRID_TEMPLATE_ID', 'd-1b2b3b4b5b6b7b8b9b0b1b2b3b4b5b6'),
    ],
];
