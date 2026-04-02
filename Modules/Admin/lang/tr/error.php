<?php

return [

    'footer'    => '<a href="\'e geri dön' . route('admin.dashboard.index') . '">dashboard</a>.',

    '404_page'      => [
        'title'     => 'Sayfa Bulunamadı !!',
        'header'    => '404 - Bulunamadı',
        'message'   => 'Aradığınız sayfa yapım aşamasında olabilir veya mevcut değil.',
    ],

    '403_page'      => [
        'title'     => 'Yasak Sayfa !!',
        'header'    => '403 - Yasak',
        'message'   => 'Bu sayfaya erişmenize izin verilmiyor.',
    ],

    '405_page'      => [
        'title'     => 'Yönteme İzin Verilmiyor !!',
        'header'    => '405 - Yönteme İzin Verilmiyor',
        'message'   => 'Erişmeye çalıştığınız yönteme izin verilmiyor.',
    ],

    '500_page'      => [
        'title'     => 'Sunucu Hatası!!',
        'header'    => '500 - Dahili Sunucu Hatası',
        'message'   => 'Hata! Bizim açımızdan bir şeyler ters gitti. Bu sorunu mümkün olan en kısa sürede düzeltmek için çalışıyoruz.',
    ],

    '503_page'      => [
        'title'     => 'Hizmet kullanılamıyor !!',
        'header'    => '503 - Hizmet Kullanılamıyor',
        'message'   => 'Üzgünüz, şu anda bakımdayız. Lütfen daha sonra tekrar kontrol edin.',
    ],
];
