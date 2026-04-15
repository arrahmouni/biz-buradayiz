<?php

namespace Modules\Platform\Concerns;

trait ProviderDashboardTailwindPillBadge
{
    private function providerDashboardPillBadgeBase(): string
    {
        return 'inline-flex items-center font-medium text-xs px-2 py-1 rounded-full';
    }
}
