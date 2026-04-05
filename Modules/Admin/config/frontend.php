<?php

use Nwidart\Modules\Facades\Module;

return [

    $currentLocalKey = app()->getLocale() == 'tr' ? 'tr' : 'us',

    'error_pages'                       => [
        '404'                           => Module::asset('admin:metronic/demo/media/illustrations/sigma-1/18.png'),
        '405'                           => Module::asset('admin:metronic/demo/media/illustrations/sigma-1/20.png'),
        '500'                           => Module::asset('admin:metronic/demo/media/illustrations/sigma-1/9.png'),
        '503'                           => Module::asset('admin:metronic/demo/media/illustrations/sigma-1/5.png'),
    ],

    'auth_pages'                        => [
        'login_aside_menu_background'   => Module::asset('admin:metronic/demo/media/illustrations/sketchy-1/13.png'),
    ],

    'country_flag'                      => [
        'current_local'                 => Module::asset('admin:metronic/demo/media/flags/'. $currentLocalKey .'.svg'),
        'ar'                            => Module::asset('admin:metronic/demo/media/flags/sa.svg'),
        'en'                            => Module::asset('admin:metronic/demo/media/flags/us.svg'),
        'tr'                            => Module::asset('admin:metronic/demo/media/flags/tr.svg'),
    ],

    'default_placeholder'               => getSetting('app_placeholder', asset('images/default/placeholder/global.png')),
];
