<?php

return [

    'cta_home' => 'Ana sayfaya dön',

    'footer' => 'Yardıma mı ihtiyacınız var? <a class="font-semibold text-red-600 underline decoration-red-200 underline-offset-4 transition hover:text-red-700 hover:decoration-red-400" href="'.route('front.contact.show').'">Bize ulaşın</a>.',

    '404_page' => [
        'title' => 'Sayfa Bulunamadı !!',
        'header' => '404 - Bulunamadı',
        'message' => 'Aradığınız sayfa yapım aşamasında olabilir veya mevcut değil.',
    ],

    '403_page' => [
        'title' => 'Yasak Sayfa !!',
        'header' => '403 - Yasak',
        'message' => 'Bu sayfaya erişmenize izin verilmiyor.',
    ],

    '401_page' => [
        'title' => 'Yetkisiz !!',
        'header' => '401 - Yetkisiz',
        'message' => 'Bu sayfaya erişmek için oturum açmanız gerekir.',
    ],

    '419_page' => [
        'title' => 'Sayfanın Süresi Doldu !!',
        'header' => '419 - Sayfanın Süresi Doldu',
        'message' => 'Oturumunuzun süresi doldu. Lütfen sayfayı yenileyip tekrar deneyin.',
    ],

    '429_page' => [
        'title' => 'Çok Fazla İstek !!',
        'header' => '429 - Çok Fazla İstek',
        'message' => 'Kısa sürede çok fazla istek gönderdiniz. Lütfen bir süre bekleyip tekrar deneyin.',
    ],

    '405_page' => [
        'title' => 'Yönteme İzin Verilmiyor !!',
        'header' => '405 - Yönteme İzin Verilmiyor',
        'message' => 'Erişmeye çalıştığınız yönteme izin verilmiyor.',
    ],

    '500_page' => [
        'title' => 'Sunucu Hatası!!',
        'header' => '500 - Dahili Sunucu Hatası',
        'message' => 'Hata! Bizim açımızdan bir şeyler ters gitti. Bu sorunu mümkün olan en kısa sürede düzeltmek için çalışıyoruz.',
    ],

    '503_page' => [
        'title' => 'Hizmet kullanılamıyor !!',
        'header' => '503 - Hizmet Kullanılamıyor',
        'message' => 'Üzgünüz, şu anda bakımdayız. Lütfen daha sonra tekrar kontrol edin.',
    ],

    'coming_soon_page' => [
        'title' => 'Çok Yakında !!',
        'badge' => 'Yakında',
        'header' => 'Çok yakında yayında',
        'message' => 'Yeni deneyimimiz üzerinde son rötuşları yapıyoruz. Kısa süre içinde tekrar uğrayın.',
    ],
];
