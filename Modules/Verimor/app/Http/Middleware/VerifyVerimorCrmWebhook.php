<?php

namespace Modules\Verimor\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Config\Constatnt;
use Symfony\Component\HttpFoundation\Response;

class VerifyVerimorCrmWebhook
{
    private const DEFAULT_ALLOWED_IP = '194.49.126.36';

    public function handle(Request $request, Closure $next): Response
    {
        $expectedToken = (string) config('verimor.webhook_token');
        if ($expectedToken === '') {
            logger()->error('Verimor CRM webhook token is not set');

            abort(503);
        }

        $token = (string) $request->route('token', '');
        if (! hash_equals($expectedToken, $token)) {
            logger()->error('Verimor CRM webhook token mismatch');

            abort(403);
        }

        $allowedRaw = (string) getSetting(Constatnt::VERIMOR_WEBHOOK_ALLOWED_IPS, self::DEFAULT_ALLOWED_IP);
        $allowedIps = array_values(array_unique(array_filter(
            array_map('trim', explode(',', $allowedRaw)),
            static fn (string $ip) => $ip !== '' && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false
        )));

        if ($allowedIps === []) {
            logger()->error('Verimor CRM webhook allowed IPs list is empty or invalid');

            abort(403);
        }

        $clientIp = $request->ip();
        if (! in_array($clientIp, $allowedIps, true)) {
            logger()->error('Verimor CRM webhook IP not allowed', ['ip' => $clientIp]);

            abort(403);
        }

        return $next($request);
    }
}
