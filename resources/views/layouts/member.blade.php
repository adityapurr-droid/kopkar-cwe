@extends('layouts.app')

@section('body')
<div class="min-h-screen bg-slate-50 flex flex-col pb-20 md:pb-8">

    {{-- Header --}}
    <header class="bg-emerald-900 text-white sticky top-0 z-30 shadow-md">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white p-1 flex items-center justify-center shadow-sm flex-shrink-0">
                    <img src="{{ asset('images/logo-cwe.png') }}" alt="Logo CWE" class="h-8 w-auto object-contain">
                </div>
                <div>
                    <h1 class="font-extrabold text-sm tracking-wide text-white leading-tight">KOPKAR POLTEK CWE</h1>
                    <p class="text-[11px] text-emerald-200">Portal Anggota Karyawan</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if(Auth::user()->hasStaffAccess())
                    <a href="{{ route('staff.dashboard') }}" class="hidden sm:inline-flex text-xs bg-emerald-800 hover:bg-emerald-700 text-emerald-100 font-semibold px-3 py-1.5 rounded-lg transition items-center gap-1">
                        <span>Panel Pengurus</span> &rarr;
                    </a>
                @endif
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Keluar" class="p-2 text-emerald-200 hover:text-white rounded-lg hover:bg-emerald-800 transition flex items-center gap-1.5 text-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        <span class="hidden sm:inline">Keluar</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- Desktop Navigation Tabs --}}
        <div class="hidden md:block bg-emerald-950/70 border-t border-emerald-800/60">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 flex gap-6">
                <a href="{{ route('member.dashboard') }}" 
                   class="py-3 text-xs font-semibold border-b-2 transition {{ request()->routeIs('member.dashboard') ? 'border-amber-400 text-amber-300' : 'border-transparent text-emerald-200 hover:text-white' }}">
                    Dashboard Saldo
                </a>
                <a href="{{ route('member.simulation') }}" 
                   class="py-3 text-xs font-semibold border-b-2 transition {{ request()->routeIs('member.simulation') ? 'border-amber-400 text-amber-300' : 'border-transparent text-emerald-200 hover:text-white' }}">
                    Simulasi & Pengajuan Pinjaman
                </a>
                <a href="{{ route('member.history') }}" 
                   class="py-3 text-xs font-semibold border-b-2 transition {{ request()->routeIs('member.history') ? 'border-amber-400 text-amber-300' : 'border-transparent text-emerald-200 hover:text-white' }}">
                    Riwayat Transaksi & Potongan
                </a>
            </div>
        </div>
    </header>

    {{-- Main Container --}}
    <main class="flex-1 max-w-4xl w-full mx-auto px-4 sm:px-6 py-6">
        @yield('content')
    </main>

    {{-- Mobile Bottom Navigation Bar --}}
    <nav class="md:hidden fixed bottom-0 inset-x-0 bg-white border-t border-slate-200 z-40 px-6 py-2 flex items-center justify-around shadow-lg">
        <a href="{{ route('member.dashboard') }}" 
           class="flex flex-col items-center gap-1 text-[11px] font-semibold transition {{ request()->routeIs('member.dashboard') ? 'text-emerald-700' : 'text-slate-400 hover:text-slate-600' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span>Ringkasan</span>
        </a>

        <a href="{{ route('member.simulation') }}" 
           class="flex flex-col items-center gap-1 text-[11px] font-semibold transition {{ request()->routeIs('member.simulation') ? 'text-emerald-700' : 'text-slate-400 hover:text-slate-600' }}">
            <div class="relative">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            </div>
            <span>Simulasi</span>
        </a>

        <a href="{{ route('member.history') }}" 
           class="flex flex-col items-center gap-1 text-[11px] font-semibold transition {{ request()->routeIs('member.history') ? 'text-emerald-700' : 'text-slate-400 hover:text-slate-600' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Riwayat</span>
        </a>
    </nav>

</div>
@endsection

