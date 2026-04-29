<?php

namespace Modules\Front\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Modules\Cms\Enums\contents\BaseContentTypes;
use Modules\Cms\Models\Content;
use Modules\Front\Http\View\Composers\FrontLayoutMetaComposer;
use Modules\Front\Support\FrontPublicServices;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FrontServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Front';

    protected string $nameLower = 'front';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
            return route('front.provider.password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], absolute: true);
        });

        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));

        View::composer('front::*', function ($view) {
            $name = $view->name();
            if (is_string($name) && (str_starts_with($name, 'front::errors.') || $name === 'front::coming-soon')) {
                return;
            }
            $request = request();
            $attrKey = '_front_default_composer_data';
            if (! $request->attributes->has($attrKey)) {
                $request->attributes->set($attrKey, [
                    'frontPublicServices' => FrontPublicServices::all(),
                    'frontPublicFilterServices' => FrontPublicServices::forSearchFilters(),
                    'frontSearchDefaultCountryId' => resolveFrontSearchDefaultCountryIdFromIp(),
                ]);
            }
            $view->with($request->attributes->get($attrKey));
        });

        View::composer('front::layouts.master', FrontLayoutMetaComposer::class);
        View::composer('front::layouts.auth', FrontLayoutMetaComposer::class);

        View::composer('front::includes.footer', function ($view) {
            cache()->remember('footer_pages_'.app()->getLocale(), 3600, function () use ($view) {
                $footerPages = Content::query()
                    ->byType(BaseContentTypes::PAGES)
                    ->orderBy('id')
                    ->get()
                    ->filter(fn (Content $page) => $page->appear_in_footer)
                    ->map(function (Content $page) {
                        $slug = $page->publicPageSlug();
                        if ($slug === null) {
                            return null;
                        }

                        return [
                            'title' => $page->smartTrans('title'),
                            'url' => route('front.page.show', ['slug' => $slug]),
                        ];
                    })
                    ->filter()
                    ->values();

                $view->with('footerPages', $footerPages);
            });
        });
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/'.$this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $relativeConfigPath = config('modules.paths.generator.config.path');
        $configPath = module_path($this->name, $relativeConfigPath);

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $relativePath = str_replace($configPath.DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $configKey = $this->nameLower.'.'.str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $relativePath);
                    $key = ($relativePath === 'config.php') ? $this->nameLower : $configKey;

                    $this->publishes([$file->getPathname() => config_path($relativePath)], 'config');
                    $this->mergeConfigFrom($file->getPathname(), $key);
                }
            }
        }
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        Blade::anonymousComponentPath(
            module_path($this->name, 'resources/views/components'),
            $this->nameLower
        );

        $componentNamespace = $this->module_namespace($this->name, $this->app_path(config('modules.paths.generator.component-class.path')));
        Blade::componentNamespace($componentNamespace, $this->nameLower);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->nameLower)) {
                $paths[] = $path.'/modules/'.$this->nameLower;
            }
        }

        return $paths;
    }
}
