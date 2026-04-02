<?php

namespace Modules\Auth\Enums;

enum UserType: string
{
    case ServiceProvider = 'service-provider';
    case Customer        = 'customer';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
