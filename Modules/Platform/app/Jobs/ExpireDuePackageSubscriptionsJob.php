<?php

namespace Modules\Platform\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Platform\Enums\PackageSubscriptionStatus;
use Modules\Platform\Models\PackageSubscription;

class ExpireDuePackageSubscriptionsJob
{
    use Dispatchable;

    public function handle(): void
    {
        PackageSubscription::query()
            ->where('status', PackageSubscriptionStatus::Active)
            ->whereNotNull('ends_at')
            ->whereNull('cancelled_at')
            ->where('ends_at', '<=', now())
            ->update(['status' => PackageSubscriptionStatus::Expired]);
    }
}
