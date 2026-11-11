<?php

namespace App\Exports;

use App\Models\Loan;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LoansSummaryExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles
{
    public function collection(): Enumerable
    {
        return Loan::with(['member.division', 'member.position'])
            ->whereIn('status', ['berjalan', 'disetujui'])
            ->get()
            ->map(function ($l, $index) {
                return [
                    'no' => $index + 1,
                    'no_pinjaman' => $l->no_pinjaman,
                    'no_anggota' => $l->member->no_anggota,
                    'nama' => $l->member->nama_lengkap,
                    'jabatan' => $l->member->position?->nama ?? '-',
                    'plafon_disetujui' => (float) $l->nominal_disetujui,
                    'tenor' => $l->tenor_bulan.' Bulan',
                    'bunga' => $l->bunga_persen.' % / Bln',
                    'angsuran_perbulan' => (float) $l->total_angsuran_perbulan,
                    'sisa_pinjaman' => (float) $l->sisa_pinjaman,
                    'sisa_tenor' => $l->sisa_tenor.' Bulan',
                    'status' => strtoupper($l->status),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'No',
            'No Pinjaman',
            'No Anggota',
            'Nama Lengkap',
            'Jabatan',
            'Plafon Disetujui (Rp)',
            'Tenor',
            'Bunga',
            'Angsuran / Bln (Rp)',
            'Sisa Hutang (Rp)',
            'Sisa Tenor',
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
