<?php

return [

    'cta_home' => 'Back to home',

    'footer' => 'Need help? <a class="font-semibold text-red-600 underline decoration-red-200 underline-offset-4 transition hover:text-red-700 hover:decoration-red-400" href="'.route('front.contact.show').'">Contact us</a>.',

    '404_page' => [
        'title' => 'Not Found Page !!',
        'header' => '404 - Not Found',
        'message' => 'The page you are looking for might be under construction or does not exist.',
    ],

    '403_page' => [
        'title' => 'Forbidden Page !!',
        'header' => '403 - Forbidden',
        'message' => 'You are not allowed to access this page.',
    ],

    '401_page' => [
        'title' => 'Unauthorized !!',
        'header' => '401 - Unauthorized',
        'message' => 'You must be signed in to access this page.',
    ],

    '419_page' => [
        'title' => 'Page Expired !!',
        'header' => '419 - Page Expired',
        'message' => 'Your session has expired. Please refresh the page and try again.',
    ],

    '429_page' => [
        'title' => 'Too Many Requests !!',
        'header' => '429 - Too Many Requests',
        'message' => 'You have sent too many requests in a short time. Please wait a moment and try again.',
    ],

    '405_page' => [
        'title' => 'Method Not Allowed !!',
        'header' => '405 - Method Not Allowed',
        'message' => 'The method you are trying to access is not allowed.',
    ],

    '500_page' => [
        'title' => 'Server Error !!',
        'header' => '500 - Internal Server Error',
        'message' => 'Oops! Something went wrong on our end. We are working to fix this issue as soon as possible.',
    ],

    '503_page' => [
        'title' => 'Service Unavailable !!',
        'header' => '503 - Service Unavailable',
        'message' => 'Sorry, we are currently under maintenance. Please check back later.',
    ],
];
