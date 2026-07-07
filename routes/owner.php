<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Owner Routes
|--------------------------------------------------------------------------
|
| Routes for owner role: Dashboard, Finance, Data, User Management.
| All routes here are protected by auth middleware and owner role.
|
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // Owner dashboard
    // Route::get('/owner/dashboard', [App\Http\Controllers\Owner\DashboardController::class, 'index'])->name('owner.dashboard');

    // Finance reports
    // Route::get('/owner/finance', [App\Http\Controllers\Owner\FinanceController::class, 'index'])->name('owner.finance');

    // Data management
    // Route::get('/owner/data', [App\Http\Controllers\Owner\DataController::class, 'index'])->name('owner.data');

    // User management
    // Route::get('/owner/users', [App\Http\Controllers\Owner\UserController::class, 'index'])->name('owner.users');
});
