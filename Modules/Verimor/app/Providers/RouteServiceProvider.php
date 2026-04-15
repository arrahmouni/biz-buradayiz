<?php

namespace Modules\Verimor\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Mcamara\LaravelLocalization\Traits\LoadsTranslatedCachedRoutes;

class RouteServiceProvider extends ServiceProvider
{
    use LoadsTranslatedCachedRoutes;

    protected string $name = 'Verimor';

    protected string $moduleNamespace = 'Modules\Verimor\Http\Controllers';

    public function boot(): void
    {
        parent::boot();

        Route::pattern('model', '[0-9]+');
        Route::pattern('id', '[0-9]+');
    }

    public function map(): void
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    protected function mapWebRoutes(): void
    {
        Route::middleware(['web', 'locale'])
            ->namespace($this->moduleNamespace.'\Admin')
            ->prefix(LaravelLocalization::setLocale().'/admin')
            ->name('verimor.')
            ->group(module_path($this->name, '/routes/web.php'));
    }

    protected function mapApiRoutes(): void
    {
        Route::prefix('api/'.config('app.api_version'))
            ->middleware('api')
            ->namespace($this->moduleNamespace)
            ->group(module_path($this->name, '/routes/api.php'));
    }
}
