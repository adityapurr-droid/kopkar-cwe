<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\LoanInstallment;
use App\Models\Member;
use Carbon\Carbon;

/**
 * Modul Layanan Bisnis: Pinjaman Koperasi
 * Mengatur formula kalkulasi bunga flat, jadwal cicilan amortisasi, dan validasi plafon.
 */
class LoanService
{
    /**
     * Hitung kalkulasi pinjaman dengan skema bunga Flat per bulan.
     *
     * Formula:
     * - Angsuran Pokok = Pokok / Tenor
     * - Angsuran Bunga = Pokok * (Bunga% / 100)
     * - Total Angsuran per Bulan = Angsuran Pokok + Angsuran Bunga
     * - Total Pengembalian = Pokok + (Angsuran Bunga * Tenor)
     */
    public function calculateFlat(float $nominal, int $tenorBulan, float $bungaPersenPerBulan): array
    {
        $angsuranPokok = $tenorBulan > 0 ? round($nominal / $tenorBulan, 2) : 0;
        $angsuranBunga = round($nominal * ($bungaPersenPerBulan / 100), 2);
        $totalBunga = round($angsuranBunga * $tenorBulan, 2);
        $totalPinjaman = $nominal + $totalBunga;
        $totalAngsuranPerBulan = $angsuranPokok + $angsuranBunga;

        return [
            'nominal' => $nominal,
            'tenor_bulan' => $tenorBulan,
            'bunga_persen' => $bungaPersenPerBulan,
            'angsuran_pokok_perbulan' => $angsuranPokok,
            'angsuran_bunga_perbulan' => $angsuranBunga,
            'total_bunga' => $totalBunga,
            'total_pinjaman' => $totalPinjaman,
            'total_angsuran_perbulan' => $totalAngsuranPerBulan,
        ];
    }

    /**
     * Terbitkan jadwal angsuran bulanan ke tabel loan_installments.
     */
    public function generateInstallments(Loan $loan, Carbon $startDate): void
    {
        $loan->installments()->delete();

        $saldoPokok = (float) $loan->nominal_disetujui;
        $pokokPerBulan = (float) $loan->angsuran_pokok_perbulan;
        $bungaPerBulan = (float) $loan->angsuran_bunga_perbulan;
        $totalPerBulan = (float) $loan->total_angsuran_perbulan;

        for ($i = 1; $i <= $loan->tenor_bulan; $i++) {
            $jatuhTempo = (clone $startDate)->addMonthsNoOverflow($i);

            // Penyesuaian pada bulan terakhir agar desimal presisi
            if ($i === $loan->tenor_bulan) {
                $pokokBulanIni = $saldoPokok;
                $totalBulanIni = $pokokBulanIni + $bungaPerBulan;
                $saldoPokokAkhir = 0;
            } else {
                $pokokBulanIni = $pokokPerBulan;
                $totalBulanIni = $totalPerBulan;
                $saldoPokokAkhir = max(0, $saldoPokok - $pokokBulanIni);
            }

            LoanInstallment::create([
                'loan_id' => $loan->id,
                'angsuran_ke' => $i,
                'nominal_pokok' => $pokokBulanIni,
                'nominal_bunga' => $bungaPerBulan,
                'total_angsuran' => $totalBulanIni,
                'sisa_saldo_pokok' => $saldoPokokAkhir,
                'jatuh_tempo' => $jatuhTempo->toDateString(),
                'status_bayar' => 'belum',
            ]);

            $saldoPokok = $saldoPokokAkhir;
        }
    }

    /**
     * Cek apakah nominal permohonan masih dalam batas plafon jabatan pemohon.
     */
    public function isWithinPlafon(Member $member, float $nominal): bool
    {
        return $nominal <= $member->maxPlafonPinjaman();
    }
}
