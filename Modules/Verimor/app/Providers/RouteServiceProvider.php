<?php

namespace Modules\Verimor\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'Verimor';

    protected string $moduleNamespace = 'Modules\Verimor\Http\Controllers';

    public function boot(): void
    {
        parent::boot();
    }

    public function map(): void
    {
        $this->mapApiRoutes();
    }

    protected function mapApiRoutes(): void
    {
        Route::prefix('api/'.config('app.api_version'))
            ->middleware('api')
            ->namespace($this->moduleNamespace)
            ->group(module_path($this->name, '/routes/api.php'));
    }
}
