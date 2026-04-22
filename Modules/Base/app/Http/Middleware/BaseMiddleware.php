<?php

namespace Modules\Base\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BaseMiddleware
{
    /**
     * Whether the request is excluded from public-site gates (e.g. admin area).
     */
    protected function isExcludedFromPublicFrontGate(Request $request, array $excludePaths, array $excludeRoutes): bool
    {
        foreach ($excludePaths as $path) {
            if ($request->is(app()->getLocale().'/'.$path) || $request->is($path)) {
                return true;
            }
        }

        foreach ($excludeRoutes as $route) {
            if (Str::startsWith($request->route()?->getName(), $route)) {
                return true;
            }
        }

        return false;
    }
}
