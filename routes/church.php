<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Church\AttendanceController;
use App\Http\Controllers\Church\MemberController;

Route::middleware(['auth', 'role:pastor,leader,member'])->prefix('church')->name('church.')->group(function () {

    // Attendance — gated by subscription
    Route::middleware('subscription:attendance')
        ->resource('attendance', AttendanceController::class);

    // Members
    Route::middleware('subscription:members')
        ->resource('members', MemberController::class);

    // Add other modules here...
});