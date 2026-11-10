@extends('layouts.member')

@section('title', 'Dashboard Anggota')

@section('content')
<div class="space-y-6">

    {{-- Member Greeting Card --}}
    <div class="bg-gradient-to-r from-emerald-800 to-teal-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-emerald-950/20 relative overflow-hidden">
        <div class="absolute -right-8 -bottom-8 w-44 h-44 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-10">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-700/60 rounded-full text-xs font-semibold text-emerald-200 mb-2 border border-emerald-600/40">
                    <span>No. Anggota:</span>
                    <strong class="text-white">{{ $member->no_anggota }}</strong>
                </div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-white">{{ $member->nama_lengkap }}</h2>
                <p class="text-xs sm:text-sm text-emerald-200 mt-1">
                    NIK: {{ $member->nik_karyawan }} &bull; {{ $member->position?->nama ?? 'Karyawan' }} &bull; {{ $member->division?->nama ?? 'POLTEK CWE' }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('member.simulation') }}" 
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-2xl bg-amber-400 hover:bg-amber-300 text-emerald-950 text-xs sm:text-sm font-bold shadow-md shadow-amber-400/20 transition active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    <span>Ajukan Pinjaman</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Financial Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        
        {{-- Card 1: Total Simpanan --}}
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full">
                    Total Saldo
                </span>
            </div>
            <p class="text-xs text-slate-500 font-medium">Total Akumulasi Simpanan</p>
            <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">
                Rp {{ number_format($totalSimpanan, 0, ',', '.') }}
            </h3>

            {{-- Breakdown --}}
            <div class="mt-5 pt-4 border-t border-slate-100 grid grid-cols-3 gap-2 text-center">
                <div>
                    <span class="text-[10px] text-slate-400 block font-medium">Pokok</span>
                    <span class="text-xs font-bold text-slate-700">Rp {{ number_format($totalPokok, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 block font-medium">Wajib</span>
                    <span class="text-xs font-bold text-emerald-700">Rp {{ number_format($totalWajib, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 block font-medium">Sukarela</span>
                    <span class="text-xs font-bold text-teal-700">Rp {{ number_format($totalSukarela, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Card 2: Status Pinjaman / Plafon --}}
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                @if($activeLoan)
                    <span class="text-[11px] font-bold uppercase tracking-wider text-rose-700 bg-rose-50 px-2.5 py-1 rounded-full">
                        Pinjaman Berjalan
                    </span>
                @else
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full">
                        Bebas Hutang
                    </span>
                @endif
            </div>

            @if($activeLoan)
                <p class="text-xs text-slate-500 font-medium">Sisa Hutang Pinjaman</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-rose-600 mt-1">
                    Rp {{ number_format((float) $activeLoan->sisa_pinjaman, 0, ',', '.') }}
                </h3>

                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                    <div>
                        <span class="text-[10px] text-slate-400 block font-medium">Cicilan Bulanan</span>
                        <span class="font-bold text-slate-800">Rp {{ number_format((float) $activeLoan->total_angsuran_perbulan, 0, ',', '.') }} / bln</span>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-slate-400 block font-medium">Sisa Tenor</span>
                        <span class="font-bold text-slate-800">{{ $activeLoan->sisa_tenor }} dari {{ $activeLoan->tenor_bulan }} Bulan</span>
                    </div>
                </div>
            @else
                <p class="text-xs text-slate-500 font-medium">Batas Plafon Pinjaman Jabatan</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-800 mt-1">
                    Rp {{ number_format($member->maxPlafonPinjaman(), 0, ',', '.') }}
                </h3>
                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span>Tidak ada cicilan pinjaman berjalan</span>
                    <a href="{{ route('member.simulation') }}" class="text-emerald-700 font-bold hover:underline">Simulasi &rarr;</a>
                </div>
            @endif
        </div>

    </div>

    {{-- Pending Loan Notification (If Any) --}}
    @if($pendingLoan)
        <div class="bg-amber-50 border border-amber-200 rounded-3xl p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-amber-900">Pengajuan Pinjaman Sedang Diproses</h4>
                    <p class="text-xs text-amber-800 mt-0.5">
                        No. {{ $pendingLoan->no_pinjaman }} sebesar <strong>Rp {{ number_format((float) $pendingLoan->nominal_pengajuan, 0, ',', '.') }}</strong> (Tenor: {{ $pendingLoan->tenor_bulan }} bln) sedang ditinjau oleh Pengurus Keuangan Koperasi.
                    </p>
                </div>
            </div>
            <a href="{{ route('member.loan.show', $pendingLoan) }}" class="inline-flex items-center justify-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold transition flex-shrink-0">
                Lihat Detail Status
            </a>
        </div>
    @endif

    {{-- Active Loan Details (Progress Bar) --}}
    @if($activeLoan)
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="font-bold text-sm text-slate-800">Detail Pinjaman Aktif</h4>
                    <p class="text-xs text-slate-400">Nomor: {{ $activeLoan->no_pinjaman }}</p>
                </div>
                <a href="{{ route('member.loan.show', $activeLoan) }}" class="text-xs font-semibold text-emerald-700 hover:underline">
                    Jadwal Angsuran &rarr;
                </a>
            </div>

            @php
                $totalPinjaman = (float) $activeLoan->total_pinjaman;
                $sisaPinjaman = (float) $activeLoan->sisa_pinjaman;
                $terbayar = max(0, $totalPinjaman - $sisaPinjaman);
                $persenLunas = $totalPinjaman > 0 ? round(($terbayar / $totalPinjaman) * 100) : 0;
            @endphp

            <div class="space-y-2">
                <div class="flex justify-between text-xs font-semibold">
                    <span class="text-slate-600">Progress Pelunasan</span>
                    <span class="text-emerald-700">{{ $persenLunas }}% Lunas</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                    <div class="bg-emerald-600 h-3 rounded-full transition-all duration-500" style="width: {{ $persenLunas }}%"></div>
                </div>
                <div class="flex justify-between text-[11px] text-slate-400 pt-1">
                    <span>Terbayar: Rp {{ number_format($terbayar, 0, ',', '.') }}</span>
                    <span>Total Pinjaman: Rp {{ number_format($totalPinjaman, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    @endif

    {{-- Recent Savings Transactions --}}
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-bold text-sm text-slate-800">Mutasi Simpanan Terakhir</h4>
            <a href="{{ route('member.history') }}" class="text-xs font-semibold text-emerald-700 hover:underline">
                Lihat Semua &rarr;
            </a>
        </div>

        @if($recentSavings->isEmpty())
            <p class="text-xs text-slate-400 py-4 text-center">Belum ada riwayat transaksi simpanan.</p>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($recentSavings as $item)
                    <div class="py-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-bold {{ $item->tipe === 'masuk' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                {{ $item->tipe === 'masuk' ? '↓' : '↑' }}
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-800">
                                    Simpanan {{ ucfirst($item->jenis_simpanan) }}
                                </p>
                                <p class="text-[11px] text-slate-400">
                                    {{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d M Y') }} &bull; {{ ucfirst(str_replace('_', ' ', $item->metode_bayar)) }}
                                </p>
                            </div>
                        </div>
                        <span class="text-xs font-extrabold {{ $item->tipe === 'masuk' ? 'text-emerald-700' : 'text-rose-600' }}">
                            {{ $item->tipe === 'masuk' ? '+' : '-' }} Rp {{ number_format((float) $item->nominal, 0, ',', '.') }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection

