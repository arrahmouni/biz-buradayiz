<?php

use Illuminate\Support\Facades\Route;
use Modules\Seo\Http\Controllers\Api\SeoController as SeoApiController;

Route::controller(SeoApiController::class)->prefix('seo')->group(function () {
    Route::get('static/{key}', 'staticPage')->name('seo.static');
    Route::get('content/{type}/{slug}', 'contentBySlug')->name('seo.content');
});
