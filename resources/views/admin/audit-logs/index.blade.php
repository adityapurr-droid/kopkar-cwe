@extends('layouts.staff')

@section('title', 'Audit Log Trail')
@section('page_title', 'Rekam Jejak Aktivitas (Audit Log)')
@section('page_description', 'Riwayat aktivitas pengguna, perubahan data, approval pinjaman, dan transaksi keuangan')

@section('content')
<div class="space-y-6">

    {{-- Filter Bar --}}
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
        <form action="{{ route('admin.audit-logs.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Cari deskripsi, user, IP..." 
                   class="py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs flex-1 min-w-[200px]">

            <select name="modul" onchange="this.form.submit()" class="py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700">
                <option value="">Semua Modul</option>
                @foreach($modules as $m)
                    <option value="{{ $m }}" {{ request('modul') == $m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
            </select>

            <select name="aksi" onchange="this.form.submit()" class="py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700">
                <option value="">Semua Aksi</option>
                @foreach($actions as $a)
                    <option value="{{ $a }}" {{ request('aksi') == $a ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
            </select>

            <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" 
                   class="py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs">

            <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-xl text-xs font-bold hover:bg-slate-700 transition">
                Filter
            </button>

            @if(request()->anyFilled(['search', 'modul', 'aksi', 'tanggal_dari']))
                <a href="{{ route('admin.audit-logs.index') }}" class="text-xs text-rose-600 font-bold hover:underline">Reset</a>
            @endif
        </form>
    </div>

    {{-- Audit Table --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Pengguna</th>
                        <th class="py-3 px-4">Modul</th>
                        <th class="py-3 px-4">Aksi</th>
                        <th class="py-3 px-4">Rincian Aktivitas</th>
                        <th class="py-3 px-4 text-right">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-mono text-slate-500 whitespace-nowrap">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="font-bold text-slate-900 block">{{ $log->user?->name ?? 'Sistem' }}</span>
                                <span class="text-[10px] text-slate-400">Role: {{ $log->user?->role ?? '-' }}</span>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="font-bold text-slate-800">{{ $log->modul }}</span>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                @php
                                    $actionStyles = [
                                        'LOGIN' => 'bg-emerald-100 text-emerald-800',
                                        'LOGOUT' => 'bg-slate-100 text-slate-700',
                                        'CREATE' => 'bg-sky-100 text-sky-800',
                                        'UPDATE' => 'bg-indigo-100 text-indigo-800',
                                        'DELETE' => 'bg-rose-100 text-rose-800',
                                        'APPROVE' => 'bg-emerald-100 text-emerald-800',
                                        'REJECT' => 'bg-rose-100 text-rose-800',
                                        'DISBURSE' => 'bg-amber-100 text-amber-900',
                                        'SETTLE' => 'bg-teal-100 text-teal-900',
                                    ];
                                @endphp
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase {{ $actionStyles[$log->aksi] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ $log->aksi }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-800">
                                {{ $log->deskripsi }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-[11px] text-slate-400 whitespace-nowrap">
                                {{ $log->ip_address ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-slate-400">Belum ada rekam jejak aktivitas tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
    </div>

</div>
@endsection

