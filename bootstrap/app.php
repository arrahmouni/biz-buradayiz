<?php

use App\Exceptions\ExceptionHandler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath;
use Modules\Admin\Http\Middleware\IsActiveAdmin;
use Modules\Admin\Http\Middleware\SystemUserInfo;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Http\Middleware\ActiveUser;
use Modules\Base\Http\Middleware\CheckComingSoonMode;
use Modules\Base\Http\Middleware\CheckMaintenanceMode;
use Modules\Base\Http\Middleware\OnlyDevEnvMiddleware;
use Modules\Front\Http\Middleware\EnsureUserIsServiceProvider;
use Modules\Permission\Http\Middleware\NeedPermissions;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->segment(2) === 'provider') {
                return route('front.provider.login');
            }

            return route('admin.auth.login');
        });

        $middleware->redirectUsersTo(function (Request $request) {
            if ($request->user('admin')) {
                return route('admin.dashboard.index');
            }

            $user = $request->user();
            if ($user && $user->type === UserType::ServiceProvider) {
                return route('front.provider.dashboard');
            }

            return route('front.index');
        });

        // Custom Global Middleware (example)
        $middleware->append([
            CheckMaintenanceMode::class,
            CheckComingSoonMode::class,
        ]);

        // Append Web Middleware
        $middleware->web([
            SystemUserInfo::class,
        ]);

        // Append API Middleware
        $middleware->throttleApi();

        // Create locale group
        $middleware->group('locale', [
            LaravelLocalizationRedirectFilter::class,
            LaravelLocalizationViewPath::class,
        ]);

        // Aliases Middleware
        $middleware->alias([
            'need.permissions' => NeedPermissions::class,
            'active.admin' => IsActiveAdmin::class,
            'active.user' => ActiveUser::class,
            'only.dev.env' => OnlyDevEnvMiddleware::class,
            'service.provider' => EnsureUserIsServiceProvider::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $exception, Request $request) {
            logger()->error('Error occurred: '.$exception->getMessage().' in '.$exception->getFile().' on line '.$exception->getLine());

            return (new ExceptionHandler)->handleException($exception, $request);
        });
    })->create();
