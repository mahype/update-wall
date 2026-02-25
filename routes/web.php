<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\EnsureIsAdmin;
use App\Http\Middleware\EnsureNotInstalled;
use Illuminate\Support\Facades\Route;

Route::middleware(EnsureNotInstalled::class)->group(function () {
    Route::get('/install', [InstallController::class, 'show'])->name('install.show');
    Route::post('/install', [InstallController::class, 'store'])->name('install.store');
});

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/status', [DashboardController::class, 'status'])->name('dashboard.status');

    // Machine detail
    Route::get('/machines/{machine}', [MachineController::class, 'show'])->name('machines.show');
    Route::get('/machines/{machine}/report/{reportId}', [MachineController::class, 'show'])->name('machines.report');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin routes
    Route::prefix('admin')->name('admin.')->middleware(EnsureIsAdmin::class)->group(function () {
        // Token management
        Route::get('tokens', [Admin\TokenController::class, 'index'])->name('tokens.index');
        Route::get('tokens/create', [Admin\TokenController::class, 'create'])->name('tokens.create');
        Route::post('tokens', [Admin\TokenController::class, 'store'])->name('tokens.store');
        Route::delete('tokens/{token}', [Admin\TokenController::class, 'destroy'])->name('tokens.destroy');
        Route::post('tokens/{token}/revoke', [Admin\TokenController::class, 'revoke'])->name('tokens.revoke');

        // User management
        Route::resource('users', Admin\UserController::class)->except(['show']);

        // Machine management
        Route::get('machines', [Admin\MachineController::class, 'index'])->name('machines.index');
        Route::delete('machines/{machine}', [Admin\MachineController::class, 'destroy'])->name('machines.destroy');

        // Documentation
        Route::get('docs', [Admin\DocumentationController::class, 'index'])->name('docs.index');
        Route::get('docs/{slug}', [Admin\DocumentationController::class, 'show'])->name('docs.show');

        // API Request Logs
        Route::get('logs', [Admin\LogController::class, 'index'])->name('logs.index');
        Route::get('logs/{log}', [Admin\LogController::class, 'show'])->name('logs.show');

        // Settings
        Route::get('settings', [Admin\SettingsController::class, 'index'])->name('settings.index');
        Route::put('settings', [Admin\SettingsController::class, 'update'])->name('settings.update');
    });
});

require __DIR__.'/auth.php';
