<?php

namespace App\Providers;

use App\Console\Commands\LocalizedRouteCacheCommand;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Console\RouteCacheCommand;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as FrameworkRouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use Mcamara\LaravelLocalization\Traits\LoadsTranslatedCachedRoutes;
use Modules\Auth\Models\PersonalAccessToken;
use Modules\Config\Constatnt;
use Modules\Permission\Models\Ability;
use Modules\Permission\Models\Role;
use Silber\Bouncer\BouncerFacade;

class AppServiceProvider extends ServiceProvider
{
    use LoadsTranslatedCachedRoutes;

    private static bool $localizedCachedRoutesLoaderRegistered = false;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        FrameworkRouteServiceProvider::loadCachedRoutesUsing(function () {
            if (self::$localizedCachedRoutesLoaderRegistered) {
                return;
            }
            self::$localizedCachedRoutesLoaderRegistered = true;

            $this->loadCachedRoutes();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->singleton(RouteCacheCommand::class, function ($app) {
            return new LocalizedRouteCacheCommand($app['files']);
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        BouncerFacade::useRoleModel(Role::class);
        BouncerFacade::useAbilityModel(Ability::class);
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        if (isDev()) {
            $this->registerQueryInfo();
            Model::preventSilentlyDiscardingAttributes();
        }

        $this->createDirectives();

        Blueprint::macro('disableable', function () {
            return $this->timestamp('disabled_at')->nullable();
        });

        Blueprint::macro('dropDisableable', function () {
            return $this->dropColumn('disabled_at');
        });

        // $this->checkDebugBar();

        if (! isLocal()) {
            URL::forceScheme('https');
        }
    }

    private function createDirectives()
    {
        Blade::directive('notEmpty', function ($expression) {
            return "<?php if(!empty($expression)): ?>";
        });

        Blade::directive('endNotEmpty', function () {
            return '<?php endif; ?>';
        });
    }

    private function registerQueryInfo()
    {
        // Lisiting For Query Events that takes longer than 500ms (0.5s)
        DB::whenQueryingForLongerThan(500, function (Connection $connection, QueryExecuted $query) {
            Log::channel('query')->info('Query Sql is : '.$query->sql);
            Log::channel('query')->info('Query Binding is : '.implode(',', $query->bindings));
            Log::channel('query')->info('Query Time is : '.$query->time.' ms');
        });
    }

    private function checkDebugBar()
    {
        $allowDebugForCustomIp = getSetting(Constatnt::ALLOW_DEBUG_FOR_CUSTOM_IP, false);
        $ipAddresses = getSetting(Constatnt::CUSTOM_IPS, '[]');
        $ipAddresses = explode(',', $ipAddresses);
        $currentIp = request()->ip();

        if ($allowDebugForCustomIp && in_array($currentIp, $ipAddresses)) {
            Debugbar::enable();
            config(['app.debug' => true]);
        }
    }
}
