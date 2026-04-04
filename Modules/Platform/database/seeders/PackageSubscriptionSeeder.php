<?php

namespace Modules\Platform\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Platform\Models\PackageSubscription;

class PackageSubscriptionSeeder extends Seeder
{
    /**
     * Seeds demo subscriptions with snapshots. Uses existing users when present;
     * otherwise {@see PackageSubscriptionFactory} creates users (requires services/cities if your User factory needs them).
     */
    public function run(): void
    {
        PackageSubscription::factory()
            ->count(20)
            ->create();
    }
}
