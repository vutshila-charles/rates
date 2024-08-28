<?php

use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;


Route::middleware('web')->group(function () {
    Route::get('/', [SiteController::class, 'viewHome'])->name('home');

    Route::get('/fetch-rates', [SiteController::class, 'fetchRates'])->name('fetch-rates');
    
    Route::post('/convert-currency', [SiteController::class, 'convertCurrency'])->name('convert-currency');
});