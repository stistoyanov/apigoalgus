<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailTestController;
use App\Http\Controllers\LogViewerController;
use App\Http\Controllers\SchedulerLogController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Scheduler: view = super_admin, admin, viewer, operator; clear = super_admin, admin, operator
    Route::get('/dashboard/scheduler', [SchedulerLogController::class, 'index'])
        ->middleware('role:super_admin,admin,viewer,operator')
        ->name('dashboard.scheduler');
    Route::delete('/dashboard/scheduler', [SchedulerLogController::class, 'destroyAll'])
        ->middleware('role:super_admin,admin,operator')
        ->name('dashboard.scheduler.clear');

    // Logs: view = super_admin, admin, viewer, operator; clear = super_admin, admin, operator
    Route::get('/dashboard/logs', [LogViewerController::class, 'index'])
        ->middleware('role:super_admin,admin,viewer,operator')
        ->name('dashboard.logs');
    Route::get('/dashboard/logs/{file}', [LogViewerController::class, 'show'])
        ->middleware('role:super_admin,admin,viewer,operator')
        ->name('dashboard.logs.show');
    Route::delete('/dashboard/logs/{file}', [LogViewerController::class, 'clear'])
        ->middleware('role:super_admin,admin,operator')
        ->name('dashboard.logs.clear');

    // Email test: super_admin, admin
    Route::middleware('role:super_admin,admin')->group(function () {
        Route::get('/dashboard/email', [EmailTestController::class, 'show'])->name('dashboard.email');
        Route::post('/dashboard/email', [EmailTestController::class, 'send'])->name('dashboard.email.send')->middleware('throttle:5,1');
    });

    // Users: super_admin, admin (admin cannot touch super admin users; enforced in UserController)
    Route::middleware('role:super_admin,admin')->group(function () {
        Route::get('/dashboard/users', [UserController::class, 'index'])->name('dashboard.users');
        Route::get('/dashboard/users/roles', [UserController::class, 'roles'])->name('dashboard.users.roles');
        Route::post('/dashboard/users', [UserController::class, 'store'])->name('dashboard.users.store');
        Route::put('/dashboard/users/{user}', [UserController::class, 'update'])->name('dashboard.users.update');
        Route::post('/dashboard/users/{user}/toggle', [UserController::class, 'toggleActive'])->name('dashboard.users.toggle');
        Route::post('/dashboard/users/{user}/delete', [UserController::class, 'destroy'])->name('dashboard.users.destroy');
    });
});
