<?php

namespace Modules\Base\Console;

use Illuminate\Console\Command;

class ReleaseInitCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'release:init';

    /**
     * The console command description.
     */
    protected $description = 'Run production release initialization (seeders required after deploy).';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Running release initialization seeders...');

        $seeders = $this->getReleaseSeeders();

        foreach ($seeders as $seederClass) {
            if (! class_exists($seederClass)) {
                $this->warn("Seeder class does not exist: {$seederClass}");
                continue;
            }

            $this->info("Running seeder: {$seederClass}");
            $this->call('db:seed', [
                '--class' => $seederClass,
                '--force' => true,
            ]);
        }

        $this->info('Release initialization completed.');

        return self::SUCCESS;
    }

    /**
     * Get the list of seeders to run on production release.
     */
    protected function getReleaseSeeders(): array
    {
        $defaults = [
            \Modules\Config\database\seeders\ConfigDatabaseSeeder::class,
        ];

        return array_merge(
            $defaults,
            config('base.release_seeders', [])
        );
    }
}
