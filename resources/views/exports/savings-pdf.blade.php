<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Simpanan Anggota Koperasi</title>
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
                    <h3 style="margin: 3px 0; font-size: 11px; color: #222;">Laporan Rekapitulasi Saldo Simpanan Anggota</h3>
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
                <th>No. Anggota</th>
                <th>Nama Anggota</th>
                <th>Jabatan</th>
                <th class="text-right">Pokok</th>
                <th class="text-right">Wajib</th>
                <th class="text-right">Sukarela</th>
                <th class="text-right">Total Saldo</th>
            </tr>
        </thead>
        <tbody>
            @php $tPokok = 0; $tWajib = 0; $tSukarela = 0; $tTotal = 0; @endphp
            @foreach($members as $i => $m)
                @php
                    $p = $m->totalSimpananPokok();
                    $w = $m->totalSimpananWajib();
                    $s = $m->totalSimpananSukarela();
                    $tot = $p + $w + $s;
                    $tPokok += $p; $tWajib += $w; $tSukarela += $s; $tTotal += $tot;
                @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $m->no_anggota }}</td>
                    <td><strong>{{ $m->nama_lengkap }}</strong></td>
                    <td>{{ $m->position?->nama ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($p, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($w, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($s, 0, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: bold;">Rp {{ number_format($tot, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-right">TOTAL SELURUH ANGGOTA:</td>
                <td class="text-right">Rp {{ number_format($tPokok, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($tWajib, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($tSukarela, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($tTotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

</body>
</html>

