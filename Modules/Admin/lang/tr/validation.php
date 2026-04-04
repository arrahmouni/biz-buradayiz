<?php

return [
    'at_least_one_locale' => 'Lütfen verileri en az bir dilde girin',
    'permission_name_end_with_group_code' => 'Yetki adi, ait oldugu grup kodu ile bitmelidir: :group_code',
    'phone_code.regex' => 'Telefon kodu 99 veya +999 veya +999-9999 formatinda olmalidir',
    'iso2.regex' => 'ISO2 kodu XX formatinda olmalidir',
    'iso3.regex' => 'ISO3 kodu XXX formatinda olmalidir',
    'cant_add_fields_without_title' => 'Baslik olmadan icerik ekleyemezsiniz. Lutfen once bir baslik ekleyin.',
    'central_phone_regex' => 'Merkez telefon yalnızca rakamlardan oluşmalıdır; isteğe bağlı olarak başa tek + eklenebilir.',
    'package_must_cover_provider_service' => 'Seçilen paket, sağlayıcının hizmet türünü içermelidir.',
    'package_subscription' => [
        'active_requires_paid' => 'Aktif abonelik için ödeme durumu Ödendi olmalıdır.',
        'paid_cannot_be_cancelled' => 'Ödenmiş bir abonelik İptal durumunda olamaz. Önce ödeme durumunu değiştirin.',
        'starts_at_required_when_active' => 'Abonelik aktif veya ödeme ödendi olduğunda başlangıç tarihi zorunludur.',
        'provider_already_has_active_package' => 'Bu hizmet sağlayıcının zaten aktif bir paketi var (ödendi, süresi dolmamış, kalan bağlantısı var).',
        'paid_cannot_be_pending_payment' => 'Ödeme Ödendi iken abonelik durumu Ödeme bekleniyor olamaz.',
        'paid_cannot_apply_to_cancelled' => 'Abonelik iptal edilmişken ödeme Ödendi olarak işaretlenemez.',
        'paid_requires_active_status' => 'Ödeme Ödendi iken abonelik durumu Aktif olmalıdır.',
    ],
];
