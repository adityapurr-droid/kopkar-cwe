<?php

namespace App\Exports;

use App\Models\Member;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SavingsSummaryExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles
{
    public function collection()
    {
        return Member::with(['division', 'position'])
            ->where('status_keanggotaan', 'aktif')
            ->get()
            ->map(function ($m, $index) {
                return [
                    'no' => $index + 1,
                    'no_anggota' => $m->no_anggota,
                    'nik' => $m->nik_karyawan,
                    'nama' => $m->nama_lengkap,
                    'divisi' => $m->division?->nama ?? '-',
                    'jabatan' => $m->position?->nama ?? '-',
                    'simpanan_pokok' => $m->totalSimpananPokok(),
                    'simpanan_wajib' => $m->totalSimpananWajib(),
                    'simpanan_sukarela' => $m->totalSimpananSukarela(),
                    'total_saldo' => $m->totalSaldoSimpanan(),
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
            'Simpanan Pokok (Rp)',
            'Simpanan Wajib (Rp)',
            'Simpanan Sukarela (Rp)',
            'Total Saldo Simpanan (Rp)',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
