<?php

namespace Modules\Base\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Config\Constatnt;

class CheckComingSoonMode extends BaseMiddleware
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

        if ($this->shouldShowComingSoon()) {
            return response()->view('front::coming-soon', [], 200);
        }

        return $next($request);
    }

    private function shouldShowComingSoon(): bool
    {
        if (getSetting(Constatnt::MAINTENANCE_MODE, false)) {
            return false;
        }

        return (bool) getSetting(Constatnt::COMING_SOON_MODE, false);
    }
}
