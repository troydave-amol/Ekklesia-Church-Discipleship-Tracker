<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TenantRegistrationController;
use App\Http\Controllers\DashboardController;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

// ── Central routes (no tenancy) ───────────────────────────────────────────────

Route::get('/', fn() => view('landing'))->name('home');
Route::get('/register', fn() => view('register'))->name('register');
Route::get('/provision', fn() => view('provision'))->name('provision');

Route::post('/register-tenant', [TenantRegistrationController::class, 'store']);
Route::get('/check-subdomain',  [TenantRegistrationController::class, 'checkSubdomain'])->name('check.subdomain');

Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('password.request');

// Admin panel
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn() => redirect()->route('admin.tenants.index'));
    Route::resource('tenants', TenantRegistrationController::class)->except(['create','store']);
    Route::patch('tenants/{tenant}/activate',   [TenantRegistrationController::class, 'activate'])->name('tenants.activate');
    Route::patch('tenants/{tenant}/deactivate', [TenantRegistrationController::class, 'deactivate'])->name('tenants.deactivate');
});

// ── Tenant routes (tenancy initialized here) ──────────────────────────────────

Route::middleware([
    'web',
    InitializeTenancyBySubdomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('tenant.login');
    Route::post('/login', [AuthController::class, 'login'])->name('tenant.login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('tenant.logout');

    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('tenant.dashboard');
        // all other tenant routes go here
    });
});