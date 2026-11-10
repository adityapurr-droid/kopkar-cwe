<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Member;
use App\Models\PayrollDeduction;
use App\Models\PayrollPeriod;
use App\Models\Saving;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

/**
 * Modul Layanan Bisnis: Pemotongan Gaji (Payroll CWE)
 * Mengatur kompilasi simpanan wajib + cicilan pinjaman seluruh karyawan, serta sinkronisasi pelunasan.
 */
class PayrollService
{
    /**
     * Generate rekapitulasi pemotongan gaji untuk periode tertentu.
     */
    public function generatePeriod(string $kodePeriode, string $namaPeriode, string $tanggalCutoff, int $processedBy): PayrollPeriod
    {
        return DB::transaction(function () use ($kodePeriode, $namaPeriode, $tanggalCutoff, $processedBy) {
            $simpananWajibDefault = (float) Setting::get('simpanan_wajib_nominal', 50000);

            $period = PayrollPeriod::create([
                'kode_periode' => $kodePeriode,
                'nama_periode' => $namaPeriode,
                'tanggal_cutoff' => $tanggalCutoff,
                'status' => 'draft',
                'processed_by' => $processedBy,
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

            return $period;
        });
    }

    /**
     * Selesaikan pemotongan payroll dan sinkronkan pelunasan ke akun simpan pinjam anggota.
     */
    public function settlePeriod(PayrollPeriod $period, int $processedBy): void
    {
        DB::transaction(function () use ($period, $processedBy) {
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
                        'created_by' => $processedBy,
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
    }
}
