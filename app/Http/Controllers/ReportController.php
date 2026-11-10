<?php

namespace App\Http\Controllers;

use App\Exports\LoansSummaryExport;
use App\Exports\SavingsSummaryExport;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Saving;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'simpanan');

        // Data Simpanan Anggota
        $members = Member::with(['division', 'position'])
            ->where('status_keanggotaan', 'aktif')
            ->orderBy('nama_lengkap')
            ->get();

        $totalPokok = 0;
        $totalWajib = 0;
        $totalSukarela = 0;

        foreach ($members as $m) {
            $totalPokok += $m->totalSimpananPokok();
            $totalWajib += $m->totalSimpananWajib();
            $totalSukarela += $m->totalSimpananSukarela();
        }

        $totalSaldoKoperasi = $totalPokok + $totalWajib + $totalSukarela;

        // Data Pinjaman Aktif
        $activeLoans = Loan::with(['member.division', 'member.position'])
            ->whereIn('status', ['berjalan', 'disetujui'])
            ->latest()
            ->get();

        $totalPencairan = (float) $activeLoans->sum('nominal_disetujui');
        $totalSisaHutang = (float) $activeLoans->sum('sisa_pinjaman');

        // Data Transaksi Kas (Mutasi Simpanan)
        $queryKas = Saving::with('member');
        if ($request->filled('tanggal_dari')) {
            $queryKas->whereDate('tanggal_transaksi', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $queryKas->whereDate('tanggal_transaksi', '<=', $request->tanggal_sampai);
        }
        $cashflow = $queryKas->latest('tanggal_transaksi')->paginate(20, ['*'], 'kas_page')->withQueryString();

        $kasMasuk = (float) Saving::where('tipe', 'masuk')->sum('nominal');
        $kasKeluar = (float) Saving::where('tipe', 'keluar')->sum('nominal');

        return view('staff.reports.index', compact(
            'tab',
            'members',
            'totalPokok',
            'totalWajib',
            'totalSukarela',
            'totalSaldoKoperasi',
            'activeLoans',
            'totalPencairan',
            'totalSisaHutang',
            'cashflow',
            'kasMasuk',
            'kasKeluar'
        ));
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $type = $request->query('type', 'simpanan');

        if ($type === 'pinjaman') {
            return Excel::download(new LoansSummaryExport, 'Laporan-Pinjaman-Aktif-CWE.xlsx');
        }

        return Excel::download(new SavingsSummaryExport, 'Laporan-Simpanan-Anggota-CWE.xlsx');
    }

    public function exportPdf(Request $request): Response
    {
        $type = $request->query('type', 'simpanan');

        if ($type === 'pinjaman') {
            $loans = Loan::with(['member.division', 'member.position'])
                ->whereIn('status', ['berjalan', 'disetujui'])
                ->get();

            $pdf = Pdf::loadView('exports.loans-pdf', compact('loans'))
                ->setPaper('a4', 'landscape');

            return $pdf->stream('Laporan-Pinjaman-Aktif-CWE.pdf');
        }

        $members = Member::with(['division', 'position'])
            ->where('status_keanggotaan', 'aktif')
            ->get();

        $pdf = Pdf::loadView('exports.savings-pdf', compact('members'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan-Simpanan-Anggota-CWE.pdf');
    }
}
