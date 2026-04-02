<?php

use Illuminate\Http\Request;
use App\Exceptions\ExceptionHandler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(fn(Request $request) => route('admin.auth.login') );
        $middleware->redirectUsersTo(fn(Request $request) => route('admin.dashboard.index') );

        // Custom Global Middleware (example)
        $middleware->append([
            \Modules\Base\Http\Middleware\CheckMaintenanceMode::class,
        ]);

        // Append Web Middleware
        $middleware->web([
            \Modules\Admin\Http\Middleware\SystemUserInfo::class,
        ]);

        // Append API Middleware
        $middleware->throttleApi();

        // Create locale group
        $middleware->group('locale', [
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect::class,
        ]);

        // Aliases Middleware
        $middleware->alias([
            'need.permissions'  => \Modules\Permission\Http\Middleware\NeedPermissions::class,
            'active.admin'      => \Modules\Admin\Http\Middleware\IsActiveAdmin::class,
            'active.user'       => \Modules\Auth\Http\Middleware\ActiveUser::class,
            'only.dev.env'      => \Modules\Base\Http\Middleware\OnlyDevEnvMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $exception, Request $request) {
            logger()->error('Error occurred: '.$exception->getMessage() . ' in ' . $exception->getFile() . ' on line ' . $exception->getLine());

            return (new ExceptionHandler)($exception);
        });
    })->create();
