@extends('layouts.staff')

@section('title', 'Dashboard Back-Office')
@section('page_title', 'Dashboard Koperasi')
@section('page_description', 'Ikhtisar operasional, status simpan pinjam, dan antrean verifikasi')

@section('content')
<div class="space-y-6">

    {{-- Top Metric Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- Card 1: Anggota --}}
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Anggota Aktif</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $totalAnggota }} <span class="text-xs font-normal text-slate-500">orang</span></h3>
                <a href="{{ route('staff.members.index') }}" class="text-[11px] font-semibold text-emerald-700 hover:underline mt-2 inline-block">Lihat Semua Data &rarr;</a>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
        </div>

        {{-- Card 2: Total Kas Simpanan --}}
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Simpanan</p>
                <h3 class="text-2xl font-black text-emerald-700 mt-1">Rp {{ number_format($saldoKasSimpanan, 0, ',', '.') }}</h3>
                <a href="{{ route('staff.savings.index') }}" class="text-[11px] font-semibold text-emerald-700 hover:underline mt-2 inline-block">Kelola Simpanan &rarr;</a>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>

        {{-- Card 3: Pinjaman Aktif --}}
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Pinjaman Berjalan</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">Rp {{ number_format($totalPinjamanAktif, 0, ',', '.') }}</h3>
                <a href="{{ route('staff.loans.index') }}" class="text-[11px] font-semibold text-emerald-700 hover:underline mt-2 inline-block">Daftar Pinjaman &rarr;</a>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        {{-- Card 4: Antrean Approval --}}
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Permohonan Pending</p>
                <h3 class="text-2xl font-black text-amber-600 mt-1">{{ $countPendingLoans }} <span class="text-xs font-normal text-slate-500">berkas</span></h3>
                <a href="{{ route('staff.loans.index', ['status' => 'pending']) }}" class="text-[11px] font-semibold text-amber-700 hover:underline mt-2 inline-block">Review Sekarang &rarr;</a>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            </div>
        </div>

    </div>

    {{-- Quick Actions --}}
    <div class="bg-gradient-to-r from-emerald-900 to-teal-900 rounded-3xl p-6 text-white shadow-lg flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="font-bold text-base">Aksi Cepat Pengurus & Admin</h3>
            <p class="text-xs text-emerald-200 mt-0.5">Kelola transaksi harian dan pemotongan gaji secara terintegrasi</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('staff.members.create') }}" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-600 rounded-xl text-xs font-bold transition">
                Anggota Baru
            </a>
            <a href="{{ route('staff.savings.index') }}" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-600 rounded-xl text-xs font-bold transition">
                Input Simpanan
            </a>
            <a href="{{ route('staff.payroll.index') }}" class="px-4 py-2 bg-amber-400 hover:bg-amber-300 text-emerald-950 rounded-xl text-xs font-extrabold transition shadow">
                ⚡ Generate Payroll CWE
            </a>
            <a href="{{ route('staff.reports.index') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 rounded-xl text-xs font-bold transition">
                📄 Cetak Laporan
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        {{-- Pending Loans Approval List --}}
        <div class="lg:col-span-7 bg-white rounded-3xl p-6 border border-slate-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-sm text-slate-800">Antrean Verifikasi Pinjaman</h3>
                    <p class="text-xs text-slate-400">Pengajuan pinjaman baru dari anggota POLTEK CWE</p>
                </div>
                <a href="{{ route('staff.loans.index', ['status' => 'pending']) }}" class="text-xs font-bold text-emerald-700 hover:underline">
                    Semua ({{ $countPendingLoans }}) &rarr;
                </a>
            </div>

            @if($pendingLoans->isEmpty())
                <div class="text-center py-10 text-slate-400 text-xs">
                    <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Tidak ada antrean pengajuan pinjaman pending saat ini.
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($pendingLoans as $loan)
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-extrabold text-slate-900">{{ $loan->member->nama_lengkap }}</p>
                                <p class="text-[11px] text-slate-400">
                                    {{ $loan->no_pinjaman }} &bull; {{ $loan->member->position?->nama ?? 'Karyawan' }} &bull; Tenor {{ $loan->tenor_bulan }} bln
                                </p>
                                <p class="text-xs font-bold text-emerald-700 mt-0.5">
                                    Rp {{ number_format((float) $loan->nominal_pengajuan, 0, ',', '.') }}
                                </p>
                            </div>

                            <a href="{{ route('staff.loans.show', $loan) }}" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 rounded-xl text-xs font-bold transition">
                                Review &rarr;
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Simpanan Breakdown & Recent Transactions --}}
        <div class="lg:col-span-5 bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-6">
            <div>
                <h3 class="font-bold text-sm text-slate-800 mb-3">Distribusi Simpanan Koperasi</h3>
                <div class="space-y-3 text-xs">
                    <div class="p-3 rounded-2xl bg-slate-50 flex items-center justify-between">
                        <span class="font-semibold text-slate-600">Simpanan Pokok</span>
                        <span class="font-black text-slate-900">Rp {{ number_format($simpananPerJenis['pokok'], 0, ',', '.') }}</span>
                    </div>
                    <div class="p-3 rounded-2xl bg-emerald-50/60 flex items-center justify-between">
                        <span class="font-semibold text-emerald-900">Simpanan Wajib (Payroll)</span>
                        <span class="font-black text-emerald-800">Rp {{ number_format($simpananPerJenis['wajib'], 0, ',', '.') }}</span>
                    </div>
                    <div class="p-3 rounded-2xl bg-teal-50/60 flex items-center justify-between">
                        <span class="font-semibold text-teal-900">Simpanan Sukarela</span>
                        <span class="font-black text-teal-800">Rp {{ number_format($simpananPerJenis['sukarela'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="font-bold text-sm text-slate-800 mb-2">Mutasi Masuk Terakhir</h3>
                <div class="divide-y divide-slate-100">
                    @foreach($recentSavings as $s)
                        <div class="py-2.5 flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-slate-800 block">{{ $s->member->nama_lengkap }}</span>
                                <span class="text-[10px] text-slate-400">Simpanan {{ ucfirst($s->jenis_simpanan) }} &bull; {{ $s->tanggal_transaksi->format('d M Y') }}</span>
                            </div>
                            <span class="font-extrabold text-emerald-700">
                                + Rp {{ number_format((float) $s->nominal, 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

