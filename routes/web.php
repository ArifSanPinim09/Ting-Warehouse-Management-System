<?php

use App\Livewire\Notifications\NotificationIndex;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

// Dashboard routes are now in customer.php, admin.php, owner.php
// with role-based middleware per PRD §7.5

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Notifications — accessible by all authenticated users (Revisi §2.11.3, §4.3)
Route::get('/notifications', NotificationIndex::class)
    ->middleware(['auth'])
    ->name('notifications');

require __DIR__.'/auth.php';
