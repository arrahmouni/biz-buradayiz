<?php

namespace Modules\Platform\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Platform\Models\PackageSubscription;

class PackageSubscriptionSeeder extends Seeder
{
    /**
     * Non-active subscriptions for admin/demo variety. Listed providers already get an active subscription from
     * {@see ServiceProviderListingSeeder}.
     */
    public function run(): void
    {
        PackageSubscription::factory()->count(8)->pendingPaymentSubscription()->create();
        PackageSubscription::factory()->count(7)->cancelledSubscription()->create();
        PackageSubscription::factory()->count(5)->expiredSubscription()->create();
    }
}
