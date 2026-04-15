<?php

use Illuminate\Support\Facades\Route;
use Modules\Front\Http\Controllers\ContactController;
use Modules\Front\Http\Controllers\ContentController;
use Modules\Front\Http\Controllers\HomeController;
use Modules\Front\Http\Controllers\ProviderAuthController;
use Modules\Front\Http\Controllers\ProviderController;
use Modules\Front\Http\Controllers\ProviderDashboardController;

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

Route::prefix('provider')->name('provider.')->group(function () {
    Route::controller(ProviderAuthController::class)->group(function () {
        Route::middleware('guest')->group(function () {
            Route::get('login', 'showLoginForm')->name('login');
            Route::post('login', 'login')->name('login.store')->middleware('throttle:10,1');
            Route::get('register/apply', 'showRegisterForm')->name('register.form');
            Route::get('register', 'showRegisterLanding')->name('register');
            Route::post('register', 'register')->name('register.store')->middleware('throttle:5,1');
            Route::get('forgot-password', 'showForgotPasswordForm')->name('password.request');
            Route::post('forgot-password', 'sendResetLinkEmail')->name('password.email')->middleware('throttle:5,1');
            Route::get('reset-password/{token}', 'showResetForm')->name('password.reset');
            Route::post('reset-password', 'resetPassword')->name('password.store')->middleware('throttle:5,1');
        });
        Route::post('logout', 'logout')->name('logout')->middleware(['auth', 'active.user', 'service.provider']);
    });
    Route::controller(ProviderDashboardController::class)->group(function () {
        Route::get('dashboard', 'dashboard')->name('dashboard')->middleware(['auth', 'active.user', 'service.provider']);
        Route::post('dashboard/fragments/subscription-history', 'subscriptionHistoryFragment')
            ->name('dashboard.fragments.subscription-history')
            ->middleware(['auth', 'active.user', 'service.provider', 'throttle:60,1']);
        Route::post('dashboard/fragments/call-log', 'callLogFragment')
            ->name('dashboard.fragments.call-log')
            ->middleware(['auth', 'active.user', 'service.provider', 'throttle:60,1']);
        Route::post('subscriptions/request', 'requestPackageSubscription')
            ->name('subscriptions.request')
            ->middleware(['auth', 'active.user', 'service.provider', 'throttle:10,1']);
    });
});

Route::controller(ProviderController::class)->group(function () {
    Route::get('/search', 'search')->name('search');
    Route::get('/providers/{provider}', 'showProvider')
        ->where('provider', '[0-9a-z]+(?:-[0-9a-z]+)*')
        ->name('provider.show');
    Route::post('/providers/{provider}/reviews', 'storeProviderReview')
        ->where('provider', '[0-9a-z]+(?:-[0-9a-z]+)*')
        ->name('provider.reviews.store')
        ->middleware('throttle:3,1');
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
