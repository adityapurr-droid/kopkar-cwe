@extends('layouts.staff')

@section('title', 'Laporan & Rekapitulasi')
@section('page_title', 'Rekapitulasi & Laporan Keuangan')
@section('page_description', 'Laporan saldo simpanan anggota, pinjaman aktif, dan mutasi arus kas koperasi')

@section('content')
<div x-data="{ tab: '{{ $tab }}' }" class="space-y-6">

    {{-- Tabs Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 bg-white rounded-3xl p-2 border border-slate-100 shadow-sm">
        <div class="flex items-center gap-2">
            <button type="button" @click="tab = 'simpanan'"
                    :class="tab === 'simpanan' ? 'bg-emerald-800 text-white font-bold shadow' : 'text-slate-600 hover:bg-slate-100 font-semibold'"
                    class="py-2.5 px-5 rounded-2xl text-xs transition">
                1. Saldo Simpanan Anggota
            </button>
            <button type="button" @click="tab = 'pinjaman'"
                    :class="tab === 'pinjaman' ? 'bg-emerald-800 text-white font-bold shadow' : 'text-slate-600 hover:bg-slate-100 font-semibold'"
                    class="py-2.5 px-5 rounded-2xl text-xs transition">
                2. Pinjaman Aktif & Kolektibilitas
            </button>
            <button type="button" @click="tab = 'kas'"
                    :class="tab === 'kas' ? 'bg-emerald-800 text-white font-bold shadow' : 'text-slate-600 hover:bg-slate-100 font-semibold'"
                    class="py-2.5 px-5 rounded-2xl text-xs transition">
                3. Mutasi Arus Kas Simpanan
            </button>
        </div>

        <div class="flex items-center gap-2 pr-2">
            <a :href="tab === 'pinjaman' ? '{{ route('staff.reports.export-excel', ['type' => 'pinjaman']) }}' : '{{ route('staff.reports.export-excel', ['type' => 'simpanan']) }}'" 
               class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                <span>Ekspor Excel (.xlsx)</span>
            </a>
            <a :href="tab === 'pinjaman' ? '{{ route('staff.reports.export-pdf', ['type' => 'pinjaman']) }}' : '{{ route('staff.reports.export-pdf', ['type' => 'simpanan']) }}'" 
               target="_blank"
               class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                <span>Cetak PDF</span>
            </a>
        </div>
    </div>

    {{-- Tab 1: Simpanan --}}
    <div x-show="tab === 'simpanan'" class="space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Simpanan Pokok</span>
                <h3 class="text-xl font-black text-slate-900 mt-1">Rp {{ number_format($totalPokok, 0, ',', '.') }}</h3>
            </div>
            <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Simpanan Wajib</span>
                <h3 class="text-xl font-black text-emerald-700 mt-1">Rp {{ number_format($totalWajib, 0, ',', '.') }}</h3>
            </div>
            <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Simpanan Sukarela</span>
                <h3 class="text-xl font-black text-teal-700 mt-1">Rp {{ number_format($totalSukarela, 0, ',', '.') }}</h3>
            </div>
            <div class="bg-gradient-to-r from-emerald-800 to-teal-900 rounded-3xl p-5 text-white shadow-lg">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-200">Total Saldo Koperasi</span>
                <h3 class="text-xl font-black text-amber-300 mt-1">Rp {{ number_format($totalSaldoKoperasi, 0, ',', '.') }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4">No</th>
                            <th class="py-3 px-4">No. Anggota</th>
                            <th class="py-3 px-4">Nama Lengkap & NIK</th>
                            <th class="py-3 px-4">Divisi & Jabatan</th>
                            <th class="py-3 px-4 text-right">Pokok</th>
                            <th class="py-3 px-4 text-right">Wajib</th>
                            <th class="py-3 px-4 text-right">Sukarela</th>
                            <th class="py-3 px-4 text-right font-black text-slate-900">Total Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($members as $index => $m)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-4 text-slate-400 font-mono">{{ $index + 1 }}</td>
                                <td class="py-3 px-4 font-mono font-bold text-slate-900">{{ $m->no_anggota }}</td>
                                <td class="py-3 px-4">
                                    <span class="font-bold text-slate-900 block">{{ $m->nama_lengkap }}</span>
                                    <span class="text-[11px] text-slate-400">NIK: {{ $m->nik_karyawan }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="text-slate-800 block">{{ $m->division?->nama ?? '-' }}</span>
                                    <span class="text-[11px] text-emerald-700 font-semibold">{{ $m->position?->nama ?? '-' }}</span>
                                </td>
                                <td class="py-3 px-4 text-right">Rp {{ number_format($m->totalSimpananPokok(), 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right text-emerald-700 font-bold">Rp {{ number_format($m->totalSimpananWajib(), 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right text-teal-700 font-bold">Rp {{ number_format($m->totalSimpananSukarela(), 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right font-black text-slate-900 text-sm">
                                    Rp {{ number_format($m->totalSaldoSimpanan(), 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Tab 2: Pinjaman Aktif --}}
    <div x-show="tab === 'pinjaman'" style="display: none;" class="space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Rekening Pinjaman Aktif</span>
                <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $activeLoans->count() }} <span class="text-xs font-normal text-slate-500">berkas</span></h3>
            </div>
            <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Plafon Dicairkan</span>
                <h3 class="text-2xl font-black text-emerald-700 mt-1">Rp {{ number_format($totalPencairan, 0, ',', '.') }}</h3>
            </div>
            <div class="bg-gradient-to-r from-rose-900 to-amber-900 rounded-3xl p-5 text-white shadow-lg">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-200">Total Sisa Tagihan (Outstanding)</span>
                <h3 class="text-2xl font-black text-amber-300 mt-1">Rp {{ number_format($totalSisaHutang, 0, ',', '.') }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4">No. Pinjaman</th>
                            <th class="py-3 px-4">Peminjam</th>
                            <th class="py-3 px-4">Plafon Disetujui</th>
                            <th class="py-3 px-4">Tenor</th>
                            <th class="py-3 px-4">Angsuran / Bln</th>
                            <th class="py-3 px-4 font-black text-rose-600">Sisa Hutang</th>
                            <th class="py-3 px-4">Sisa Tenor</th>
                            <th class="py-3 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($activeLoans as $al)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-4 font-mono font-bold text-slate-900">{{ $al->no_pinjaman }}</td>
                                <td class="py-3 px-4">
                                    <span class="font-bold text-slate-900 block">{{ $al->member->nama_lengkap }}</span>
                                    <span class="text-[11px] text-slate-400">{{ $al->member->position?->nama ?? '-' }}</span>
                                </td>
                                <td class="py-3 px-4 font-bold text-slate-900">Rp {{ number_format((float) $al->nominal_disetujui, 0, ',', '.') }}</td>
                                <td class="py-3 px-4">{{ $al->tenor_bulan }} Bulan</td>
                                <td class="py-3 px-4 font-bold text-slate-800">Rp {{ number_format((float) $al->total_angsuran_perbulan, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 font-black text-rose-600 text-sm">Rp {{ number_format((float) $al->sisa_pinjaman, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 font-bold text-slate-700">{{ $al->sisa_tenor }} bln</td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-100 text-emerald-800">
                                        {{ $al->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-10 text-slate-400">Tidak ada pinjaman aktif saat ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Tab 3: Kas --}}
    <div x-show="tab === 'kas'" style="display: none;" class="space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Kas Masuk</span>
                <h3 class="text-2xl font-black text-emerald-700 mt-1">Rp {{ number_format($kasMasuk, 0, ',', '.') }}</h3>
            </div>
            <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Kas Keluar</span>
                <h3 class="text-2xl font-black text-rose-600 mt-1">Rp {{ number_format($kasKeluar, 0, ',', '.') }}</h3>
            </div>
            <div class="bg-gradient-to-r from-emerald-800 to-teal-900 rounded-3xl p-5 text-white shadow-lg">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-200">Saldo Akhir</span>
                <h3 class="text-2xl font-black text-amber-300 mt-1">Rp {{ number_format($kasMasuk - $kasKeluar, 0, ',', '.') }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Anggota</th>
                            <th class="py-3 px-4">Jenis Transaksi</th>
                            <th class="py-3 px-4">Keterangan</th>
                            <th class="py-3 px-4 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($cashflow as $cf)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-4 font-semibold text-slate-900">{{ $cf->tanggal_transaksi->format('d M Y') }}</td>
                                <td class="py-3 px-4 font-bold text-slate-900">{{ $cf->member->nama_lengkap }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $cf->tipe === 'masuk' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ $cf->tipe }} - Simpanan {{ $cf->jenis_simpanan }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-slate-600">{{ $cf->keterangan }}</td>
                                <td class="py-3 px-4 text-right font-black {{ $cf->tipe === 'masuk' ? 'text-emerald-700' : 'text-rose-600' }}">
                                    {{ $cf->tipe === 'masuk' ? '+' : '-' }} Rp {{ number_format((float) $cf->nominal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $cashflow->links() }}
            </div>
        </div>
    </div>

</div>
@endsection

