<?php

use App\Livewire\Admin\CustomerIndex;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\EstUpdate;
use App\Livewire\Admin\GenerateInvoice;
use App\Livewire\Admin\KursHistoryIndex;
use App\Livewire\Admin\ManageBox;
use App\Livewire\Admin\ManageCheckout;
use App\Livewire\Admin\ManageComplain;
use App\Livewire\Admin\RecapIndex;
use App\Livewire\Admin\SettingsIndex;
use App\Livewire\Admin\VerificationIndex;
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
    // Admin dashboard (PRD §8.11)
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Manage Boxes (PRD §4.9, §8.12)
    Route::get('/manage-boxes', ManageBox::class)->name('boxes');

    // Generate Invoice (PRD §4.10, §11.7)
    Route::get('/invoices', GenerateInvoice::class)->name('invoices');

    // Verification (PRD §4.11, §8.13)
    Route::get('/verification', VerificationIndex::class)->name('verification');

    // Settings / Rate (PRD §4.12, §8.14)
    Route::get('/settings', SettingsIndex::class)->name('settings');

    // Info Customer (PRD §4.14)
    Route::get('/customers', CustomerIndex::class)->name('customers');

    // Est Update (PRD §4.15)
    Route::get('/est-update', EstUpdate::class)->name('est-update');

    // Recap (PRD §4.16)
    Route::get('/recap', RecapIndex::class)->name('recap');

    // Checkout Requests (PRD §4.6, §7.3)
    Route::get('/checkouts', ManageCheckout::class)->name('checkouts');

    // Complains (PRD §4.7, §7.3)
    Route::get('/complains', ManageComplain::class)->name('complains');

    // History Kurs (Revisi §2.2, §4.1)
    Route::get('/kurs-history', KursHistoryIndex::class)->name('kurs-history');
});
