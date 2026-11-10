<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_pinjaman',
        'member_id',
        'nominal_pengajuan',
        'nominal_disetujui',
        'tenor_bulan',
        'bunga_persen',
        'total_bunga',
        'total_pinjaman',
        'angsuran_pokok_perbulan',
        'angsuran_bunga_perbulan',
        'total_angsuran_perbulan',
        'sisa_pinjaman',
        'sisa_tenor',
        'keperluan',
        'status',
        'catatan_pengurus',
        'approved_by',
        'approved_at',
        'disbursed_at',
    ];

    protected function casts(): array
    {
        return [
            'nominal_pengajuan' => 'decimal:2',
            'nominal_disetujui' => 'decimal:2',
            'bunga_persen' => 'decimal:2',
            'total_bunga' => 'decimal:2',
            'total_pinjaman' => 'decimal:2',
            'angsuran_pokok_perbulan' => 'decimal:2',
            'angsuran_bunga_perbulan' => 'decimal:2',
            'total_angsuran_perbulan' => 'decimal:2',
            'sisa_pinjaman' => 'decimal:2',
            'approved_at' => 'datetime',
            'disbursed_at' => 'datetime',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function installments(): HasMany
    {
        return $this->hasMany(LoanInstallment::class);
    }

    /**
     * Hitung kalkulasi pinjaman dengan skema bunga Flat per bulan.
     */
    public static function calculateFlat(float $nominal, int $tenorBulan, float $bungaPersenPerBulan): array
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
     * Generate jadwal amortisasi angsuran ke tabel loan_installments.
     */
    public function generateInstallments(Carbon $startDate): void
    {
        $this->installments()->delete();

        $saldoPokok = (float) $this->nominal_disetujui;
        $pokokPerBulan = (float) $this->angsuran_pokok_perbulan;
        $bungaPerBulan = (float) $this->angsuran_bunga_perbulan;
        $totalPerBulan = (float) $this->total_angsuran_perbulan;

        for ($i = 1; $i <= $this->tenor_bulan; $i++) {
            $jatuhTempo = (clone $startDate)->addMonthsNoOverflow($i);

            // Penyesuaian pada bulan terakhir jika ada pembulatan sisa desimal
            if ($i === $this->tenor_bulan) {
                $pokokBulanIni = $saldoPokok;
                $totalBulanIni = $pokokBulanIni + $bungaPerBulan;
                $saldoPokokAkhir = 0;
            } else {
                $pokokBulanIni = $pokokPerBulan;
                $totalBulanIni = $totalPerBulan;
                $saldoPokokAkhir = max(0, $saldoPokok - $pokokBulanIni);
            }

            LoanInstallment::create([
                'loan_id' => $this->id,
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
}
