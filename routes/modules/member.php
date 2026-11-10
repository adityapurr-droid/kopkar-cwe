<?php

use App\Http\Controllers\MemberPortalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Modul Rute: Portal Anggota (Mobile Responsive)
|--------------------------------------------------------------------------
| Seluruh endpoint yang diakses oleh anggota koperasi (karyawan/dosen POLTEK CWE).
| Prefix: /anggota
| Name Prefix: member.
*/

Route::middleware(['auth', 'role:anggota'])->prefix('anggota')->name('member.')->group(function () {
    // Ringkasan Dashboard Saldo
    Route::get('/dashboard', [MemberPortalController::class, 'dashboard'])->name('dashboard');

    // Kalkulator Simulasi Pinjaman & Form Pengajuan
    Route::get('/simulasi', [MemberPortalController::class, 'simulation'])->name('simulation');
    Route::post('/pinjaman', [MemberPortalController::class, 'storeLoan'])->name('loan.store');
    Route::get('/pinjaman/{loan}', [MemberPortalController::class, 'showLoan'])->name('loan.show');

    // Riwayat Transaksi & Unduh Slip Potongan Gaji
    Route::get('/riwayat', [MemberPortalController::class, 'history'])->name('history');
    Route::get('/potongan-gaji/{deduction}/slip', [MemberPortalController::class, 'downloadDeductionSlip'])->name('deduction.slip');
});
