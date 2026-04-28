<?php

use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Route;
use Modules\Verimor\Http\Controllers\Api\VerimorWebhookController;
use Modules\Verimor\Http\Middleware\VerifyVerimorCrmWebhook;

Route::post('webhooks/verimor/crm/{token}', [VerimorWebhookController::class, 'crm'])
    ->middleware([VerifyVerimorCrmWebhook::class])
    ->withoutMiddleware([ThrottleRequests::class])
    ->name('verimor.webhooks.crm');
