<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pinjaman Aktif Koperasi</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; color: #333; margin: 15px; }
        .header { text-align: center; border-bottom: 2px solid #1b4d3e; padding-bottom: 10px; margin-bottom: 15px; }
        .header h2 { margin: 0; font-size: 14px; color: #1b4d3e; text-transform: uppercase; }
        .header h3 { margin: 3px 0; font-size: 12px; color: #222; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 5px 6px; }
        th { background-color: #f2f5f3; color: #1b4d3e; font-weight: bold; text-align: left; font-size: 9px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background-color: #e8f0ec; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 60px; border: none; vertical-align: middle; text-align: left;">
                    <img src="{{ public_path('images/logo-cwe.png') }}" style="height: 48px; width: auto;">
                </td>
                <td style="border: none; text-align: center; vertical-align: middle;">
                    <h2 style="margin: 0; font-size: 13px; color: #1b4d3e; text-transform: uppercase;">Koperasi Karyawan Politeknik Kelapa Sawit Citra Widya Edukasi</h2>
                    <h3 style="margin: 3px 0; font-size: 11px; color: #222;">Laporan Pinjaman Berjalan & Kolektibilitas</h3>
                    <p style="margin: 0; font-size: 9px; color: #666;">Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }}</p>
                </td>
                <td style="width: 60px; border: none;"></td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 25px;">No</th>
                <th>No. Pinjaman</th>
                <th>Nama Peminjam</th>
                <th>Jabatan</th>
                <th class="text-right">Plafon Disetujui</th>
                <th>Tenor</th>
                <th class="text-right">Angsuran / Bln</th>
                <th class="text-right">Sisa Hutang</th>
                <th class="text-center">Sisa Tenor</th>
            </tr>
        </thead>
        <tbody>
            @php $tPlafon = 0; $tSisa = 0; @endphp
            @foreach($loans as $i => $l)
                @php
                    $tPlafon += (float) $l->nominal_disetujui;
                    $tSisa += (float) $l->sisa_pinjaman;
                @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $l->no_pinjaman }}</td>
                    <td><strong>{{ $l->member->nama_lengkap }}</strong></td>
                    <td>{{ $l->member->position?->nama ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format((float) $l->nominal_disetujui, 0, ',', '.') }}</td>
                    <td>{{ $l->tenor_bulan }} Bulan</td>
                    <td class="text-right">Rp {{ number_format((float) $l->total_angsuran_perbulan, 0, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: bold; color: #b91c1c;">Rp {{ number_format((float) $l->sisa_pinjaman, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $l->sisa_tenor }} bln</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-right">TOTAL PINJAMAN AKTIF:</td>
                <td class="text-right">Rp {{ number_format($tPlafon, 0, ',', '.') }}</td>
                <td></td>
                <td></td>
                <td class="text-right" style="color: #b91c1c;">Rp {{ number_format($tSisa, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

</body>
</html>

