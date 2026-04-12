<?php

use Illuminate\Support\Facades\Route;
use Modules\Front\Http\Controllers\ContactController;
use Modules\Front\Http\Controllers\ContentController;
use Modules\Front\Http\Controllers\HomeController;
use Modules\Front\Http\Controllers\ProviderController;

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

Route::get('/', [HomeController::class, 'index'])->name('index');

Route::controller(ProviderController::class)->group(function () {
    Route::get('/search', 'search')->name('search');
    Route::get('/providers/{provider}', 'showProvider')
        ->where('provider', '[0-9a-z]+(?:-[0-9a-z]+)*')
        ->name('provider.show');
});

Route::controller(ContactController::class)->prefix('contact')->name('contact.')->group(function () {
    Route::get('/', 'show')->name('show');
    Route::post('/', 'store')->name('store')->middleware('throttle:3,1');
});

Route::controller(ContentController::class)->group(function () {
    Route::prefix('blog')->name('blog.')->group(function () {
        Route::get('/', 'BlogList')->name('index');
        Route::get('/{slug}', 'showBlog')->name('show');
    });
    Route::prefix('page')->name('page.')->group(function () {
        Route::get('/faq', 'faq')->name('faq');
        Route::get('/{slug}', 'showPage')->name('show');
    });
});

Route::view('/test', 'front::test');
