<?php

use App\Livewire\WhChina\Dashboard;
use App\Livewire\WhChina\GoodsWeightFees;
use App\Livewire\WhChina\History;
use App\Livewire\WhChina\NewBatch;
use App\Livewire\WhChina\RequestToSend;
use App\Livewire\WhChina\Requests;
use App\Livewire\WhChina\ShippingMaterialFees;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| WH China Routes
|--------------------------------------------------------------------------
|
| Routes for China warehouse admin role: Input data, create batches,
| view customer requests, send to cargo, manage fees.
|
*/

Route::middleware(['auth', 'verified', 'role:china_admin'])->prefix('china')->name('china.')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/new-batch', NewBatch::class)->name('new-batch');
    Route::get('/requests', Requests::class)->name('requests');
    Route::get('/request-to-send', RequestToSend::class)->name('request-to-send');
    Route::get('/history', History::class)->name('history');
    Route::get('/shipping-material-fees', ShippingMaterialFees::class)->name('shipping-material-fees');
    Route::get('/goods-weight-fees', GoodsWeightFees::class)->name('goods-weight-fees');
    Route::get('/export-service-fee', \App\Http\Controllers\ServiceFeeExportController::class)->name('export-service-fee');
});
