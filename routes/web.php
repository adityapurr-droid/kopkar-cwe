<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Koperasi Karyawan POLTEK CWE
|--------------------------------------------------------------------------
| File ini bertindak sebagai aggregator utama yang memuat modul-modul rute:
| 1. routes/modules/auth.php     -> Autentikasi Login & Logout
| 2. routes/modules/member.php   -> Portal Anggota (Mobile Responsive)
| 3. routes/modules/staff.php    -> Operasional Pengurus (Simpan Pinjam, Payroll, Laporan)
| 4. routes/modules/admin.php    -> Administrasi Sistem, Plafon Jabatan, Pengaturan
*/

// Redirect Halaman Utama
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();

        return $user->hasStaffAccess()
            ? redirect()->route('staff.dashboard')
            : redirect()->route('member.dashboard');
    }

    return redirect()->route('login');
});

// Load Rute Modular
require __DIR__.'/modules/auth.php';
require __DIR__.'/modules/member.php';
require __DIR__.'/modules/staff.php';
require __DIR__.'/modules/admin.php';
