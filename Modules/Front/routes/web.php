<?php

use Illuminate\Support\Facades\Route;
use Modules\Front\Http\Controllers\BlogController;
use Modules\Front\Http\Controllers\ContentController;
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
Route::controller(BlogController::class)->prefix('blog')->name('blog.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{slug}', 'show')->name('show');
});
Route::controller(ContentController::class)->prefix('page')->name('page.')->group(function () {
    Route::get('/faq', 'faq')->name('faq');
    Route::get('/{slug}', 'showPage')->name('show');
});
