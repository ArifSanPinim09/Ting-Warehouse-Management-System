<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Routes for admin role: Dashboard, Box Management, Invoice Management,
| Verification, Settings.
| All routes here are protected by auth middleware and admin role.
|
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // Admin dashboard
    // Route::get('/admin/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');

    // Manage Boxes
    // Route::get('/admin/manage-boxes', [App\Http\Controllers\Admin\BoxController::class, 'index'])->name('admin.manage-boxes');

    // Manage Invoices
    // Route::get('/admin/manage-invoices', [App\Http\Controllers\Admin\InvoiceController::class, 'index'])->name('admin.manage-invoices');

    // Verification
    // Route::get('/admin/verification', [App\Http\Controllers\Admin\VerificationController::class, 'index'])->name('admin.verification');

    // Settings
    // Route::get('/admin/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('admin.settings');
});
