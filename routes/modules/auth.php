<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Modul Rute: Autentikasi Pengguna
|--------------------------------------------------------------------------
| Menangani proses login dan logout untuk seluruh pengguna (Admin, Pengurus, Anggota).
*/

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
