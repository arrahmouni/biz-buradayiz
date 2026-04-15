<?php

namespace Modules\Config\Enums;

final class SettingGroups
{
    public const GENERAL = 'general';

    public const SOCIAL_MEDIA = 'social_media';

    public const CONTACT_INFO = 'contact_info';

    public const PLATFORM = 'platform';

    public const MEDIA = 'media';

    public const MOBILE = 'mobile';

    public const DEVELOPERS = 'developers';

    public const PROVIDER_RANKING = 'provider_ranking';

    public static function all(): array
    {
        return [
            self::GENERAL,
            self::SOCIAL_MEDIA,
            self::CONTACT_INFO,
            self::PLATFORM,
            self::MEDIA,
            self::MOBILE,
            self::DEVELOPERS,
            self::PROVIDER_RANKING,
        ];
    }

    public static function getGroups(): array
    {
        return [
            self::GENERAL => trans('config::settings.groups.general.title'),
            self::SOCIAL_MEDIA => trans('config::settings.groups.social_media.title'),
            self::CONTACT_INFO => trans('config::settings.groups.contact_info.title'),
            self::PLATFORM => trans('config::settings.groups.platform.title'),
            self::MEDIA => trans('config::settings.groups.media.title'),
            self::MOBILE => trans('config::settings.groups.mobile.title'),
            self::DEVELOPERS => trans('config::settings.groups.developers.title'),
            self::PROVIDER_RANKING => trans('config::settings.groups.provider_ranking.title'),
        ];
    }
}
