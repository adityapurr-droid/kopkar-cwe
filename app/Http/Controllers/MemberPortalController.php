<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Loan;
use App\Models\PayrollDeduction;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MemberPortalController extends Controller
{
    public function dashboard(): View
    {
        $user = Auth::user();
        $member = $user->member;

        if (! $member) {
            abort(403, 'Profil anggota belum terhubung dengan akun ini.');
        }

        $totalPokok = $member->totalSimpananPokok();
        $totalWajib = $member->totalSimpananWajib();
        $totalSukarela = $member->totalSimpananSukarela();
        $totalSimpanan = $member->totalSaldoSimpanan();

        $activeLoan = $member->loans()
            ->whereIn('status', ['berjalan', 'disetujui'])
            ->latest()
            ->first();

        $pendingLoan = $member->loans()
            ->where('status', 'pending')
            ->latest()
            ->first();

        $recentSavings = $member->savings()
            ->latest('tanggal_transaksi')
            ->take(5)
            ->get();

        $recentInstallments = [];
        if ($activeLoan) {
            $recentInstallments = $activeLoan->installments()
                ->where('status_bayar', 'lunas')
                ->latest('tanggal_bayar')
                ->take(5)
                ->get();
        }

        return view('member.dashboard', compact(
            'member',
            'totalPokok',
            'totalWajib',
            'totalSukarela',
            'totalSimpanan',
            'activeLoan',
            'pendingLoan',
            'recentSavings',
            'recentInstallments'
        ));
    }

    public function simulation(): View
    {
        $member = Auth::user()->member;
        $bungaPersen = (float) Setting::get('bunga_pinjaman_persen', 1.0);
        $tenorMaksimal = (int) Setting::get('tenor_maksimal_bulan', 24);
        $maxPlafon = $member ? $member->maxPlafonPinjaman() : 10000000;

        return view('member.simulation', compact('bungaPersen', 'tenorMaksimal', 'maxPlafon', 'member'));
    }

    public function storeLoan(Request $request): RedirectResponse
    {
        $member = Auth::user()->member;
        if (! $member) {
            abort(403, 'Hanya anggota terdaftar yang dapat mengajukan pinjaman.');
        }

        $maxPlafon = $member->maxPlafonPinjaman();
        $maxTenor = (int) Setting::get('tenor_maksimal_bulan', 24);

        $request->validate([
            'nominal_pengajuan' => ['required', 'numeric', 'min:500000', "max:{$maxPlafon}"],
            'tenor_bulan' => ['required', 'integer', 'min:1', "max:{$maxTenor}"],
            'keperluan' => ['required', 'string', 'max:500'],
        ], [
            'nominal_pengajuan.max' => 'Nominal pengajuan melebihi batas plafon jabatan Anda (Maksimal: Rp '.number_format($maxPlafon, 0, ',', '.').').',
            'tenor_bulan.max' => "Tenor maksimal adalah {$maxTenor} bulan.",
        ]);

        // Cek jika sudah memiliki pinjaman aktif atau permohonan pending
        $hasExisting = $member->loans()
            ->whereIn('status', ['pending', 'berjalan', 'disetujui'])
            ->exists();

        if ($hasExisting) {
            return back()->with('error', 'Anda masih memiliki permohonan pinjaman yang sedang diproses atau pinjaman aktif yang belum lunas.');
        }

        $bungaPersen = (float) Setting::get('bunga_pinjaman_persen', 1.0);
        $nominal = (float) $request->nominal_pengajuan;
        $tenor = (int) $request->tenor_bulan;

        $calc = Loan::calculateFlat($nominal, $tenor, $bungaPersen);

        $noPinjaman = 'PINJ-'.Carbon::now()->format('Ym').'-'.str_pad((string) (Loan::count() + 1), 4, '0', STR_PAD_LEFT);

        $loan = Loan::create([
            'no_pinjaman' => $noPinjaman,
            'member_id' => $member->id,
            'nominal_pengajuan' => $nominal,
            'nominal_disetujui' => $nominal, // default usulan
            'tenor_bulan' => $tenor,
            'bunga_persen' => $bungaPersen,
            'total_bunga' => $calc['total_bunga'],
            'total_pinjaman' => $calc['total_pinjaman'],
            'angsuran_pokok_perbulan' => $calc['angsuran_pokok_perbulan'],
            'angsuran_bunga_perbulan' => $calc['angsuran_bunga_perbulan'],
            'total_angsuran_perbulan' => $calc['total_angsuran_perbulan'],
            'sisa_pinjaman' => $calc['total_pinjaman'],
            'sisa_tenor' => $tenor,
            'keperluan' => $request->keperluan,
            'status' => 'pending',
        ]);

        AuditLog::record(
            'Pinjaman',
            'CREATE',
            "Anggota {$member->nama_lengkap} mengajukan pinjaman baru sebesar Rp ".number_format($nominal, 0, ',', '.')." ({$noPinjaman})",
            null,
            $loan->toArray()
        );

        return redirect()->route('member.loan.show', $loan)
            ->with('success', 'Pengajuan pinjaman berhasil dikirim dan sedang menunggu verifikasi pengurus.');
    }

    public function showLoan(Loan $loan): View
    {
        $member = Auth::user()->member;
        if (! $member || $loan->member_id !== $member->id) {
            abort(403, 'Anda tidak memiliki hak untuk melihat pengajuan pinjaman ini.');
        }

        $loan->load(['installments', 'approver']);

        return view('member.loan-detail', compact('loan', 'member'));
    }

    public function history(): View
    {
        $member = Auth::user()->member;
        if (! $member) {
            abort(403, 'Profil anggota belum ditemukan.');
        }

        $savings = $member->savings()->latest('tanggal_transaksi')->paginate(15, ['*'], 'savings_page');
        $loans = $member->loans()->latest()->paginate(10, ['*'], 'loans_page');
        $payrollDeductions = $member->payrollDeductions()->with('payrollPeriod')->latest()->paginate(10, ['*'], 'payroll_page');

        return view('member.history', compact('member', 'savings', 'loans', 'payrollDeductions'));
    }

    public function downloadDeductionSlip(PayrollDeduction $deduction): Response
    {
        $member = Auth::user()->member;
        if (! $member || $deduction->member_id !== $member->id) {
            abort(403, 'Anda tidak berhak mengakses slip potongan ini.');
        }

        $deduction->load(['payrollPeriod', 'member.division', 'member.position']);

        $pdf = Pdf::loadView('exports.member-slip-pdf', compact('deduction', 'member'))
            ->setPaper('a5', 'landscape');

        return $pdf->stream('Slip-Potongan-Koperasi-'.$deduction->payrollPeriod->kode_periode.'-'.$member->no_anggota.'.pdf');
    }
}
