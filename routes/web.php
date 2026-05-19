<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailTestController;
use App\Http\Controllers\LogViewerController;
use App\Http\Controllers\SchedulerLogController;
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
    Route::get('/dashboard/scheduler', [SchedulerLogController::class, 'index'])->name('dashboard.scheduler');
    Route::delete('/dashboard/scheduler', [SchedulerLogController::class, 'destroyAll'])->name('dashboard.scheduler.clear');
    Route::get('/dashboard/logs', [LogViewerController::class, 'index'])->name('dashboard.logs');
    Route::get('/dashboard/logs/{file}', [LogViewerController::class, 'show'])->name('dashboard.logs.show');
    Route::delete('/dashboard/logs/{file}', [LogViewerController::class, 'clear'])->name('dashboard.logs.clear');
    Route::get('/dashboard/email', [EmailTestController::class, 'show'])->name('dashboard.email');
    Route::post('/dashboard/email', [EmailTestController::class, 'send'])->name('dashboard.email.send')->middleware('throttle:5,1');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});
