<?php

namespace Modules\Front\Support;

final class ProviderDashboardTailwindBadge
{
    private const BASE = 'inline-flex items-center font-medium text-xs px-2 py-1 rounded-full';

    public static function forBool(bool $yes): string
    {
        return self::BASE.' '.($yes
            ? 'bg-green-100 text-green-800'
            : 'bg-gray-100 text-gray-600');
    }
}
