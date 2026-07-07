<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Routes for admin role: Dashboard, Box Management, Invoice Management,
| Verification, Settings.
| All routes here are protected by auth middleware and admin/owner role.
|
*/

Route::middleware(['auth', 'verified', 'role:admin,owner'])->prefix('admin')->name('admin.')->group(function () {
    // Admin dashboard (PRD §4.9)
    Route::view('/dashboard', 'admin.dashboard')->name('dashboard');

    // Manage Boxes (PRD §4.9)
    // Route::get('/manage-boxes', [App\Http\Controllers\Admin\BoxController::class, 'index'])->name('manage-boxes');

    // Generate Invoice (PRD §4.10)
    // Route::get('/invoices', [App\Http\Controllers\Admin\InvoiceController::class, 'index'])->name('invoices');

    // Verification (PRD §4.11)
    // Route::get('/verification', [App\Http\Controllers\Admin\VerificationController::class, 'index'])->name('verification');

    // Settings (PRD §4.12)
    // Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings');

    // Info Customer (PRD §4.14)
    // Route::get('/customers', [App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('customers');

    // Est Update (PRD §4.15)
    // Route::get('/est-update', [App\Http\Controllers\Admin\EstUpdateController::class, 'index'])->name('est-update');

    // Recap (PRD §4.16)
    // Route::get('/recap', [App\Http\Controllers\Admin\RecapController::class, 'index'])->name('recap');
});
