<?php

namespace Modules\Verimor\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Verimor\Jobs\ProcessVerimorCrmWebhookJob;

class VerimorWebhookController extends Controller
{
    public function crm(Request $request): Response
    {
        logger()->info('Verimor CRM webhook received', ['request' => $request->all()]);

        // $expected = (string) config('verimor.webhook_token');
        // if ($expected === '') {
        //     abort(503);
        // }
        // if (! hash_equals($expected, $token)) {
        //     logger()->error('Verimor CRM webhook token mismatch', ['expected' => $expected, 'token' => $token]);
        //     abort(403);
        // }

        ProcessVerimorCrmWebhookJob::dispatch($request->all());

        return response('OK', 200);
    }
}
