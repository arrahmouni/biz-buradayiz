<?php

namespace Modules\Platform\Console;

use Illuminate\Console\Command;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;

class SyncServiceProviderRatingsCommand extends Command
{
    protected $signature = 'platform:sync-service-provider-ratings {--user= : Sync a single user by ID (any user with review aggregates)}';

    protected $description = 'Recalculate review_rating_average and approved_reviews_count from approved reviews via sync_service_provider_rating.';

    public function handle(): int
    {
        $userOption = $this->option('user');

        if ($userOption !== null && $userOption !== '') {
            $userId = (int) $userOption;
            if ($userId < 1) {
                $this->error('Invalid user ID.');

                return self::FAILURE;
            }

            sync_service_provider_rating($userId);
            $this->info("Synced ratings for user #{$userId}.");

            return self::SUCCESS;
        }

        $ids = User::query()
            ->where('type', UserType::ServiceProvider->value)
            ->pluck('id');

        $bar = $this->output->createProgressBar($ids->count());
        $bar->start();

        foreach ($ids as $id) {
            sync_service_provider_rating((int) $id);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Synced ratings for {$ids->count()} service provider(s).");

        return self::SUCCESS;
    }
}
