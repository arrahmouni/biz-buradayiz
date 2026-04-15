<?php

namespace Modules\Front\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Mcamara\LaravelLocalization\Traits\LoadsTranslatedCachedRoutes;

class RouteServiceProvider extends ServiceProvider
{
    use LoadsTranslatedCachedRoutes;

    protected string $name = 'Front';

    protected string $moduleNamespace = 'Modules\Front\Http\Controllers';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware(['web', 'locale'])
        ->namespace($this->moduleNamespace)
        ->prefix(LaravelLocalization::setLocale())
        ->name('front.')
        ->group(module_path($this->name, '/routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApiRoutes(): void
    {
        Route::prefix('api/' . config('app.api_version'))
        ->middleware('api')
        ->namespace($this->moduleNamespace)
        ->group(module_path($this->name, '/routes/api.php'));
    }
}
