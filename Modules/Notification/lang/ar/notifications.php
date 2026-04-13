<?php

return [
    'notifications'                                             => 'الإشعارات',
    'mark_all_as_read'                                          => 'وضع علامة على الكل كمقروء',
    'notification_templates'                                    => [
        'welcome_in_our_platform'                               => [
            'title'                                             => 'مرحبا بك في منصتنا',
            'description'                                       => 'هذه رسالة ترحيب للمستخدم',
            'short_template'                                    => 'مرحبا {{username}} في منصتنا',
            'long_template'                                     => 'مرحبا {{username}} في منصتنا',
        ],
        'forget_password'                                       => [
            'title'                                             => 'إعادة تعيين كلمة المرور',
            'description'                                       => 'رابط إعادة تعيين كلمة المرور يُرسل إلى بريد المستخدم',
            'short_template'                                    => 'إعادة تعيين كلمة المرور',
            'long_template'                                     => '
                <div style="direction: rtl; text-align: right; font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif;">
                    <h3 style="color: #2d3748; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #667eea; font-size: 20px;">
                        🔐 إعادة تعيين كلمة المرور
                    </h3>

                    <p style="color: #4a5568; margin-bottom: 20px; line-height: 1.6;">
                        <strong>السيد/ة {{username}}،</strong><br>
                        تلقينا طلباً منك لإعادة تعيين كلمة المرور الخاصة بحسابك. لتعيين كلمة مرور جديدة، يرجى النقر على الزر أدناه.
                    </p>

                    <div style="text-align: center; margin: 25px 0;">
                        <a href="{{reset_link}}" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: 600; font-size: 15px; margin: 5px;">
                            🔑 إعادة تعيين كلمة المرور
                        </a>
                    </div>

                    <div style="background: #f8fafc; padding: 20px; border-radius: 10px; border-right: 4px solid #667eea; margin-bottom: 25px;">
                        <p style="margin: 0 0 10px 0; color: #4a5568; font-size: 14px; line-height: 1.6;">
                            ⚠️ في حال لم تقدم هذا الطلب، يمكنك تجاهل هذه الرسالة ولن يتم إجراء أي تغيير على حسابك.
                        </p>
                        <p style="margin: 0; color: #718096; font-size: 14px;">
                            ⏱️ تنبيه: صلاحية هذا الرابط تنتهي بعد 60 دقيقة من إرسال الرسالة.
                        </p>
                    </div>
                </div>
            ',
        ],
        'priority'                                              => [
            'low'                                               => 'منخفض',
            'medium'                                            => 'متوسط',
            'high'                                              => 'عالي',
            'default'                                           => 'افتراضي',
        ],
    ],
    'statuses'                                                  => [
        'delivered'                                             => 'تم الإستلام',
        'pending'                                               => 'قيد الإنتظار',
        'seen'                                                  => 'تمت مشاهدته',
        'failed'                                                => 'فشل',
        'read'                                                  => 'تمت القراءة',
    ],
    'added_by'                                                  => [
        'system'                                                => 'النظام',
        'admin'                                                 => 'المشرف',
    ],
];
