<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

// Dashboard routes are now in customer.php, admin.php, owner.php
// with role-based middleware per PRD §7.5

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
