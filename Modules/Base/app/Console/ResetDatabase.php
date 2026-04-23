<?php

namespace Modules\Base\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Nwidart\Modules\Facades\Module;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ResetDatabase extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'reset:database';

    /**
     * The console command description.
     */
    protected $description = 'Reset database fresh and seed';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $confirmed = $this->confirm('Are you sure you want to reset the database?', false);

        if(!$confirmed) return $this->error('Database reset cancelled.');

        $this->alert('Resetting database...');

        $this->call('migrate:fresh');

        $this->info('Cleaning uploaded files...');
        $this->cleanUploadedFiles();
        $this->info('Uploaded files cleaned successfully.');

        $this->call('module:seed', [
            'module' => 'Permission',
        ]);

        $this->call('module:seed', [
            'module' => 'Zms',
        ]);

        $this->call('module:seed', [
            'module' => 'Admin',
        ]);

        $this->call('module:seed', [
            'module' => 'Platform',
        ]);

        $modules = array_keys(array_filter(Module::all(), function ($module) {
            return $module->getName() !== 'Admin' && $module->getName() !== 'Permission' && $module->getName() !== 'Zms' && $module->getName() !== 'Platform';
        }));

        foreach($modules as $module) {
            $this->call('module:seed', [
                'module' => $module,
            ]);
        }

        $this->info('Database reset successfully.');
        $this->alert('Please run "php artisan key:generate" to generate a new key (if needed)');
        $this->alert('Please run "php artisan storage:link" to create symbolic links (if needed)');
        $this->alert('You must execute this command if there is a queue that needs to be started. Please run "php artisan queue:work"');
    }

    /**
     * Remove all user-uploaded files from public/media and storage/app/public.
     */
    protected function cleanUploadedFiles(): void
    {
        $directories = [
            public_path('media'),
            storage_path('app/public'),
        ];

        $preserve = ['.gitignore', '.gitkeep'];

        foreach ($directories as $directory) {
            if (!File::isDirectory($directory)) {
                continue;
            }

            foreach (File::files($directory) as $file) {
                if (!in_array($file->getFilename(), $preserve)) {
                    File::delete($file->getPathname());
                }
            }

            foreach (File::directories($directory) as $subDirectory) {
                File::deleteDirectory($subDirectory);
            }

            $this->info("Cleaned: {$directory}");
        }
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
