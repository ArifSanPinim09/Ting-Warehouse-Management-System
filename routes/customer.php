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
    // Customer dashboard
    // Route::get('/customer/dashboard', [App\Http\Controllers\Customer\DashboardController::class, 'index'])->name('customer.dashboard');

    // My Boxes
    // Route::get('/customer/my-boxes', [App\Http\Controllers\Customer\BoxController::class, 'index'])->name('customer.my-boxes');

    // My Invoices
    // Route::get('/customer/my-invoices', [App\Http\Controllers\Customer\InvoiceController::class, 'index'])->name('customer.my-invoices');

    // Checkout
    // Route::get('/customer/checkout', [App\Http\Controllers\Customer\CheckoutController::class, 'index'])->name('customer.checkout');

    // Komplain
    // Route::get('/customer/komplain', [App\Http\Controllers\Customer\KomplainController::class, 'index'])->name('customer.komplain');
});
