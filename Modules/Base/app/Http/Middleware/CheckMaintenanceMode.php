<?php

namespace Modules\Base\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckMaintenanceMode extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $excludePaths = ['admin/*'];
        $excludeRoutes = ['admin.*'];

        if ($this->isExcludedFromPublicFrontGate($request, $excludePaths, $excludeRoutes)) {
            return $next($request);
        }

        if ($this->isMaintenanceMode()) {
            return sendMaintenanceModeResponse();
        }

        return $next($request);
    }

    /**
     * Check if the application is in maintenance mode.
     */
    private function isMaintenanceMode(): bool
    {
        return getSetting('maintenance_mode', false);
    }
}
