<?php

namespace Modules\Config\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class RefreshOptimizationCachesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Run only in CLI workers to keep HTTP requests safe.
        if (! app()->runningInConsole()) {
            return;
        }

        Artisan::call('optimize:clear');

        if (function_exists('putenv')) {
            Artisan::call('optimize');
        }
    }
}
