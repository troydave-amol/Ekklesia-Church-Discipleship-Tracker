<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ChurchController;
use App\Http\Controllers\DiscipleController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DevotionalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

// Root tenant landing page
// Note: 'web', InitializeTenancyBySubdomain, and PreventAccessFromCentralDomains
// are already applied by TenancyServiceProvider::mapRoutes()
Route::get('/', function () {
    return redirect()->route('login');
});

// ══════════════════════════════════════════════
//  GUEST ROUTES (tenant-level auth)
// ══════════════════════════════════════════════
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

// ══════════════════════════════════════════════
//  AUTHENTICATED ROUTES
//  Stack: auth → church.tenant (binds church_id)
// ══════════════════════════════════════════════
Route::middleware(['auth', 'church.tenant'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Single dashboard — dispatches by role internally
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── PASTOR ONLY ──────────────────────────────────────────
    Route::middleware('role:pastor')->group(function () {

        // Church & subscription management
        Route::get('/church/subscription',  [ChurchController::class, 'subscription'])->name('church.subscription');
        Route::post('/church/subscription', [ChurchController::class, 'updatePlan'])->name('church.subscription.update');

        // Member management (with plan limit gates)
        Route::get('/users',         [DiscipleController::class, 'index'])->name('users.index');
        Route::get('/users/create',  [DiscipleController::class, 'create'])->name('users.create');
        Route::post('/users',        [DiscipleController::class, 'store'])->middleware('subscription:add_disciple')->name('users.store');
        Route::get('/users/{id}',    [DiscipleController::class, 'show'])->name('users.show');
        Route::get('/users/{id}/edit', [DiscipleController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}',    [DiscipleController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [DiscipleController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/import', [DiscipleController::class, 'import'])->middleware('subscription:bulk_import')->name('users.import');

        // Programs
        Route::get('/programs',          [ProgramController::class, 'index'])->name('programs.index');
        Route::post('/programs',         [ProgramController::class, 'store'])->name('programs.store');
        Route::put('/programs/{id}',     [ProgramController::class, 'update'])->name('programs.update');
        Route::delete('/programs/{id}',  [ProgramController::class, 'destroy'])->name('programs.destroy');
        Route::patch('programs/{program}/toggle', [ProgramController::class, 'toggle'])->name('programs.toggle');
        Route::get('programs/{program}/edit', [ProgramController::class, 'edit'])->name('programs.edit');

        // Lessons (pastor manages, disciples read)
        Route::post('/lessons',          [LessonController::class, 'store'])->name('lessons.store');
        Route::put('/lessons/{id}',      [LessonController::class, 'update'])->name('lessons.update');
        Route::delete('/lessons/{id}',   [LessonController::class, 'destroy'])->name('lessons.destroy');
        Route::get('lessons/create', [LessonController::class, 'create'])->name('lessons.create');
        Route::get('lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');
        Route::get('lessons/{lesson}/edit', [LessonController::class, 'edit'])->name('lessons.edit');

        // Reports — Standard/Premium only
        Route::middleware('subscription:export_sheets')->group(function () {
            Route::get('/reports',         [ReportController::class, 'index'])->name('reports.index');
            Route::get('/reports/export',  [ReportController::class, 'export'])->name('reports.export');
        });
    });

    // ── PASTOR + LEADER ───────────────────────────────────────
    Route::middleware('role:pastor,leader')->group(function () {

        // Attendance tracking
        Route::get('/attendance',   [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance',  [AttendanceController::class, 'store'])->name('attendance.store');

        // Devotional tracking — Basic+ only
        Route::middleware('subscription:devotional')->group(function () {
            Route::get('/devotionals',              [DevotionalController::class, 'index'])->name('devotionals.index');
            Route::post('/devotionals/mark',        [DevotionalController::class, 'markComplete'])->name('devotionals.complete');
        });
    });

    // ── DISCIPLE ONLY ─────────────────────────────────────────
    Route::middleware('role:disciple')->group(function () {
        Route::get('/my-lessons',   [LessonController::class, 'myLessons'])->name('lessons.my');
        Route::get('/my-progress',  [DashboardController::class, 'disciple'])->name('progress.my');
        Route::post('/devotionals/complete', [DevotionalController::class, 'markComplete'])->name('devotionals.complete');
    });

    // ── SHARED READ (any authenticated role) ──────────────────
    Route::get('/lessons', [LessonController::class, 'index'])->name('lessons.index');
    Route::get('/settings/church', fn() => abort(404))->name('settings.church');
});