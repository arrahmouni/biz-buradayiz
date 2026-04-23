<?php

return [
    'name'                      => 'Admin',
    'style_version'             => isDev() ? rand(1, 1000) : '1.1.0',
    'defalut_locale_key'        => 'tr',
    'avatar'                    => [
        'default'               => asset('images/default/avatars/blank.svg'),
    ],
    'main_roles'                => [
        'users'                 => 'users',
        'admins'                => 'admins',
    ],
    'seeders'                   => [
        'root_email'            => env('ROOT_ADMIN_EMAIL'),
        'root_password'         => env('ROOT_ADMIN_PASSWORD'),
        'system_admin_email'    => env('SYSTEM_ADMIN_EMAIL'),
        'system_admin_password' => env('SYSTEM_ADMIN_PASSWORD'),
    ],
];
