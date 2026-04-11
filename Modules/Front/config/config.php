<?php

return [
    'name' => 'Front',

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
        ],
    ],
];
