<?php

namespace Modules\Platform\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Models\User;
use Modules\Platform\Jobs\RecalculateProviderRankingsJob;
use Modules\Platform\Models\PackageSubscription;
use Modules\Platform\Models\Review;

class ServiceProviderListingSeeder extends Seeder
{
    private const LISTED_PROVIDER_COUNT = 150;

    private const ACTIVE_SUBSCRIPTION_RATIO = 0.92;

    private const APPROVED_REVIEWS_PROBABILITY = 0.88;

    public function run(): void
    {
        $providers = User::factory()
            ->activeServiceProvider()
            ->count(self::LISTED_PROVIDER_COUNT)
            ->create();

        $withSubCount = (int) round($providers->count() * self::ACTIVE_SUBSCRIPTION_RATIO);
        $withSubCount = max(1, min($withSubCount, $providers->count()));

        $withSubscription = $providers->shuffle()->take($withSubCount);

        foreach ($withSubscription as $user) {
            PackageSubscription::factory()
                ->for($user)
                ->activePaidSubscription()
                ->create();
        }

        Review::withoutEvents(function () use ($withSubscription): void {
            foreach ($withSubscription as $user) {
                if (! fake()->boolean((int) (self::APPROVED_REVIEWS_PROBABILITY * 100))) {
                    continue;
                }

                $reviewCount = fake()->numberBetween(1, 5);
                for ($i = 0; $i < $reviewCount; $i++) {
                    Review::factory()
                        ->forUser($user)
                        ->approved()
                        ->create([
                            'rating' => fake()->numberBetween(3, 5),
                        ]);
                }
            }
        });

        foreach ($withSubscription as $user) {
            sync_service_provider_rating((int) $user->id);
        }

        RecalculateProviderRankingsJob::dispatchSync();
    }
}
