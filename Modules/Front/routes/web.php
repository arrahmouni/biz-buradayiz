<?php

use Illuminate\Support\Facades\Route;
use Modules\Front\Http\Controllers\ContentPageController;
use Modules\Front\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::controller(HomeController::class)->group(function () {
    Route::get('/', 'index')->name('index');
});

Route::get('page/{slug}', [ContentPageController::class, 'show'])->name('page.show');
