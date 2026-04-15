<?php

namespace Modules\Front\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Auth\Enums\UserType;
use Modules\Base\Http\Middleware\BaseMiddleware;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsServiceProvider extends BaseMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->type !== UserType::ServiceProvider) {
            auth()->guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('front.provider.login')
                ->withErrors(['email' => __('front::auth.wrong_account_type')]);
        }

        return $next($request);
    }
}
