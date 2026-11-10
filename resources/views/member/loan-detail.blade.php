@extends('layouts.member')

@section('title', 'Detail Pinjaman ' . $loan->no_pinjaman)

@section('content')
<div class="space-y-6">

    {{-- Breadcrumb & Back --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('member.dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-emerald-700 transition">
            &larr; Kembali ke Dashboard
        </a>
        <span class="text-xs text-slate-400">Diajukan: {{ $loan->created_at->format('d M Y, H:i') }}</span>
    </div>

    {{-- Main Summary Card --}}
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Nomor Pinjaman</span>
                <h2 class="text-2xl font-black text-slate-900 mt-0.5">{{ $loan->no_pinjaman }}</h2>
                <p class="text-xs text-slate-500 mt-1">Keperluan: {{ $loan->keperluan }}</p>
            </div>

            <div>
                @php
                    $statusStyles = [
                        'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                        'disetujui' => 'bg-sky-100 text-sky-800 border-sky-200',
                        'berjalan' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                        'lunas' => 'bg-teal-100 text-teal-800 border-teal-200',
                        'ditolak' => 'bg-rose-100 text-rose-800 border-rose-200',
                    ];
                @endphp
                <span class="inline-block px-4 py-1.5 rounded-full text-xs font-extrabold uppercase tracking-wider border {{ $statusStyles[$loan->status] ?? 'bg-slate-100 text-slate-700' }}">
                    Status: {{ strtoupper($loan->status) }}
                </span>
            </div>
        </div>

        {{-- Financial Metrics --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 py-6 border-b border-slate-100 text-xs">
            <div>
                <span class="text-slate-400 block text-[11px]">Nominal Diajukan</span>
                <span class="font-extrabold text-sm text-slate-800">Rp {{ number_format((float) $loan->nominal_pengajuan, 0, ',', '.') }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Nominal Disetujui</span>
                <span class="font-extrabold text-sm text-emerald-700">Rp {{ number_format((float) $loan->nominal_disetujui, 0, ',', '.') }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Tenor & Bunga</span>
                <span class="font-extrabold text-sm text-slate-800">{{ $loan->tenor_bulan }} Bln ({{ $loan->bunga_persen }}%)</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Potongan per Bulan</span>
                <span class="font-extrabold text-sm text-amber-600">Rp {{ number_format((float) $loan->total_angsuran_perbulan, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Catatan Pengurus --}}
        @if($loan->catatan_pengurus)
            <div class="mt-6 p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs text-slate-700">
                <span class="font-bold text-slate-900 block mb-0.5">Catatan Pengurus / Alasan:</span>
                <p class="leading-relaxed">{{ $loan->catatan_pengurus }}</p>
                @if($loan->approver)
                    <span class="text-[10px] text-slate-400 block mt-2">Ditinjau oleh: {{ $loan->approver->name }} ({{ $loan->approved_at?->format('d M Y, H:i') }})</span>
                @endif
            </div>
        @endif
    </div>

    {{-- Installment Amortization Table --}}
    @if($loan->installments->isNotEmpty())
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm overflow-hidden">
            <h4 class="font-bold text-sm text-slate-800 mb-1">Daftar Angsuran Bulanan</h4>
            <p class="text-xs text-slate-400 mb-4">Rincian status pembayaran cicilan yang terhubung dengan payroll bulanan.</p>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-3">Ke</th>
                            <th class="py-3 px-3">Jatuh Tempo</th>
                            <th class="py-3 px-3">Pokok</th>
                            <th class="py-3 px-3">Bunga</th>
                            <th class="py-3 px-3">Total Angsuran</th>
                            <th class="py-3 px-3">Sisa Saldo Pokok</th>
                            <th class="py-3 px-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($loan->installments as $inst)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-2.5 px-3 font-bold text-slate-900">{{ $inst->angsuran_ke }}</td>
                                <td class="py-2.5 px-3">{{ \Carbon\Carbon::parse($inst->jatuh_tempo)->format('d M Y') }}</td>
                                <td class="py-2.5 px-3">Rp {{ number_format((float) $inst->nominal_pokok, 0, ',', '.') }}</td>
                                <td class="py-2.5 px-3 text-emerald-700">Rp {{ number_format((float) $inst->nominal_bunga, 0, ',', '.') }}</td>
                                <td class="py-2.5 px-3 font-bold text-slate-900">Rp {{ number_format((float) $inst->total_angsuran, 0, ',', '.') }}</td>
                                <td class="py-2.5 px-3 text-slate-500">Rp {{ number_format((float) $inst->sisa_saldo_pokok, 0, ',', '.') }}</td>
                                <td class="py-2.5 px-3">
                                    @if($inst->status_bayar === 'lunas')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            ✓ Lunas
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                            Belum
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection

