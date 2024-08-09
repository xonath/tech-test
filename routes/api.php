<?php

use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\StoreManagementController;
use App\Http\Middleware\TokenAuthentication;
use Illuminate\Support\Facades\Route;

Route::middleware(TokenAuthentication::class)
    ->withoutMiddleware('api')
    ->group(function () {
    // store management routes
    Route::post('/add-store', [StoreManagementController::class, 'addStore'])->name('add-store');

    // search routes
    Route::get('search/stores/{type}/{postcode}/{distance}', [SearchController::class, 'searchNearest'])->name('search-store');
    Route::get('search/delivery/{type}/{postcode}', [SearchController::class, 'searchDelivery'])->name('search-delivery');
});


