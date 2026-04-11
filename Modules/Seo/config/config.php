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
            'key' => 'contact',
            'path_hint' => '/contact',
            'sort_order' => 5,
            'label' => 'seo::static_pages.contact',
        ],
        [
            'key' => 'blog',
            'path_hint' => '/blog',
            'sort_order' => 6,
            'label' => 'seo::static_pages.blog',
        ],
        [
            'key' => 'faq',
            'path_hint' => '/page/faq',
            'sort_order' => 7,
            'label' => 'seo::static_pages.faq',
        ],
        [
            'key' => 'provider_search',
            'path_hint' => '/search',
            'sort_order' => 8,
            'label' => 'seo::static_pages.provider_search',
        ],
        [
            'key' => 'login',
            'path_hint' => '/login',
            'sort_order' => 10,
            'label' => 'seo::static_pages.login',
        ],
    ],
];
