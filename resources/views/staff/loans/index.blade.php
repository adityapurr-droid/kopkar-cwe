@extends('layouts.staff')

@section('title', 'Approval & Kelola Pinjaman')
@section('page_title', 'Kelola Pinjaman & Approval')
@section('page_description', 'Tinjau permohonan pinjaman, persetujuan plafon, dan status pembayaran angsuran')

@section('content')
<div class="space-y-6">

    {{-- Status Filter Tabs --}}
    <div class="flex flex-wrap items-center gap-2 bg-white rounded-3xl p-2 border border-slate-100 shadow-sm">
        @php
            $currentStatus = request('status');
        @endphp

        <a href="{{ route('staff.loans.index') }}" 
           class="px-4 py-2 rounded-2xl text-xs transition {{ empty($currentStatus) ? 'bg-slate-900 text-white font-bold shadow' : 'text-slate-600 hover:bg-slate-100 font-semibold' }}">
            Semua ({{ $counts['all'] }})
        </a>

        <a href="{{ route('staff.loans.index', ['status' => 'pending']) }}" 
           class="px-4 py-2 rounded-2xl text-xs transition flex items-center gap-1.5 {{ $currentStatus === 'pending' ? 'bg-amber-500 text-emerald-950 font-extrabold shadow' : 'text-amber-800 bg-amber-50 hover:bg-amber-100 font-bold' }}">
            <span>Menunggu Approval</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-amber-600 text-white">{{ $counts['pending'] }}</span>
        </a>

        <a href="{{ route('staff.loans.index', ['status' => 'disetujui']) }}" 
           class="px-4 py-2 rounded-2xl text-xs transition {{ $currentStatus === 'disetujui' ? 'bg-sky-700 text-white font-bold shadow' : 'text-slate-600 hover:bg-slate-100 font-semibold' }}">
            Disetujui / Siap Cair ({{ $counts['disetujui'] }})
        </a>

        <a href="{{ route('staff.loans.index', ['status' => 'berjalan']) }}" 
           class="px-4 py-2 rounded-2xl text-xs transition {{ $currentStatus === 'berjalan' ? 'bg-emerald-700 text-white font-bold shadow' : 'text-slate-600 hover:bg-slate-100 font-semibold' }}">
            Berjalan / Aktif ({{ $counts['berjalan'] }})
        </a>

        <a href="{{ route('staff.loans.index', ['status' => 'lunas']) }}" 
           class="px-4 py-2 rounded-2xl text-xs transition {{ $currentStatus === 'lunas' ? 'bg-teal-700 text-white font-bold shadow' : 'text-slate-600 hover:bg-slate-100 font-semibold' }}">
            Lunas ({{ $counts['lunas'] }})
        </a>

        <a href="{{ route('staff.loans.index', ['status' => 'ditolak']) }}" 
           class="px-4 py-2 rounded-2xl text-xs transition {{ $currentStatus === 'ditolak' ? 'bg-rose-700 text-white font-bold shadow' : 'text-slate-600 hover:bg-slate-100 font-semibold' }}">
            Ditolak ({{ $counts['ditolak'] }})
        </a>
    </div>

    {{-- Loans Table --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-4">No. Pinjaman</th>
                        <th class="py-3 px-4 min-w-[200px]">Anggota & Jabatan</th>
                        <th class="py-3 px-4">Nominal</th>
                        <th class="py-3 px-4">Plafon</th>
                        <th class="py-3 px-4">Tenor & Bunga</th>
                        <th class="py-3 px-4">Angsuran / Bln</th>
                        <th class="py-3 px-4">Sisa Hutang</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($loans as $loan)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">
                                {{ $loan->no_pinjaman }}
                            </td>
                            <td class="py-3 px-4 min-w-[200px]">
                                <a href="{{ route('staff.members.show', $loan->member) }}" class="font-bold text-slate-900 hover:text-emerald-700 block text-xs break-words">
                                    {{ $loan->member->nama_lengkap }}
                                </a>
                                <span class="text-[11px] text-slate-400 block leading-tight mt-0.5">{{ $loan->member->position?->nama ?? '-' }} &bull; {{ $loan->member->division?->nama ?? '-' }}</span>
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-800">
                                Rp {{ number_format((float) $loan->nominal_pengajuan, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 font-extrabold text-emerald-700">
                                Rp {{ number_format((float) $loan->nominal_disetujui, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="block font-bold text-slate-900">{{ $loan->tenor_bulan }} Bulan</span>
                                <span class="text-[11px] text-slate-400">{{ $loan->bunga_persen }}% Flat / bln</span>
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-900">
                                Rp {{ number_format((float) $loan->total_angsuran_perbulan, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4">
                                @if(in_array($loan->status, ['berjalan', 'disetujui']))
                                    <span class="font-bold text-rose-600 block">Rp {{ number_format((float) $loan->sisa_pinjaman, 0, ',', '.') }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $loan->sisa_tenor }} bln tersisa</span>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $statusStyles = [
                                        'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        'disetujui' => 'bg-sky-100 text-sky-800 border-sky-200',
                                        'berjalan' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'lunas' => 'bg-teal-100 text-teal-800 border-teal-200',
                                        'ditolak' => 'bg-rose-100 text-rose-800 border-rose-200',
                                    ];
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $statusStyles[$loan->status] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ $loan->status }}
                                </span>
                            </td>
                             <td class="py-3 px-4 text-right whitespace-nowrap">
                                <a href="{{ route('staff.loans.show', $loan) }}" class="inline-flex items-center px-4 py-2 rounded-xl font-bold text-xs transition {{ $loan->status === 'pending' ? 'bg-amber-400 hover:bg-amber-300 text-emerald-950 shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-800' }}">
                                    {{ $loan->status === 'pending' ? 'Review & Approval' : 'Detail' }} &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-10 text-slate-400">Tidak ada data pinjaman ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $loans->links() }}
        </div>
    </div>

</div>
@endsection

