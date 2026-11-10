@extends('layouts.app')

@section('body')
<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-50 flex">

    {{-- Mobile Sidebar Backdrop --}}
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false" 
         class="fixed inset-0 bg-slate-900/60 z-40 lg:hidden" 
         style="display: none;"></div>

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed inset-y-0 left-0 z-50 w-72 bg-emerald-950 text-white flex flex-col transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 border-r border-emerald-900/50">
        
        {{-- Brand / Header --}}
        <div class="h-20 flex items-center justify-between px-6 border-b border-emerald-900/60 bg-emerald-950">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-white p-1 flex items-center justify-center shadow-md flex-shrink-0">
                    <img src="{{ asset('images/logo-cwe.png') }}" alt="Logo CWE" class="h-9 w-auto object-contain">
                </div>
                <div>
                    <h1 class="font-extrabold text-sm leading-tight text-white tracking-wide">KOPKAR CWE</h1>
                    <p class="text-[11px] text-emerald-300 font-medium">Poltek Kelapa Sawit</p>
                </div>
            </div>
            <button @click="sidebarOpen = false" class="lg:hidden text-emerald-300 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        {{-- Nav Links --}}
        <div class="flex-1 overflow-y-auto px-4 py-6 space-y-7">
            
            {{-- Operasional Koperasi --}}
            <div>
                <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-emerald-400/70 mb-2">Operasional Pengurus</p>
                <nav class="space-y-1">
                    <a href="{{ route('staff.dashboard') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('staff.dashboard') ? 'bg-emerald-800 text-white font-semibold shadow-sm' : 'text-emerald-100/80 hover:bg-emerald-900/50 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('staff.members.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('staff.members.*') ? 'bg-emerald-800 text-white font-semibold shadow-sm' : 'text-emerald-100/80 hover:bg-emerald-900/50 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span>Data Anggota</span>
                    </a>

                    <a href="{{ route('staff.savings.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('staff.savings.*') ? 'bg-emerald-800 text-white font-semibold shadow-sm' : 'text-emerald-100/80 hover:bg-emerald-900/50 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Kelola Simpanan</span>
                    </a>

                    @php
                        $pendingLoansCount = \App\Models\Loan::where('status', 'pending')->count();
                    @endphp
                    <a href="{{ route('staff.loans.index') }}" 
                       class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('staff.loans.*') ? 'bg-emerald-800 text-white font-semibold shadow-sm' : 'text-emerald-100/80 hover:bg-emerald-900/50 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            <span>Approval Pinjaman</span>
                        </div>
                        @if($pendingLoansCount > 0)
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-500 text-emerald-950 animate-pulse">
                                {{ $pendingLoansCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('staff.payroll.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('staff.payroll.*') ? 'bg-emerald-800 text-white font-semibold shadow-sm' : 'text-emerald-100/80 hover:bg-emerald-900/50 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span>Potongan Payroll</span>
                    </a>

                    <a href="{{ route('staff.reports.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('staff.reports.*') ? 'bg-emerald-800 text-white font-semibold shadow-sm' : 'text-emerald-100/80 hover:bg-emerald-900/50 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Rekap & Laporan</span>
                    </a>
                </nav>
            </div>

            {{-- Konfigurasi & Administrasi (Admin Only) --}}
            @if(Auth::user()->isAdmin())
                <div>
                    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-amber-400/80 mb-2">Administrasi Sistem</p>
                    <nav class="space-y-1">
                        <a href="{{ route('admin.users.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.users.*') ? 'bg-emerald-800 text-white font-semibold shadow-sm' : 'text-emerald-100/80 hover:bg-emerald-900/50 hover:text-white' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <span>Kelola User</span>
                        </a>

                        <a href="{{ route('admin.master.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.master.*') ? 'bg-emerald-800 text-white font-semibold shadow-sm' : 'text-emerald-100/80 hover:bg-emerald-900/50 hover:text-white' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            <span>Divisi & Plafon Jabatan</span>
                        </a>

                        <a href="{{ route('admin.settings.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.settings.*') ? 'bg-emerald-800 text-white font-semibold shadow-sm' : 'text-emerald-100/80 hover:bg-emerald-900/50 hover:text-white' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>Konfigurasi Koperasi</span>
                        </a>

                        <a href="{{ route('admin.audit-logs.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.audit-logs.*') ? 'bg-emerald-800 text-white font-semibold shadow-sm' : 'text-emerald-100/80 hover:bg-emerald-900/50 hover:text-white' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Audit Log Trail</span>
                        </a>
                    </nav>
                </div>
            @endif

        </div>

        {{-- Footer User Profile --}}
        <div class="p-4 border-t border-emerald-900/60 bg-emerald-950/80">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-9 h-9 rounded-full bg-emerald-700 flex items-center justify-center font-bold text-white text-sm flex-shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="truncate">
                        <p class="text-xs font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                        <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ Auth::user()->isAdmin() ? 'bg-amber-400 text-amber-950' : 'bg-emerald-400 text-emerald-950' }}">
                            {{ Auth::user()->role }}
                        </span>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Logout" class="p-2 text-emerald-300 hover:text-rose-400 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main Content Area --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        {{-- Top Bar --}}
        <header class="h-20 bg-white border-b border-slate-200 px-4 sm:px-8 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div>
                    <h2 class="text-lg font-bold text-slate-800">@yield('page_title', 'Dashboard')</h2>
                    <p class="text-xs text-slate-500 hidden sm:block">@yield('page_description', 'Sistem Informasi Koperasi Karyawan POLTEK CWE')</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-500 hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 rounded-full">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Tahun Buku {{ now()->year }}
                </span>
                <a href="{{ route('member.dashboard') }}" target="_blank" class="text-xs text-emerald-700 bg-emerald-50 hover:bg-emerald-100 font-semibold px-3 py-2 rounded-xl transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    <span>Lihat Portal Anggota</span>
                </a>
            </div>
        </header>

        {{-- Page Body --}}
        <main class="flex-1 overflow-y-auto p-4 sm:p-8 bg-slate-50">
            @yield('content')
        </main>
    </div>

</div>
@endsection

