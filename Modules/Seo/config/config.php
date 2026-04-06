<?php

/**
 * SEO module configuration.
 *
 * Static pages listed here are seeded into `seo_static_pages` and are valid morph
 * targets for `seo_entries`. Map `path_hint` to your SPA routes (e.g. Next.js).
 */
return [
    'name' => 'Seo',

    /*
    |--------------------------------------------------------------------------
    | Static SEO pages (registry)
    |--------------------------------------------------------------------------
    |
    | Each item becomes one row in seo_static_pages. `key` must be unique.
    | `path_hint` is the public URL path (without locale prefix) for documentation
    | and optional frontend use; LaravelLocalization still prefixes locales.
    |
    */
    'static_pages' => [
        [
            'key' => 'home',
            'path_hint' => '/',
            'sort_order' => 0,
            'label' => 'seo::static_pages.home',
        ],
        [
            'key' => 'login',
            'path_hint' => '/login',
            'sort_order' => 10,
            'label' => 'seo::static_pages.login',
        ],
    ],
];
