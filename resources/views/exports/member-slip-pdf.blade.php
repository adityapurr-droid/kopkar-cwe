<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Slip Pemotongan Gaji Koperasi - {{ $deduction->payrollPeriod->kode_periode }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; color: #333; margin: 15px; }
        .box { border: 1.5px solid #1b4d3e; padding: 15px; border-radius: 8px; }
        .header { text-align: center; border-bottom: 1.5px solid #1b4d3e; padding-bottom: 8px; margin-bottom: 12px; }
        .header h2 { margin: 0; font-size: 13px; color: #1b4d3e; text-transform: uppercase; }
        .header p { margin: 2px 0 0; font-size: 9px; color: #666; }
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .meta-table td { padding: 3px 0; font-size: 9px; }
        .item-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .item-table th, .item-table td { border: 1px solid #ccc; padding: 6px 8px; font-size: 9px; }
        .item-table th { background-color: #f2f5f3; color: #1b4d3e; }
        .text-right { text-align: right; }
        .total-row { background-color: #e8f0ec; font-weight: bold; font-size: 10px; }
        .signature-table { width: 100%; margin-top: 20px; }
        .signature-table td { width: 50%; text-align: center; font-size: 9px; }
    </style>
</head>
<body>

    <div class="box">
        <div class="header">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="width: 50px; border: none; vertical-align: middle; text-align: left;">
                        <img src="{{ public_path('images/logo-cwe.png') }}" style="height: 42px; width: auto;">
                    </td>
                    <td style="border: none; text-align: center; vertical-align: middle;">
                        <h2 style="margin: 0; font-size: 12px; color: #1b4d3e; text-transform: uppercase;">Koperasi Karyawan Politeknik Kelapa Sawit Citra Widya Edukasi</h2>
                        <p style="margin: 2px 0 0; font-size: 9px; color: #666;">Bukti Pemotongan Gaji Payroll (Slip Potongan Koperasi)</p>
                    </td>
                    <td style="width: 50px; border: none;"></td>
                </tr>
            </table>
        </div>

        <table class="meta-table">
            <tr>
                <td style="width: 20%;"><strong>Periode Gaji:</strong></td>
                <td style="width: 30%;">{{ $deduction->payrollPeriod->nama_periode }}</td>
                <td style="width: 20%;"><strong>No. Anggota:</strong></td>
                <td style="width: 30%;">{{ $member->no_anggota }}</td>
            </tr>
            <tr>
                <td><strong>Nama Anggota:</strong></td>
                <td>{{ $member->nama_lengkap }}</td>
                <td><strong>NIK CWE:</strong></td>
                <td>{{ $member->nik_karyawan }}</td>
            </tr>
            <tr>
                <td><strong>Divisi:</strong></td>
                <td>{{ $member->division?->nama ?? '-' }}</td>
                <td><strong>Jabatan:</strong></td>
                <td>{{ $member->position?->nama ?? '-' }}</td>
            </tr>
        </table>

        <table class="item-table">
            <thead>
                <tr>
                    <th style="width: 35px; text-align: center;">No</th>
                    <th>Komponen Pemotongan Koperasi</th>
                    <th style="width: 140px; text-align: right;">Nominal (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td>
                        <strong>Simpanan Wajib Anggota</strong><br>
                        <span style="font-size: 8px; color: #666;">Iuran simpanan wajib bulanan otomatis menambah saldo tabungan</span>
                    </td>
                    <td class="text-right">Rp {{ number_format((float) $deduction->nominal_simpanan_wajib, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="text-align: center;">2</td>
                    <td>
                        <strong>Angsuran Pinjaman Koperasi</strong><br>
                        <span style="font-size: 8px; color: #666;">Pembayaran cicilan pokok + bunga pinjaman berjalan</span>
                    </td>
                    <td class="text-right">Rp {{ number_format((float) $deduction->nominal_angsuran_pinjaman, 0, ',', '.') }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="2" class="text-right">TOTAL POTONGAN GAJI:</td>
                    <td class="text-right" style="color: #1b4d3e;">Rp {{ number_format((float) $deduction->total_potongan, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <table class="signature-table">
            <tr>
                <td>
                    Status: <strong>{{ $deduction->status === 'lunas' ? 'LUNAS TERPOTONG DARI SLIP GAJI' : 'PENDING' }}</strong><br><br><br>
                    <strong>Pengurus Koperasi CWE</strong>
                </td>
                <td>
                    Bekasi, {{ $deduction->payrollPeriod->tanggal_cutoff->format('d/m/Y') }}<br><br><br>
                    <strong>Bagian Keuangan / Payroll POLTEK CWE</strong>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>

