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
        logger()->info('Verimor CRM webhook received', [
            'ip' => $request->ip(),
            'request' => $request->all(),
        ]);

        ProcessVerimorCrmWebhookJob::dispatch($request->all());

        return response('OK', 200);
    }
}
