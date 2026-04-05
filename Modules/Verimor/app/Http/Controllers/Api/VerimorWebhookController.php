<?php

namespace Modules\Verimor\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Verimor\Jobs\ProcessVerimorCrmWebhookJob;

class VerimorWebhookController extends Controller
{
    public function crm(Request $request, string $token): Response
    {
        $expected = (string) config('verimor.webhook_token');
        if ($expected === '') {
            abort(503);
        }
        if (! hash_equals($expected, $token)) {
            abort(403);
        }

        ProcessVerimorCrmWebhookJob::dispatch($request->all());

        return response('OK', 200);
    }
}
