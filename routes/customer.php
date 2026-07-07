<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
|
| Routes for customer role: Dashboard, Box, Invoice, Checkout, Komplain.
| All routes here are protected by auth middleware and customer role.
|
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // Customer dashboard (PRD §4.2)
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // My Boxes (PRD §4.3)
    // Route::get('/my-boxes', [App\Http\Controllers\Customer\BoxController::class, 'index'])->name('customer.my-boxes');

    // Setor Resi (PRD §4.4)
    // Route::get('/setor-resi', [App\Http\Controllers\Customer\ItemController::class, 'create'])->name('customer.setor-resi');

    // My Invoices (PRD §4.5)
    // Route::get('/my-invoices', [App\Http\Controllers\Customer\InvoiceController::class, 'index'])->name('customer.my-invoices');

    // Checkout (PRD §4.6)
    // Route::get('/checkout', [App\Http\Controllers\Customer\CheckoutController::class, 'index'])->name('customer.checkout');

    // Komplain (PRD §4.7)
    // Route::get('/komplain', [App\Http\Controllers\Customer\KomplainController::class, 'index'])->name('customer.komplain');

    // Profile
    Route::view('/profile', 'profile')->name('profile');
});
