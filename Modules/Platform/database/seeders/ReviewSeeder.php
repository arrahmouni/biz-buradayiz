<?php

namespace Modules\Platform\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;
use Modules\Platform\Jobs\RecalculateProviderRankingsJob;
use Modules\Platform\Models\Review;

class ReviewSeeder extends Seeder
{
    /**
     * Pending reviews on existing providers (moderation queue); does not create extra provider accounts.
     */
    public function run(): void
    {
        $providers = User::query()
            ->where('type', UserType::ServiceProvider)
            ->inRandomOrder()
            ->limit(25)
            ->get();

        if ($providers->isEmpty()) {
            return;
        }

        Review::withoutEvents(function () use ($providers): void {
            foreach (range(1, 12) as $_) {
                $user = $providers->random();
                Review::factory()
                    ->forUser($user)
                    ->pending()
                    ->create([
                        'rating' => fake()->numberBetween(1, 5),
                    ]);
            }
        });

        foreach ($providers as $user) {
            sync_service_provider_rating((int) $user->id);
        }

        RecalculateProviderRankingsJob::dispatchSync();
    }
}
