<?php

return [
    'notifications'                                             => 'Bildirimler',
    'mark_all_as_read'                                          => 'Tumunu okundu olarak isaretle',
    'notification_templates'                                    => [
        'welcome_in_our_platform'                               => [
            'title'                                             => 'Platformumuza hoş geldiniz',
            'description'                                       => 'Bu kullanici icin bir hos geldiniz mesajidir',
            'short_template'                                    => '{{username}} platformumuza hos geldiniz',
            'long_template'                                     => '{{username}} platformumuza hos geldiniz',
        ],
        'forget_password'                                       => [
            'title'                                             => 'Şifre sıfırlama',
            'description'                                       => 'Şifre sıfırlama bağlantısı kullanıcının e-postasına gönderilir',
            'short_template'                                    => 'Şifrenizi sıfırlayın',
            'long_template'                                     => '
                <div style="direction: ltr; text-align: left; font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif;">
                    <h3 style="color: #2d3748; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #667eea; font-size: 20px;">
                        🔐 Şifrenizi sıfırlayın
                    </h3>

                    <p style="color: #4a5568; margin-bottom: 20px; line-height: 1.6;">
                        <strong>Merhaba {{username}},</strong><br>
                        Hesabınız için şifre sıfırlama talebi aldık. Yeni bir şifre belirlemek için lütfen aşağıdaki düğmeye tıklayın.
                    </p>

                    <div style="text-align: center; margin: 25px 0;">
                        <a href="{{reset_link}}" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: 600; font-size: 15px; margin: 5px;">
                            🔑 Şifreyi sıfırla
                        </a>
                    </div>

                    <div style="background: #f8fafc; padding: 20px; border-radius: 10px; border-left: 4px solid #667eea; margin-bottom: 25px;">
                        <p style="margin: 0 0 10px 0; color: #4a5568; font-size: 14px; line-height: 1.6;">
                            ⚠️ Bu talebi siz oluşturmadıysanız bu mesajı yok sayabilirsiniz; hesabınızda değişiklik yapılmaz.
                        </p>
                        <p style="margin: 0; color: #718096; font-size: 14px;">
                            ⏱️ Not: Bu bağlantının süresi bu e-postanın gönderilmesinden itibaren 60 dakika sonra dolar.
                        </p>
                    </div>
                </div>
            ',
        ],
        'priority'                                              => [
            'low'                                               => 'Düşük',
            'medium'                                            => 'Orta',
            'high'                                              => 'Yüksek',
            'default'                                           => 'Varsayılan',
        ],
    ],
    'statuses'                                                  => [
        'delivered'                                             => 'Teslim Edildi',
        'pending'                                               => 'Beklemede',
        'seen'                                                  => 'Goruldu',
        'failed'                                                => 'Basarisiz',
        'read'                                                  => 'Okundu',
    ],
    'added_by'                                                  => [
        'system'                                                => 'Sistem',
        'admin'                                                 => 'Yonetici',
    ],
];
