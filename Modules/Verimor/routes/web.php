<?php

use Illuminate\Support\Facades\Route;
use Modules\Verimor\Http\Controllers\Admin\VerimorCallEventController;

Route::prefix('verimor-call-events')->name('verimor_call_events.')->controller(VerimorCallEventController::class)->group(function () {
    Route::get('list', 'index')->name('index');
    Route::get('datatable', 'datatable')->name('datatable');
    Route::get('view/{model}', 'viewAsModal')->name('viewAsModal');
});
