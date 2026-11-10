<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Modul Rute: Administrator Sistem
|--------------------------------------------------------------------------
| Menangani manajemen akun user, reset password, divisi & plafon jabatan,
| konfigurasi suku bunga/simpanan koperasi, dan audit log trail.
| Akses: Khusus Role Admin
| Prefix: /admin
| Name Prefix: admin.
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // 1. Modul Pengguna (Users) & Reset Password
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
    });

    // 2. Modul Master Data (Divisi & Jabatan dengan Batas Plafon)
    Route::get('/master', [MasterDataController::class, 'index'])->name('master.index');

    Route::prefix('divisi')->name('divisions.')->group(function () {
        Route::post('/', [MasterDataController::class, 'storeDivision'])->name('store');
        Route::put('/{division}', [MasterDataController::class, 'updateDivision'])->name('update');
        Route::delete('/{division}', [MasterDataController::class, 'destroyDivision'])->name('destroy');
    });

    Route::prefix('jabatan')->name('positions.')->group(function () {
        Route::post('/', [MasterDataController::class, 'storePosition'])->name('store');
        Route::put('/{position}', [MasterDataController::class, 'updatePosition'])->name('update');
        Route::delete('/{position}', [MasterDataController::class, 'destroyPosition'])->name('destroy');
    });

    // 3. Modul Konfigurasi Koperasi
    Route::prefix('pengaturan')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::post('/', [SettingController::class, 'update'])->name('update');
    });

    // 4. Modul Rekam Jejak Aktivitas (Audit Log)
    Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-logs.index');
});
