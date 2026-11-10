@extends('layouts.staff')

@section('title', 'Data Anggota')
@section('page_title', 'Kelola Data Anggota')
@section('page_description', 'Daftar seluruh anggota Koperasi Karyawan POLTEK CWE')

@section('content')
<div class="space-y-6">

    {{-- Filter & Action Bar --}}
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        
        <form action="{{ route('staff.members.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative min-w-[220px] flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari nama, NIK, no anggota..." 
                       class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-600 focus:bg-white transition">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            <select name="division_id" onchange="this.form.submit()" class="py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700">
                <option value="">Semua Divisi</option>
                @foreach($divisions as $div)
                    <option value="{{ $div->id }}" {{ request('division_id') == $div->id ? 'selected' : '' }}>{{ $div->nama }}</option>
                @endforeach
            </select>

            <select name="position_id" onchange="this.form.submit()" class="py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700">
                <option value="">Semua Jabatan</option>
                @foreach($positions as $pos)
                    <option value="{{ $pos->id }}" {{ request('position_id') == $pos->id ? 'selected' : '' }}>{{ $pos->nama }}</option>
                @endforeach
            </select>

            <select name="status" onchange="this.form.submit()" class="py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700">
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                <option value="keluar" {{ request('status') == 'keluar' ? 'selected' : '' }}>Keluar</option>
            </select>

            @if(request()->anyFilled(['search', 'division_id', 'position_id', 'status']))
                <a href="{{ route('staff.members.index') }}" class="text-xs text-rose-600 font-bold hover:underline">Reset</a>
            @endif
        </form>

        <a href="{{ route('staff.members.create') }}" class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            <span>Tambah Anggota</span>
        </a>

    </div>

    {{-- Members Table --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-4">No. Anggota</th>
                        <th class="py-3 px-4 min-w-[200px]">Nama & NIK</th>
                        <th class="py-3 px-4">Jabatan & Plafon</th>
                        <th class="py-3 px-4">Divisi</th>
                        <th class="py-3 px-4">Total Simpanan</th>
                        <th class="py-3 px-4">Pinjaman Aktif</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($members as $m)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">
                                {{ $m->no_anggota }}
                            </td>
                            <td class="py-3 px-4 min-w-[200px]">
                                <span class="font-bold text-slate-900 block text-xs break-words">{{ $m->nama_lengkap }}</span>
                                <span class="text-[11px] text-slate-400">NIK: {{ $m->nik_karyawan }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-semibold text-slate-800 block">{{ $m->position?->nama ?? '-' }}</span>
                                <span class="text-[10px] text-emerald-700 font-bold">Plafon: Rp {{ number_format($m->maxPlafonPinjaman(), 0, ',', '.') }}</span>
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ $m->division?->nama ?? '-' }}
                            </td>
                            <td class="py-3 px-4 font-bold text-emerald-700">
                                Rp {{ number_format($m->totalSaldoSimpanan(), 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $activeLoan = $m->activeLoan();
                                @endphp
                                @if($activeLoan)
                                    <span class="text-rose-600 font-bold block">Rp {{ number_format((float) $activeLoan->sisa_pinjaman, 0, ',', '.') }}</span>
                                    <span class="text-[10px] text-slate-400">Tenor: {{ $activeLoan->sisa_tenor }} bln lg</span>
                                @else
                                    <span class="text-slate-400 text-[11px]">Tidak Ada</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($m->status_keanggotaan === 'aktif')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Aktif</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">{{ ucfirst($m->status_keanggotaan) }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <a href="{{ route('staff.members.show', $m) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-emerald-50 hover:text-emerald-800 text-slate-700 font-bold transition">
                                    Detail &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10 text-slate-400">Tidak ada data anggota ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $members->links() }}
        </div>
    </div>

</div>
@endsection

