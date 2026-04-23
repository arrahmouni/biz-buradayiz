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
];
