<?php

use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Route;
use Modules\Verimor\Http\Controllers\Api\VerimorWebhookController;

Route::post('webhooks/verimor/crm/{token}', [VerimorWebhookController::class, 'crm'])
    ->withoutMiddleware([ThrottleRequests::class])
    ->name('verimor.webhooks.crm');
