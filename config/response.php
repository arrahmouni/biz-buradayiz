<?php

/*
|--------------------------------------------------------------------------
| Response Helper Configuration
|--------------------------------------------------------------------------
|
| This package provides a fluent response builder + global helpers.
| All keys below are meant to be override-friendly and publishable.
|
*/

return [
    /*
    |--------------------------------------------------------------------------
    | Container Binding
    |--------------------------------------------------------------------------
    |
    | The service container key used to resolve the builder.
    | Default key: `response` (keeps `app('response')` working).
    |
    */
    'binding'           => [
        'key'           => 'response',
    ],

    /*
    |--------------------------------------------------------------------------
    | Request Path Rules
    |--------------------------------------------------------------------------
    |
    | Any request matching this pattern is considered an API request.
    | Used to switch between api/web message keys and output format.
    |
    */
    'paths'             => [
        'api_pattern'   => 'api/*',
    ],

    /*
    |--------------------------------------------------------------------------
    | Views
    |--------------------------------------------------------------------------
    |
    | View mappings used by helper functions for non-ajax web responses.
    |
    */
    'views'             => [
        'errors'        => [
            '404'       => 'admin::errors.404',
            '405'       => 'admin::errors.405',
            '500'       => 'admin::errors.500',
            '503'       => 'admin::errors.503',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | UI Defaults
    |--------------------------------------------------------------------------
    |
    | UI-related defaults for web/ajax responses.
    |
    */
    'notify'            => [
        'default'       => 'toastr',
    ],

    /*
    |--------------------------------------------------------------------------
    | Translations
    |--------------------------------------------------------------------------
    |
    | Controls where response messages are loaded from.
    | Example namespace/file: `response::messages.*`.
    |
    */
    'translations'      => [
        'namespace'     => 'response',
        'file'          => 'messages',
        'keys'          => [
            'types'      => 'response_message_types',
            'web'        => 'web_response_messages',
            'api'        => 'api_response_messages',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Payload Shape
    |--------------------------------------------------------------------------
    |
    | Controls payload key names and API JSON behavior.
    |
    */
    'payload'            => [
        'keys'           => [
            'success'    => 'success',
            'code'       => 'code',
            'message'    => 'message',
            'data'       => 'data',
            'errors'     => 'errors',
        ],
        'api'                         => [
            'message_as_string'       => true,
            'cast_empty_to_object'    => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Debug / Exceptions
    |--------------------------------------------------------------------------
    |
    | Controls if exception details should be exposed in response messages.
    |
    */
    'debug'                         => [
        'expose_exception_details'  => null,
        'dev_environments'          => ['local', 'development', 'staging'],
    ],
];

