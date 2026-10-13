<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GeofenceController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

// Tamu: halaman login. Pengguna yang sudah masuk dialihkan ke dashboard oleh middleware "guest".
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

// Semua halaman aplikasi wajib login. Tamu dialihkan ke route bernama "login".
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/lokasi-ternak', [LocationController::class, 'index'])->name('locations');

    Route::get('/geofence', [GeofenceController::class, 'edit'])->name('geofence.edit');
    Route::post('/geofence', [GeofenceController::class, 'update'])->name('geofence.update');

    Route::get('/pengaturan', [SettingsController::class, 'index'])->name('settings');
    Route::put('/pengaturan/profil', [SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::put('/pengaturan/password', [SettingsController::class, 'updatePassword'])->name('settings.password');

    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
});
