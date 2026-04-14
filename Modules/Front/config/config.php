<?php

return [
    'name' => 'Front',

    /*
    |--------------------------------------------------------------------------
    | Public site hero background (default when setting has no upload)
    |--------------------------------------------------------------------------
    */
    'default_hero_background_url' => 'https://images.unsplash.com/photo-1645445522156-9ac06bc7a767?q=80&w=1740&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',

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
