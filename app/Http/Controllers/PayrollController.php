<?php

namespace App\Http\Controllers;

use App\Exports\PayrollPeriodExport;
use App\Models\AuditLog;
use App\Models\Loan;
use App\Models\Member;
use App\Models\PayrollDeduction;
use App\Models\PayrollPeriod;
use App\Models\Saving;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PayrollController extends Controller
{
    public function index(): View
    {
        $periods = PayrollPeriod::with('processor')->latest('kode_periode')->paginate(12);

        $simpananWajibNominal = (float) Setting::get('simpanan_wajib_nominal', 50000);
        $activeMembersCount = Member::where('status_keanggotaan', 'aktif')->count();
        $activeLoansCount = Loan::where('status', 'berjalan')->count();

        return view('staff.payroll.index', compact(
            'periods',
            'simpananWajibNominal',
            'activeMembersCount',
            'activeLoansCount'
        ));
    }

    public function generate(Request $request): RedirectResponse
    {
        $request->validate([
            'kode_periode' => ['required', 'string', 'regex:/^\d{4}-\d{2}$/', 'unique:payroll_periods,kode_periode'],
            'nama_periode' => ['required', 'string', 'max:100'],
            'tanggal_cutoff' => ['required', 'date'],
        ], [
            'kode_periode.regex' => 'Format kode periode harus YYYY-MM (contoh: '.now()->format('Y-m').').',
            'kode_periode.unique' => 'Periode payroll untuk bulan tersebut sudah pernah digenerate.',
        ]);

        DB::transaction(function () use ($request) {
            $simpananWajibDefault = (float) Setting::get('simpanan_wajib_nominal', 50000);

            $period = PayrollPeriod::create([
                'kode_periode' => $request->kode_periode,
                'nama_periode' => $request->nama_periode,
                'tanggal_cutoff' => $request->tanggal_cutoff,
                'status' => 'draft',
                'processed_by' => auth()->id(),
            ]);

            $activeMembers = Member::where('status_keanggotaan', 'aktif')->get();
            $totalWajib = 0;
            $totalAngsuran = 0;

            foreach ($activeMembers as $member) {
                $wajib = $simpananWajibDefault;
                $angsuran = 0;

                // Cari pinjaman aktif berjalan
                $activeLoan = $member->loans()
                    ->where('status', 'berjalan')
                    ->first();

                if ($activeLoan) {
                    // Cari cicilan yang belum lunas tertua
                    $unpaidInstallment = $activeLoan->installments()
                        ->where('status_bayar', 'belum')
                        ->orderBy('angsuran_ke')
                        ->first();

                    if ($unpaidInstallment) {
                        $angsuran = (float) $unpaidInstallment->total_angsuran;
                    }
                }

                $totalPotongan = $wajib + $angsuran;

                PayrollDeduction::create([
                    'payroll_period_id' => $period->id,
                    'member_id' => $member->id,
                    'nominal_simpanan_wajib' => $wajib,
                    'nominal_angsuran_pinjaman' => $angsuran,
                    'total_potongan' => $totalPotongan,
                    'status' => 'pending',
                ]);

                $totalWajib += $wajib;
                $totalAngsuran += $angsuran;
            }

            $period->update([
                'total_simpanan_wajib' => $totalWajib,
                'total_angsuran_pinjaman' => $totalAngsuran,
                'total_pemotongan' => $totalWajib + $totalAngsuran,
            ]);

            AuditLog::record(
                'Payroll',
                'CREATE',
                "Generate rekapitulasi pemotongan gaji periode {$period->nama_periode} ({$period->kode_periode}) total Rp ".number_format($period->total_pemotongan, 0, ',', '.'),
                null,
                $period->toArray()
            );
        });

        return redirect()->route('staff.payroll.show', $request->kode_periode)
            ->with('success', 'Rekap pemotongan payroll berhasil di-generate.');
    }

    public function show(string $kodePeriode): View
    {
        $period = PayrollPeriod::where('kode_periode', $kodePeriode)->firstOrFail();
        $deductions = $period->deductions()
            ->with(['member.division', 'member.position'])
            ->get();

        return view('staff.payroll.show', compact('period', 'deductions'));
    }

    public function settle(PayrollPeriod $period): RedirectResponse
    {
        if ($period->status === 'selesai') {
            return back()->with('error', 'Periode payroll ini sudah berstatus selesai/lunas.');
        }

        DB::transaction(function () use ($period) {
            $deductions = $period->deductions()->with('member')->get();

            foreach ($deductions as $deduction) {
                $member = $deduction->member;

                // 1. Simpanan Wajib Masuk
                if ((float) $deduction->nominal_simpanan_wajib > 0) {
                    Saving::create([
                        'member_id' => $member->id,
                        'jenis_simpanan' => 'wajib',
                        'tipe' => 'masuk',
                        'nominal' => $deduction->nominal_simpanan_wajib,
                        'tanggal_transaksi' => $period->tanggal_cutoff,
                        'metode_bayar' => 'potong_gaji',
                        'keterangan' => 'Simpanan Wajib Payroll Periode '.$period->nama_periode,
                        'created_by' => auth()->id(),
                    ]);
                }

                // 2. Angsuran Pinjaman
                if ((float) $deduction->nominal_angsuran_pinjaman > 0) {
                    $activeLoan = $member->loans()
                        ->where('status', 'berjalan')
                        ->first();

                    if ($activeLoan) {
                        $installment = $activeLoan->installments()
                            ->where('status_bayar', 'belum')
                            ->orderBy('angsuran_ke')
                            ->first();

                        if ($installment) {
                            $installment->update([
                                'status_bayar' => 'lunas',
                                'tanggal_bayar' => now(),
                            ]);

                            $newSisaPinjaman = max(0, (float) $activeLoan->sisa_pinjaman - (float) $installment->total_angsuran);
                            $newSisaTenor = max(0, (int) $activeLoan->sisa_tenor - 1);

                            $loanUpdate = [
                                'sisa_pinjaman' => $newSisaPinjaman,
                                'sisa_tenor' => $newSisaTenor,
                            ];

                            // Cek jika seluruh cicilan sudah lunas
                            $unpaidCount = $activeLoan->installments()
                                ->where('status_bayar', 'belum')
                                ->count();

                            if ($unpaidCount === 0 || $newSisaPinjaman <= 0) {
                                $loanUpdate['status'] = 'lunas';
                            }

                            $activeLoan->update($loanUpdate);
                        }
                    }
                }

                $deduction->update(['status' => 'lunas']);
            }

            $period->update([
                'status' => 'selesai',
                'processed_at' => now(),
            ]);

            AuditLog::record(
                'Payroll',
                'SETTLE',
                "Penyelesaian dan sinkronisasi pelunasan payroll periode {$period->nama_periode} ({$period->kode_periode})",
                null,
                $period->toArray()
            );
        });

        return back()->with('success', 'Pemotongan gaji periode '.$period->nama_periode.' berhasil diselesaikan dan disinkronkan ke saldo simpanan & pinjaman anggota!');
    }

    public function exportExcel(PayrollPeriod $period): BinaryFileResponse
    {
        $fileName = 'Rekap-Potongan-Gaji-CWE-'.$period->kode_periode.'.xlsx';

        return Excel::download(new PayrollPeriodExport($period), $fileName);
    }

    public function exportPdf(PayrollPeriod $period): Response
    {
        $deductions = $period->deductions()
            ->with(['member.division', 'member.position'])
            ->get();

        $pdf = Pdf::loadView('exports.payroll-pdf', compact('period', 'deductions'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Rekap-Potongan-Gaji-CWE-'.$period->kode_periode.'.pdf');
    }
}
