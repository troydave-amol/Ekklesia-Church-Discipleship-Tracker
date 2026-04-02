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
use App\Http\Controllers\SettingsController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

Route::middleware(['auth', 'church.tenant'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Church Admin (registering pastor) — legacy role slug "pastor" still accepted by middleware
    Route::middleware('role:church_admin,pastor')->group(function () {

        Route::get('/church/subscription', [ChurchController::class, 'subscription'])->name('church.subscription');
        Route::post('/church/subscription', [ChurchController::class, 'updatePlan'])->name('church.subscription.update');

        Route::get('/users', [DiscipleController::class, 'index'])->name('users.index');
        Route::get('/users/create', [DiscipleController::class, 'create'])->name('users.create');
        Route::post('/users', [DiscipleController::class, 'store'])->middleware('subscription:add_disciple')->name('users.store');
        Route::get('/users/{id}/edit', [DiscipleController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [DiscipleController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [DiscipleController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/import', [DiscipleController::class, 'import'])->middleware('subscription:bulk_import')->name('users.import');

        Route::get('/programs', [ProgramController::class, 'index'])->name('programs.index');
        Route::post('/programs', [ProgramController::class, 'store'])->name('programs.store');
        Route::put('/programs/{id}', [ProgramController::class, 'update'])->name('programs.update');
        Route::delete('/programs/{id}', [ProgramController::class, 'destroy'])->name('programs.destroy');
        Route::patch('programs/{program}/toggle', [ProgramController::class, 'toggle'])->name('programs.toggle');
        Route::get('programs/{program}/edit', [ProgramController::class, 'edit'])->name('programs.edit');

        Route::post('/lessons', [LessonController::class, 'store'])->name('lessons.store');
        Route::put('/lessons/{id}', [LessonController::class, 'update'])->name('lessons.update');
        Route::delete('/lessons/{id}', [LessonController::class, 'destroy'])->name('lessons.destroy');
        Route::get('lessons/create', [LessonController::class, 'create'])->name('lessons.create');
        Route::get('lessons/{lesson}/edit', [LessonController::class, 'edit'])->name('lessons.edit');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

        Route::middleware('subscription:export_sheets')->group(function () {
            Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
            Route::get('/reports/attendance', [ReportController::class, 'attendanceExport'])->name('reports.attendance');
            Route::get('/reports/devotionals', [ReportController::class, 'devotionalsExport'])->name('reports.devotionals');
            Route::get('/reports/programs', [ReportController::class, 'programsExport'])->name('reports.programs');
            Route::get('/reports/historical', [ReportController::class, 'historicalExport'])->name('reports.historical');
        });
    });

    Route::middleware('role:church_admin,pastor,program_leader,leader,cell_leader')
    ->get('/users/{id}', [DiscipleController::class, 'show'])
    ->whereNumber('id')
    ->name('users.show');

    Route::middleware('role:church_admin,pastor,program_leader,leader,cell_leader')->group(function () {
        Route::get('/my-disciples', [DiscipleController::class, 'leaderDisciples'])->name('users.disciples');

        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');

        Route::middleware('subscription:devotional')->group(function () {
            Route::get('/devotionals', [DevotionalController::class, 'index'])->name('devotionals.index');
            Route::post('/devotionals/bulk', [DevotionalController::class, 'bulkStore'])->name('devotionals.bulk');
            Route::post('/devotionals/self', [DevotionalController::class, 'markComplete'])->name('devotionals.complete');
        });
    });

    Route::middleware('role:disciple')->group(function () {
        Route::get('/my-lessons', [LessonController::class, 'myLessons'])->name('lessons.my');
        Route::get('/my-progress', [DashboardController::class, 'discipleDashboard'])->name('progress.my');
    });

    Route::middleware(['role:disciple', 'subscription:devotional'])->group(function () {
        Route::get('/my-devotionals', [DevotionalController::class, 'mine'])->name('devotionals.mine');
        Route::post('/devotionals/self', [DevotionalController::class, 'markComplete'])->name('devotionals.complete');
    });

    Route::get('/lessons', [LessonController::class, 'index'])->name('lessons.index');
    Route::get('lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');

    Route::get('/settings/church', [SettingsController::class, 'church'])->name('settings.church');
    Route::patch('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');

    Route::middleware('role:church_admin,pastor')->group(function () {
        Route::get('/settings/church', [SettingsController::class, 'church'])->name('settings.church');
        Route::patch('/settings/church', [SettingsController::class, 'updateChurch'])->name('settings.church.update');
        Route::get('/settings/upgrade/{plan}', [SettingsController::class, 'upgrade'])->name('settings.upgrade');
    });
});
