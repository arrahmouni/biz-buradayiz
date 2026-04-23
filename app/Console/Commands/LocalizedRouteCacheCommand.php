<?php

namespace App\Console\Commands;

use Mcamara\LaravelLocalization\Commands\RouteTranslationsCacheCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'route:cache', description: 'Create localized route cache files for all supported locales')]
class LocalizedRouteCacheCommand extends RouteTranslationsCacheCommand
{
    /**
     * Use the standard Artisan name so deploy scripts and `optimize` keep working.
     * Implementation is Mcamara's per-locale caching (see RouteTranslationsCacheCommand).
     */
    protected $name = 'route:cache';

    protected $description = 'Create localized route cache files for faster route registration';
}
