<?php

namespace Modules\Platform\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'Platform';

    protected string $moduleNamespace = 'Modules\Platform\Http\Controllers';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     */
    public function boot(): void
    {
        parent::boot();
        Route::pattern('model', '[0-9]+');

        Route::pattern('id', '[0-9]+');
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
            ->prefix(LaravelLocalization::setLocale().'/admin')
            ->name('platform.')
            ->group(module_path('Platform', '/routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApiRoutes(): void
    {
        Route::prefix('api/'.config('app.api_version'))
            ->middleware('api')
            ->namespace($this->moduleNamespace)
            ->group(module_path('Platform', '/routes/api.php'));
    }
}
