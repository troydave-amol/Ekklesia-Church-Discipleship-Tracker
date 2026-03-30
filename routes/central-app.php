<?php

use App\Http\Controllers\Admin\CentralDashboardController;
use App\Http\Controllers\Admin\TenantSignupController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TenantRegistrationController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

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

    Route::resource('tenants', TenantRegistrationController::class)->only(['index', 'show']);
});

// ── Tenant routes (tenancy initialized here) ──────────────────────────────────

Route::middleware([
    'web',
    InitializeTenancyBySubdomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('tenant.login');
    Route::post('/login', [AuthController::class, 'login'])->name('tenant.login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('tenant.logout');

    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('tenant.dashboard');
        // all other tenant routes go here
    });
});
