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
        logger()->info('ExpireDuePackageSubscriptionsJob started');

        $expiredCount = PackageSubscription::query()
            ->where('status', PackageSubscriptionStatus::Active)
            ->whereNull('cancelled_at')
            ->where(function ($query) {
                $query
                    ->where(function ($q) {
                        $q->whereNotNull('ends_at')
                            ->where('ends_at', '<=', now());
                    })
                    ->orWhere('remaining_connections', 0);
            })
            ->update(['status' => PackageSubscriptionStatus::Expired]);

        logger()->info('ExpireDuePackageSubscriptionsJob completed', [
            'subscriptions' => $expiredCount,
        ]);
    }
}
