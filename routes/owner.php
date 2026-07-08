<?php

use App\Http\Controllers\Owner\ExportFinanceController;
use App\Livewire\AuditLogIndex;
use App\Livewire\Owner\Dashboard;
use App\Livewire\Owner\FinanceIndex;
use App\Livewire\Owner\ManageAdminIndex;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Owner Routes
|--------------------------------------------------------------------------
|
| Routes for owner role: Dashboard, Finance, Manage Admin, Audit Log.
| All routes here are protected by auth middleware and owner role only.
| Owner also has full access to all admin routes (via role:admin,owner).
|
*/

Route::middleware(['auth', 'verified', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    // Owner Dashboard (PRD §8.15)
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Laporan Keuangan (PRD §4.13, §8.16)
    Route::get('/finance', FinanceIndex::class)->name('finance');

    // Manage Admin
    Route::get('/manage-admin', ManageAdminIndex::class)->name('manage-admin');

    // Audit Log (PRD §3.3)
    Route::get('/audit-log', AuditLogIndex::class)->name('audit-log');

    // Export Finance (PRD §8.16)
    Route::get('/finance/export', ExportFinanceController::class)->name('export-finance');
});
