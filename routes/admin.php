<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:superadmin'])->prefix('admin')->name('admin.')->group(function () {
    // SuperAdmin routes
});