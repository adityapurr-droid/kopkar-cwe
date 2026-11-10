<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Member;
use App\Models\Saving;
use Illuminate\View\View;

class StaffDashboardController extends Controller
{
    public function index(): View
    {
        $totalAnggota = Member::where('status_keanggotaan', 'aktif')->count();

        $totalSimpananMasuk = (float) Saving::where('tipe', 'masuk')->sum('nominal');
        $totalSimpananKeluar = (float) Saving::where('tipe', 'keluar')->sum('nominal');
        $saldoKasSimpanan = $totalSimpananMasuk - $totalSimpananKeluar;

        $totalPinjamanAktif = (float) Loan::whereIn('status', ['berjalan', 'disetujui'])->sum('sisa_pinjaman');
        $countPendingLoans = Loan::where('status', 'pending')->count();

        $pendingLoans = Loan::with(['member.division', 'member.position'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $recentSavings = Saving::with('member')
            ->latest('tanggal_transaksi')
            ->take(6)
            ->get();

        $simpananPerJenis = [
            'pokok' => (float) Saving::where('jenis_simpanan', 'pokok')->where('tipe', 'masuk')->sum('nominal'),
            'wajib' => (float) Saving::where('jenis_simpanan', 'wajib')->where('tipe', 'masuk')->sum('nominal'),
            'sukarela' => (float) Saving::where('jenis_simpanan', 'sukarela')->where('tipe', 'masuk')->sum('nominal') - (float) Saving::where('jenis_simpanan', 'sukarela')->where('tipe', 'keluar')->sum('nominal'),
        ];

        return view('staff.dashboard', compact(
            'totalAnggota',
            'saldoKasSimpanan',
            'totalPinjamanAktif',
            'countPendingLoans',
            'pendingLoans',
            'recentSavings',
            'simpananPerJenis'
        ));
    }
}
