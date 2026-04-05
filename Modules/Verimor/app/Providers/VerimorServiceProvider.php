<?php

namespace Modules\Verimor\Providers;

use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;

class VerimorServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Verimor';

    protected string $nameLower = 'verimor';

    public function boot(): void
    {
        $this->registerConfig();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(module_path($this->name, 'config/config.php'), 'verimor');
    }
}
