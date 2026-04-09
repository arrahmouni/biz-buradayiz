<?php

use Illuminate\Support\Facades\Route;
use Modules\Zms\Http\Controllers\Api\CityController;
use Modules\Zms\Http\Controllers\Api\CountryController;
use Modules\Zms\Http\Controllers\Api\StateController;

/*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
*/

Route::controller(CountryController::class)->prefix('countries')->group(function () {
    Route::get('list', 'list');
    Route::get('show/{id}', 'show');
});

Route::controller(StateController::class)->prefix('states')->group(function () {
    Route::get('list', 'list')->name('zms.states.list');
    Route::get('show/{id}', 'show')->name('zms.states.show');
});

Route::controller(CityController::class)->prefix('cities')->group(function () {
    Route::get('list', 'list')->name('zms.cities.list');
    Route::get('show/{id}', 'show')->name('zms.cities.show');
});
