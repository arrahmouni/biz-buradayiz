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
            'long_template' => '
                <div style="font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif;">
                    <!-- Başlık ve kırmızı vurgu çizgisi -->
                    <div style="margin-bottom: 28px;">
                        <h2 style="color: #1F2937; font-size: 22px; font-weight: 700; margin: 0 0 10px 0;">Şifrenizi Sıfırlayın</h2>
                        <div style="width: 50px; height: 3px; background-color: #DC2626; border-radius: 2px;"></div>
                    </div>

                    <!-- Selamlama -->
                    <p style="color: #374151; font-size: 15px; line-height: 1.5; margin: 0 0 16px 0;">
                        <strong>Merhaba {{username}},</strong>
                    </p>
                    <p style="color: #4B5563; font-size: 15px; line-height: 1.5; margin: 0 0 28px 0;">
                        Hesabınız için bir şifre sıfırlama talebi aldık. Yeni bir şifre oluşturmak için lütfen aşağıdaki butona tıklayın.
                    </p>

                    <!-- Şifre sıfırlama butonu -->
                    <div style="text-align: center; margin: 32px 0;">
                        <a href="{{reset_link}}" style="display: inline-block; background-color: #DC2626; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-weight: 600; font-size: 14px; letter-spacing: 0.3px;">Şifrenizi Sıfırlayın</a>
                    </div>

                    <!-- Bilgi kutusu -->
                    <div style="background-color: #F8FAFC; padding: 20px 24px; border-radius: 8px; border-left: 3px solid #DC2626; margin: 32px 0 16px;">
                        <p style="margin: 0 0 12px 0; color: #4B5563; font-size: 14px; line-height: 1.5;">
                            <strong>Bunu siz talep etmediyseniz?</strong> Bu e-postayı güvenle görmezden gelebilirsiniz. Hesabınızda herhangi bir değişiklik yapılmayacaktır.
                        </p>
                        <p style="margin: 0; color: #6B7280; font-size: 13px; line-height: 1.5;">
                            <strong>Bağlantı süresi:</strong> Bu şifre sıfırlama bağlantısı, e-posta gönderildikten 60 dakika sonra geçerliliğini yitirecektir.
                        </p>
                    </div>

                    <!-- Düz URL yedek metni -->
                    <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #E5E7EB;">
                        <p style="color: #9CA3AF; font-size: 12px; line-height: 1.5; margin: 0 0 8px 0;">
                            Yukarıdaki buton çalışmazsa, aşağıdaki bağlantıyı tarayıcınıza kopyalayıp yapıştırın:
                        </p>
                        <p style="margin: 0;">
                            <a href="{{reset_link}}" style="color: #DC2626; font-size: 12px; word-break: break-all; text-decoration: underline;">{{reset_link}}</a>
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
