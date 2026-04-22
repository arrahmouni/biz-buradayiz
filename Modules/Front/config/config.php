<?php

return [
    'name' => 'Front',

    /*
    |--------------------------------------------------------------------------
    | Public site hero background (default when setting has no upload)
    |--------------------------------------------------------------------------
    */
    'default_hero_background_url' => asset('images/default/hero_background.jpeg'),

    /*
    |--------------------------------------------------------------------------
    | Front route → SEO static page key (seo_static_pages.key)
    |--------------------------------------------------------------------------
    |
    | Dynamic CMS routes (e.g. blog post, static page by slug) use Content + seo_entries
    | and are resolved in the layout meta composer from view data.
    |
    */
    'meta' => [
        'route_to_static_key' => [
            'front.index' => 'home',
            'front.contact.show' => 'contact',
            'front.blog.index' => 'blog',
            'front.page.faq' => 'faq',
            'front.search' => 'provider_search',
            'front.provider.login' => 'login',
            'front.provider.register' => 'provider_register',
            'front.provider.register.form' => 'provider_register_apply',
            'front.provider.password.request' => 'provider_forgot_password',
            'front.provider.password.reset' => 'provider_reset_password',
            'front.provider.dashboard' => 'provider_dashboard',
        ],
    ],
];
