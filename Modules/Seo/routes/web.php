<?php

use Illuminate\Support\Facades\Route;
use Modules\Seo\Http\Controllers\Admin\SeoController;

Route::prefix('seo-entries')->name('entries.')->controller(SeoController::class)->group(function () {
    Route::get('list', 'index')->name('index');
    Route::get('datatable', 'datatable')->name('datatable');
    Route::get('ajax-list', 'ajaxList')->name('ajaxList');
    Route::get('create', 'create')->name('create');
    Route::post('create', 'postCreate')->name('postCreate');
    Route::get('update/{model}', 'update')->name('update');
    Route::put('update/{model}', 'postUpdate')->name('postUpdate');
    Route::delete('soft-delete/{model}', 'softDelete')->name('softDelete');
    Route::delete('hard-delete/{model}', 'hardDelete')->name('hardDelete');
    Route::post('restore/{model}', 'restore')->name('restore');
    Route::delete('bulk-soft-delete', 'bulkSoftDelete')->name('bulkSoftDelete');
    Route::delete('bulk-hard-delete', 'bulkHardDelete')->name('bulkHardDelete');
    Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
});
