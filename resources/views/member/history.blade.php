@extends('layouts.member')

@section('title', 'Riwayat Transaksi & Potongan Gaji')

@section('content')
<div x-data="{ tab: 'simpanan' }" class="space-y-6">

    {{-- Tabs Header --}}
    <div class="flex flex-wrap items-center justify-between bg-white rounded-3xl p-2 border border-slate-100 shadow-sm gap-2">
        <button type="button" @click="tab = 'simpanan'"
                :class="tab === 'simpanan' ? 'bg-emerald-700 text-white shadow-sm font-bold' : 'text-slate-500 hover:text-slate-900 font-semibold'"
                class="flex-1 py-3 px-3 rounded-2xl text-xs transition text-center whitespace-nowrap">
            1. Mutasi Simpanan
        </button>
        <button type="button" @click="tab = 'payroll'"
                :class="tab === 'payroll' ? 'bg-emerald-700 text-white shadow-sm font-bold' : 'text-slate-500 hover:text-slate-900 font-semibold'"
                class="flex-1 py-3 px-3 rounded-2xl text-xs transition text-center whitespace-nowrap">
            2. Potongan Gaji Payroll
        </button>
        <button type="button" @click="tab = 'pinjaman'"
                :class="tab === 'pinjaman' ? 'bg-emerald-700 text-white shadow-sm font-bold' : 'text-slate-500 hover:text-slate-900 font-semibold'"
                class="flex-1 py-3 px-3 rounded-2xl text-xs transition text-center whitespace-nowrap">
            3. Pengajuan Pinjaman
        </button>
    </div>

    {{-- Tab 1: Simpanan --}}
    <div x-show="tab === 'simpanan'" class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Histori Mutasi Simpanan</h3>
                <p class="text-xs text-slate-400">Pencatatan simpanan pokok, simpanan wajib payroll, dan sukarela.</p>
            </div>
            <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full">
                Saldo: Rp {{ number_format($member->totalSaldoSimpanan(), 0, ',', '.') }}
            </span>
        </div>

        @if($savings->isEmpty())
            <div class="text-center py-10 text-slate-400 text-xs">
                Belum ada transaksi simpanan tercatat.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-3">Tanggal</th>
                            <th class="py-3 px-3">Jenis Simpanan</th>
                            <th class="py-3 px-3">Keterangan</th>
                            <th class="py-3 px-3">Metode</th>
                            <th class="py-3 px-3 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($savings as $s)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-3 font-semibold text-slate-900">
                                    {{ \Carbon\Carbon::parse($s->tanggal_transaksi)->format('d M Y') }}
                                </td>
                                <td class="py-3 px-3">
                                    <span class="inline-block px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider {{ $s->jenis_simpanan === 'pokok' ? 'bg-amber-100 text-amber-900' : ($s->jenis_simpanan === 'wajib' ? 'bg-emerald-100 text-emerald-900' : 'bg-teal-100 text-teal-900') }}">
                                        {{ $s->jenis_simpanan }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-slate-600">
                                    {{ $s->keterangan ?: '-' }}
                                </td>
                                <td class="py-3 px-3">
                                    {{ ucfirst(str_replace('_', ' ', $s->metode_bayar)) }}
                                </td>
                                <td class="py-3 px-3 text-right font-extrabold {{ $s->tipe === 'masuk' ? 'text-emerald-700' : 'text-rose-600' }}">
                                    {{ $s->tipe === 'masuk' ? '+' : '-' }} Rp {{ number_format((float) $s->nominal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $savings->links() }}
            </div>
        @endif
    </div>

    {{-- Tab 2: Potongan Payroll CWE --}}
    <div x-show="tab === 'payroll'" style="display: none;" class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Histori Potongan Gaji Bulanan (Payroll CWE)</h3>
                <p class="text-xs text-slate-400">Rekapitulasi potongan Simpanan Wajib rutin dan cicilan pinjaman dari slip gaji Anda.</p>
            </div>
        </div>

        @if($payrollDeductions->isEmpty())
            <div class="text-center py-10 text-slate-400 text-xs">
                Belum ada data potongan payroll yang tercatat untuk akun Anda.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-3">Periode Payroll</th>
                            <th class="py-3 px-3">Tanggal Cut-off</th>
                            <th class="py-3 px-3 text-right">Simpanan Wajib</th>
                            <th class="py-3 px-3 text-right">Angsuran Pinjaman</th>
                            <th class="py-3 px-3 text-right font-black text-slate-900">Total Potong</th>
                            <th class="py-3 px-3 text-center">Status</th>
                            <th class="py-3 px-3 text-right">Slip Bukti</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($payrollDeductions as $pd)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-3 font-bold text-slate-900">
                                    {{ $pd->payrollPeriod->nama_periode }}
                                </td>
                                <td class="py-3 px-3 text-slate-600">
                                    {{ $pd->payrollPeriod->tanggal_cutoff->format('d M Y') }}
                                </td>
                                <td class="py-3 px-3 text-right text-emerald-700 font-semibold">
                                    Rp {{ number_format((float) $pd->nominal_simpanan_wajib, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-right text-amber-700 font-semibold">
                                    Rp {{ number_format((float) $pd->nominal_angsuran_pinjaman, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-right font-black text-slate-900">
                                    Rp {{ number_format((float) $pd->total_potongan, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-center">
                                    @if($pd->status === 'lunas')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            ✓ Lunas Potong
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <a href="{{ route('member.deduction.slip', $pd) }}" target="_blank"
                                       class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 font-bold text-[11px] transition">
                                        📄 Slip PDF
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $payrollDeductions->links() }}
            </div>
        @endif
    </div>

    {{-- Tab 3: Pinjaman --}}
    <div x-show="tab === 'pinjaman'" style="display: none;" class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Histori Pengajuan Pinjaman</h3>
                <p class="text-xs text-slate-400">Seluruh pengajuan pinjaman yang pernah Anda kirimkan.</p>
            </div>
            <a href="{{ route('member.simulation') }}" class="text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-xl transition">
                Ajukan Baru
            </a>
        </div>

        @if($loans->isEmpty())
            <div class="text-center py-10 text-slate-400 text-xs">
                Belum pernah mengajukan pinjaman.
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($loans as $l)
                    <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50 p-3 rounded-2xl transition">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-extrabold text-sm text-slate-900">{{ $l->no_pinjaman }}</span>
                                @php
                                    $statusStyles = [
                                        'pending' => 'bg-amber-100 text-amber-800',
                                        'disetujui' => 'bg-sky-100 text-sky-800',
                                        'berjalan' => 'bg-emerald-100 text-emerald-800',
                                        'lunas' => 'bg-teal-100 text-teal-800',
                                        'ditolak' => 'bg-rose-100 text-rose-800',
                                    ];
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusStyles[$l->status] ?? 'bg-slate-100' }}">
                                    {{ $l->status }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">
                                Diajukan: {{ $l->created_at->format('d M Y') }} &bull; Tenor: {{ $l->tenor_bulan }} Bulan &bull; Bunga: {{ $l->bunga_persen }}%
                            </p>
                            <p class="text-xs text-slate-600 mt-0.5">Keperluan: {{ $l->keperluan }}</p>
                        </div>

                        <div class="flex items-center justify-between sm:justify-end gap-4">
                            <div class="text-right">
                                <span class="text-[10px] text-slate-400 block">Plafon Disetujui</span>
                                <span class="text-sm font-extrabold text-emerald-700">Rp {{ number_format((float) $l->nominal_disetujui, 0, ',', '.') }}</span>
                            </div>
                            <a href="{{ route('member.loan.show', $l) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition">
                                Rincian &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $loans->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
