<?php

return [
    'groups' => [
        'general' => [
            'title' => 'العامة',
            'fields' => [
                'app_name' => 'اسم التطبيق',
                'app_default_language' => 'لغة التطبيق الافتراضية',
                'vat_rate' => 'نسبة ضريبة القيمة المضافة (%)',
                'maintenance_mode' => 'وضع الصيانة',
                'coming_soon_mode' => 'صفحة قريبًا (الموقع العام)',
                'website_launch_date' => 'تاريخ إطلاق الموقع',
            ],
        ],
        'social_media' => [
            'title' => 'وسائل التواصل الاجتماعي',
            'fields' => [
                'facebook' => 'فيسبوك',
                'twitter' => 'تويتر',
                'instagram' => 'انستجرام',
                'linkedin' => 'لينكد إن',
                'youtube' => 'يوتيوب',
                'tiktok' => 'تيك توك',
            ],
        ],
        'contact_info' => [
            'title' => 'معلومات الاتصال',
            'fields' => [
                'phone' => 'الهاتف',
                'email' => 'البريد الإلكتروني',
                'address' => 'العنوان',
                'contact_map_embed_url' => 'رابط تضمين خريطة صفحة الاتصال (مصدر iframe لخرائط Google)',
            ],
        ],
        'platform' => [
            'title' => 'المنصة',
            'fields' => [
                'emergency_contact_number' => 'رقم اتصال الطوارئ (الموقع العام)',
                'front_search_default_country_id' => 'معرّف البلد الافتراضي (بحث الموقع في الموقع العام)',
                'provider_register_landing_youtube_url' => 'صفحة تسجيل مقدم الخدمة — رابط فيديو يوتيوب (مشاهدة أو youtu.be أو رابط تضمين)',
                'provider_subscription_whatsapp_e164' => 'رقم واتساب لإيصالات دفع مقدمي الخدمة (E.164، مثل +905551234567)',
                'provider_bank_transfer_instructions' => 'تعليمات التحويل البنكي المعروضة في لوحة مقدم الخدمة',
            ],
        ],
        'media' => [
            'title' => 'وسائط',
            'fields' => [
                'app_logo' => 'شعار التطبيق',
                'app_mobile_logo' => 'شعار التطبيق للهاتف المحمول',
                'email_logo' => 'شعار البريد الإلكتروني',
                'app_favicon' => 'الصورة المصغرة للموقع',
                'app_placeholder' => 'الصورة الافتراضية',
                'front_hero_background' => 'خلفية البطل للموقع العام',
            ],
        ],
        'mobile' => [
            'title' => 'تطبيقات الجوال',
            'fields' => [
                'app_store' => 'رابط App Store (iOS)',
                'google_play' => 'رابط Google Play (Android)',
            ],
        ],
        'developers' => [
            'title' => 'المطورين',
            'fields' => [
                'clear_cache' => 'مسح الذاكرة المؤقتة',
                'clear_logs' => 'مسح السجلات',
                'reset_permissions' => 'إعادة تعيين الصلاحيات',
                'session_lifetime' => [
                    'title' => 'مدة الجلسة',
                    'description' => 'مدة الجلسة بالدقائق',
                ],
                'allow_debug_for_custom_ip' => 'تفعيل ال debug لعنوان IP المخصص',
                'custom_ips' => [
                    'title' => 'عناوين IP المخصصة',
                    'description' => 'أدخل عناوين IP مفصولة بفاصلة مثال : xxx.xxx.xxx.xxx, yyy.yyy.yyy.yyy',
                ],
            ],
        ],
        'provider_ranking' => [
            'title' => 'ترتيب مقدمي الخدمة',
            'fields' => [
                'featured_providers_count' => 'عدد مقدمي الخدمة المميزين',
                'new_provider_hours' => 'نافذة مقدم الخدمة الجديد (ساعات)',
                'ranking_weight_rating' => 'وزن التقييم (%)',
                'ranking_weight_activity' => 'وزن النشاط (%)',
                'ranking_weight_experience' => 'وزن الخبرة/الأقدمية (%)',
            ],
        ],
    ],
    'ranking_weights_sum_exceeded' => 'مجموع أوزان الترتيب (التقييم + النشاط + الخبرة) يجب ألا يتجاوز 100.',
    'validation' => [
        'website_launch_date_required_when_coming_soon' => 'تاريخ إطلاق الموقع مطلوب عند تفعيل صفحة «قريبًا».',
    ],
];
