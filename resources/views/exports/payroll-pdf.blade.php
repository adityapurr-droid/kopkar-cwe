<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Pemotongan Gaji - {{ $period->kode_periode }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
            margin: 15px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1b4d3e;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h2 {
            margin: 0;
            font-size: 14px;
            color: #1b4d3e;
            text-transform: uppercase;
        }
        .header h3 {
            margin: 3px 0;
            font-size: 12px;
            color: #222;
        }
        .header p {
            margin: 0;
            font-size: 9px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 5px 6px;
        }
        th {
            background-color: #f2f5f3;
            color: #1b4d3e;
            font-weight: bold;
            text-align: left;
            font-size: 9px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            background-color: #e8f0ec;
            font-weight: bold;
        }
        .signature-table {
            width: 100%;
            margin-top: 30px;
            border: none;
        }
        .signature-table td {
            border: none;
            width: 50%;
            text-align: center;
            padding: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <table style="width: 100%; border: none; margin-bottom: 5px;">
            <tr>
                <td style="width: 60px; border: none; vertical-align: middle; text-align: left;">
                    <img src="{{ public_path('images/logo-cwe.png') }}" style="height: 50px; width: auto;">
                </td>
                <td style="border: none; text-align: center; vertical-align: middle;">
                    <h2 style="margin: 0; font-size: 13px; color: #1b4d3e; text-transform: uppercase;">Koperasi Karyawan Politeknik Kelapa Sawit Citra Widya Edukasi</h2>
                    <h3 style="margin: 3px 0; font-size: 11px; color: #222;">Rekapitulasi Pemotongan Gaji Karyawan (Payroll CWE)</h3>
                    <p style="margin: 0; font-size: 9px; color: #666;">Periode: <strong>{{ $period->nama_periode }}</strong> &bull; Cut-Off: {{ $period->tanggal_cutoff->format('d/m/Y') }} &bull; Kode: {{ $period->kode_periode }}</p>
                </td>
                <td style="width: 60px; border: none;"></td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 25px;">No</th>
                <th style="width: 60px;">No. Anggota</th>
                <th style="width: 80px;">NIK CWE</th>
                <th>Nama Lengkap</th>
                <th>Divisi</th>
                <th>Jabatan</th>
                <th class="text-right" style="width: 85px;">Simpanan Wajib</th>
                <th class="text-right" style="width: 90px;">Angsuran Pinjaman</th>
                <th class="text-right" style="width: 95px;">Total Potongan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($deductions as $i => $d)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $d->member->no_anggota }}</td>
                    <td>{{ $d->member->nik_karyawan }}</td>
                    <td><strong>{{ $d->member->nama_lengkap }}</strong></td>
                    <td>{{ $d->member->division?->nama ?? '-' }}</td>
                    <td>{{ $d->member->position?->nama ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format((float) $d->nominal_simpanan_wajib, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format((float) $d->nominal_angsuran_pinjaman, 0, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: bold;">Rp {{ number_format((float) $d->total_potongan, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6" class="text-right">TOTAL PEMOTONGAN GAJI:</td>
                <td class="text-right">Rp {{ number_format((float) $period->total_simpanan_wajib, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format((float) $period->total_angsuran_pinjaman, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format((float) $period->total_pemotongan, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <table class="signature-table">
        <tr>
            <td>
                Bekasi, {{ now()->translatedFormat('d F Y') }}<br>
                Diserahkan oleh,<br>
                <strong>Pengurus Koperasi CWE</strong><br><br><br><br>
                ( ______________________ )
            </td>
            <td>
                Diterima & Disetujui oleh,<br>
                <strong>Bagian Keuangan / Payroll POLTEK CWE</strong><br><br><br><br>
                ( ______________________ )
            </td>
        </tr>
    </table>

</body>
</html>

