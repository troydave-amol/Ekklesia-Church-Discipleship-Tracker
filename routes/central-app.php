<?php

use App\Http\Controllers\Admin\CentralDashboardController;
use App\Http\Controllers\Admin\TenantSignupController;
use App\Http\Controllers\TenantRegistrationController;
use Illuminate\Support\Facades\Route;

// ── Central routes (no tenancy) ───────────────────────────────────────────────

Route::get('/', fn () => view('landing'))->name('home');
Route::get('/register', fn () => view('register'))->name('register');
Route::get('/provision', fn () => view('provision'))->name('provision');
Route::get('/registration-pending', fn () => view('registration-pending'))->name('registration.pending');

Route::post('/register-tenant', [TenantRegistrationController::class, 'store']);
Route::get('/check-subdomain', [TenantRegistrationController::class, 'checkSubdomain'])->name('check.subdomain');

Route::get('/forgot-password', fn () => view('auth.forgot-password'))->name('password.request');

// Central SaaS management (monitoring, approvals before domain + DB, tenant list)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn () => redirect()->route('admin.dashboard'));
    Route::get('/dashboard', CentralDashboardController::class)->name('dashboard');

    Route::get('/signups', [TenantSignupController::class, 'index'])->name('signups.index');
    Route::post('/signups/{signup}/approve', [TenantSignupController::class, 'approve'])->name('signups.approve');
    Route::post('/signups/{signup}/reject', [TenantSignupController::class, 'reject'])->name('signups.reject');

    Route::resource('tenants', TenantRegistrationController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);
    Route::patch('/tenants/{tenant}/status', [TenantRegistrationController::class, 'toggleStatus'])->name('tenants.toggle-status');
});

// Tenant subdomain routes are defined in routes/tenant.php (loaded by TenancyServiceProvider).
