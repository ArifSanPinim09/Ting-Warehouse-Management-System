<?php

use App\Livewire\AuditLogIndex;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Owner Routes
|--------------------------------------------------------------------------
|
| Routes for owner role: Dashboard, Finance, Data, User Management.
| All routes here are protected by auth middleware and owner role only.
|
*/

Route::middleware(['auth', 'verified', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    // Owner dashboard (PRD §4.13)
    Route::view('/dashboard', 'owner.dashboard')->name('dashboard');

    // Audit Log (PRD §3.3, CLAUDE.md §3.3)
    Route::get('/audit-log', AuditLogIndex::class)->name('audit-log');

    // Finance reports (PRD §4.13)
    // Route::get('/finance', [App\Http\Controllers\Owner\FinanceController::class, 'index'])->name('finance');

    // Data management
    // Route::get('/data', [App\Http\Controllers\Owner\DataController::class, 'index'])->name('data');

    // User management
    // Route::get('/users', [App\Http\Controllers\Owner\UserController::class, 'index'])->name('users');
});
