<?php

use App\Http\Controllers\MemberManagementController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\StaffLoanController;
use App\Http\Controllers\StaffSavingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Modul Rute: Pengurus Keuangan & Operasional Koperasi
|--------------------------------------------------------------------------
| Menangani manajemen anggota, simpanan, approval pinjaman, payroll CWE, dan laporan.
| Akses: Pengurus & Admin
| Prefix: /pengurus
| Name Prefix: staff.
*/

Route::middleware(['auth', 'role:pengurus,admin'])->prefix('pengurus')->name('staff.')->group(function () {
    // 1. Dashboard Operasional
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');

    // 2. Modul Data Anggota
    Route::prefix('anggota')->name('members.')->group(function () {
        Route::get('/', [MemberManagementController::class, 'index'])->name('index');
        Route::get('/tambah', [MemberManagementController::class, 'create'])->name('create');
        Route::post('/', [MemberManagementController::class, 'store'])->name('store');
        Route::get('/{member}', [MemberManagementController::class, 'show'])->name('show');
        Route::put('/{member}', [MemberManagementController::class, 'update'])->name('update');
    });

    // 3. Modul Simpanan
    Route::prefix('simpanan')->name('savings.')->group(function () {
        Route::get('/', [StaffSavingController::class, 'index'])->name('index');
        Route::post('/batch', [StaffSavingController::class, 'storeBatch'])->name('store.batch');
    });

    // 4. Modul Pinjaman & Approval
    Route::prefix('pinjaman')->name('loans.')->group(function () {
        Route::get('/', [StaffLoanController::class, 'index'])->name('index');
        Route::get('/{loan}', [StaffLoanController::class, 'show'])->name('show');
        Route::post('/{loan}/approve', [StaffLoanController::class, 'approve'])->name('approve');
        Route::post('/{loan}/reject', [StaffLoanController::class, 'reject'])->name('reject');
        Route::post('/{loan}/disburse', [StaffLoanController::class, 'disburse'])->name('disburse');
    });

    // 5. Modul Pemotongan Gaji (Payroll CWE)
    Route::prefix('payroll')->name('payroll.')->group(function () {
        Route::get('/', [PayrollController::class, 'index'])->name('index');
        Route::post('/generate', [PayrollController::class, 'generate'])->name('generate');
        Route::get('/{period}', [PayrollController::class, 'show'])->name('show');
        Route::post('/{period}/settle', [PayrollController::class, 'settle'])->name('settle');
        Route::get('/{period}/export-excel', [PayrollController::class, 'exportExcel'])->name('export-excel');
        Route::get('/{period}/export-pdf', [PayrollController::class, 'exportPdf'])->name('export-pdf');
    });

    // 6. Modul Laporan & Rekapitulasi Keuangan
    Route::prefix('laporan')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('export-excel');
        Route::get('/export-pdf', [ReportController::class, 'exportPdf'])->name('export-pdf');
    });
});
