<?php

use App\Livewire\Customer\BoxDirect;
use App\Livewire\Customer\BoxSharing;
use App\Livewire\Customer\CheckoutIndex;
use App\Livewire\Customer\CreateInvoice;
use App\Livewire\Customer\Dashboard;
use App\Livewire\Customer\InvoiceIndex;
use App\Livewire\Customer\Kalkulator;
use App\Livewire\Customer\KomplainIndex;
use App\Livewire\Customer\NoTuanIndex;
use App\Livewire\Customer\SetorResi;
use App\Livewire\Customer\UnmatchedResi;
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
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // My Boxes (PRD §4.3)
    Route::get('/box/sharing', BoxSharing::class)->name('customer.box.sharing');
    Route::get('/box/direct', BoxDirect::class)->name('customer.box.direct');

    // Setor Resi (PRD §4.4)
    Route::get('/setor-resi', SetorResi::class)->name('customer.setor-resi');

    // My Invoices (PRD §4.5)
    Route::get('/invoice', InvoiceIndex::class)->name('customer.invoice');

    // Buat Invoice (Revisi §2.8)
    Route::get('/create-invoice', CreateInvoice::class)->name('customer.create-invoice');

    // Checkout (PRD §4.6)
    Route::get('/checkout', CheckoutIndex::class)->name('customer.checkout');

    // Komplain (PRD §4.7)
    Route::get('/komplain', KomplainIndex::class)->name('customer.komplain');

    // Kalkulator (PRD §4.8)
    Route::get('/kalkulator', Kalkulator::class)->name('customer.kalkulator');

    // No Tuan (Revisi §2.1, §4.1)
    Route::get('/no-tuan', NoTuanIndex::class)->name('customer.no-tuan');

    // Resi Belum Dikenali — customer klaim unmatched WH China data
    Route::get('/unmatched-resi', UnmatchedResi::class)->name('customer.unmatched-resi');

    // Profile
    Route::view('/profile', 'profile')->name('profile');
});
