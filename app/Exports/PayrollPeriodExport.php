<?php

namespace App\Exports;

use App\Models\PayrollPeriod;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PayrollPeriodExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles
{
    public function __construct(public PayrollPeriod $period) {}

    public function collection()
    {
        return $this->period->deductions()
            ->with(['member.division', 'member.position'])
            ->get()
            ->map(function ($d, $index) {
                return [
                    'no' => $index + 1,
                    'no_anggota' => $d->member->no_anggota,
                    'nik' => $d->member->nik_karyawan,
                    'nama' => $d->member->nama_lengkap,
                    'divisi' => $d->member->division?->nama ?? '-',
                    'jabatan' => $d->member->position?->nama ?? '-',
                    'simpanan_wajib' => (float) $d->nominal_simpanan_wajib,
                    'angsuran_pinjaman' => (float) $d->nominal_angsuran_pinjaman,
                    'total_potongan' => (float) $d->total_potongan,
                    'status' => strtoupper($d->status),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'No',
            'No Anggota',
            'NIK Karyawan',
            'Nama Lengkap',
            'Divisi',
            'Jabatan',
            'Simpanan Wajib (Rp)',
            'Angsuran Pinjaman (Rp)',
            'Total Potongan Payroll (Rp)',
            'Status',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
