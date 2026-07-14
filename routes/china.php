<?php

use App\Livewire\WhChina\Dashboard;
use App\Livewire\WhChina\NewBatch;
use App\Livewire\WhChina\Requests;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| WH China Routes
|--------------------------------------------------------------------------
|
| Routes for China warehouse admin role: Input data, create batches,
| view customer requests. All routes protected by auth and china_admin role.
|
*/

Route::middleware(['auth', 'verified', 'role:china_admin'])->prefix('china')->name('china.')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/new-batch', NewBatch::class)->name('new-batch');
    Route::get('/requests', Requests::class)->name('requests');
});
