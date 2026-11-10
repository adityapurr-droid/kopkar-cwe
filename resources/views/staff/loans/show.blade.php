@extends('layouts.staff')

@section('title', 'Review Pinjaman ' . $loan->no_pinjaman)
@section('page_title', 'Review & Approval Pinjaman')
@section('page_description', 'Pemeriksaan kelayakan, persetujuan nominal, dan penerbitan amortisasi angsuran')

@section('content')
<div class="space-y-6" x-data="{
    showApproveConfirm: false,
    showRejectConfirm: false,
    showDisburseConfirm: false,
    confirmAction(action) {
        this[action] = true;
    },
    submitForm(formId) {
        document.getElementById(formId).submit();
    }
}">

    {{-- Breadcrumb --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('staff.loans.index') }}" class="text-xs font-bold text-slate-500 hover:text-emerald-700 transition">
            &larr; Kembali ke Daftar Pinjaman
        </a>
        <span class="text-xs text-slate-400">Diajukan: {{ $loan->created_at->format('d M Y, H:i') }}</span>
    </div>

    {{-- Applicant Profile & Financial Feasibility Check --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Member Context --}}
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
            <h3 class="font-bold text-sm text-slate-900 border-b border-slate-100 pb-2">Profil Pemohon Pinjaman</h3>
            
            <div>
                <span class="text-[10px] font-bold uppercase text-slate-400 block">Nama Anggota</span>
                <span class="text-sm font-black text-slate-900">{{ $member->nama_lengkap }}</span>
                <span class="text-xs text-slate-500 block">NIK: {{ $member->nik_karyawan }} &bull; No: {{ $member->no_anggota }}</span>
            </div>

            <div>
                <span class="text-[10px] font-bold uppercase text-slate-400 block">Divisi & Jabatan</span>
                <span class="text-xs font-semibold text-slate-800">{{ $member->division?->nama ?? '-' }}</span>
                <span class="text-xs text-emerald-700 font-bold block">{{ $member->position?->nama ?? '-' }}</span>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">Plafon Maksimal Jabatan:</span>
                    <span class="font-black text-slate-900">Rp {{ number_format($maxPlafon, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">Total Simpanan Dimiliki:</span>
                    <span class="font-bold text-emerald-700">Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">Masa Keanggotaan:</span>
                    <span class="font-medium text-slate-700">{{ $member->tanggal_bergabung->diffForHumans(null, true) }}</span>
                </div>
            </div>
        </div>

        {{-- Loan Application Overview --}}
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400 block">Nomor Pengajuan</span>
                    <h3 class="text-xl font-black text-slate-900">{{ $loan->no_pinjaman }}</h3>
                </div>

                @php
                    $statusStyles = [
                        'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                        'disetujui' => 'bg-sky-100 text-sky-800 border-sky-200',
                        'berjalan' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                        'lunas' => 'bg-teal-100 text-teal-800 border-teal-200',
                        'ditolak' => 'bg-rose-100 text-rose-800 border-rose-200',
                    ];
                @endphp
                <span class="px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider border {{ $statusStyles[$loan->status] ?? 'bg-slate-100' }}">
                    Status: {{ $loan->status }}
                </span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                <div>
                    <span class="text-slate-400 block text-[11px]">Nominal Diajukan</span>
                    <span class="font-extrabold text-sm text-slate-900">Rp {{ number_format((float) $loan->nominal_pengajuan, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Plafon Disetujui</span>
                    <span class="font-extrabold text-sm text-emerald-700">Rp {{ number_format((float) $loan->nominal_disetujui, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Tenor & Suku Bunga</span>
                    <span class="font-extrabold text-sm text-slate-900">{{ $loan->tenor_bulan }} Bulan ({{ $loan->bunga_persen }}%/bln)</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Potongan per Bulan</span>
                    <span class="font-extrabold text-sm text-amber-600">Rp {{ number_format((float) $loan->total_angsuran_perbulan, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-700">
                <span class="font-bold text-slate-900 block mb-0.5">Keperluan Pinjaman (Alasan Anggota):</span>
                <p>{{ $loan->keperluan ?: '-' }}</p>
            </div>

            @if($loan->catatan_pengurus)
                <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200/80 text-xs text-amber-900">
                    <span class="font-bold block mb-0.5">Catatan Pengurus / Alasan Putusan:</span>
                    <p>{{ $loan->catatan_pengurus }}</p>
                    @if($loan->approver)
                        <span class="text-[10px] text-amber-700 block mt-1">Oleh: {{ $loan->approver->name }} ({{ $loan->approved_at?->format('d M Y H:i') }})</span>
                    @endif
                </div>
            @endif
        </div>

    </div>

    {{-- Approval Actions Area (When Status is Pending) --}}
    @if($loan->status === 'pending')
        <div class="bg-white rounded-3xl p-6 sm:p-8 border-2 border-amber-200 shadow-md space-y-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                    ⚖️
                </div>
                <div>
                    <h3 class="font-black text-slate-900 text-base">Keputusan Pengurus & Revisi Nominal</h3>
                    <p class="text-xs text-slate-500">Anda dapat menyetujui sesuai permohonan, merevisi nominal pencairan sesuai plafon, atau menolak permohonan.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                
                {{-- Form Persetujuan / Revisi --}}
                <div class="p-5 rounded-2xl bg-emerald-50/60 border border-emerald-200 space-y-4">
                    <h4 class="font-extrabold text-sm text-emerald-950 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                        Setujui Permohonan Pinjaman
                    </h4>

                    <form action="{{ route('staff.loans.approve', $loan) }}" method="POST" class="space-y-3" id="approveForm">
                        @csrf
                        
                        <div>
                            <label class="block text-xs font-bold text-emerald-950 uppercase mb-1">
                                Nominal Pencairan Disetujui (Rp) *
                            </label>
                            <input type="number" name="nominal_disetujui" 
                                   value="{{ old('nominal_disetujui', (float) $loan->nominal_pengajuan) }}" 
                                   min="500000" max="{{ $maxPlafon }}" step="100000" required
                                   class="w-full p-2.5 bg-white border border-emerald-300 rounded-xl text-sm font-black text-emerald-950 focus:ring-2 focus:ring-emerald-600">
                            <span class="text-[10px] text-emerald-800 mt-1 block">
                                Maksimal Plafon Jabatan: Rp {{ number_format($maxPlafon, 0, ',', '.') }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-emerald-950 uppercase mb-1">
                                Catatan Persetujuan (Opsional)
                            </label>
                            <textarea name="catatan_pengurus" rows="2" placeholder="Catatan persetujuan / revisi nominal..." 
                                      class="w-full p-2 bg-white border border-emerald-300 rounded-xl text-xs">{{ old('catatan_pengurus') }}</textarea>
                        </div>

                        <button type="button" 
                                @click="confirmAction('showApproveConfirm')"
                                class="w-full py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-extrabold shadow transition">
                            ✓ Setujui Pinjaman
                        </button>
                    </form>
                </div>

                {{-- Form Penolakan --}}
                <div class="p-5 rounded-2xl bg-rose-50/60 border border-rose-200 space-y-4">
                    <h4 class="font-extrabold text-sm text-rose-950 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                        Tolak Permohonan Pinjaman
                    </h4>

                    <form action="{{ route('staff.loans.reject', $loan) }}" method="POST" class="space-y-3" id="rejectForm">
                        @csrf

                        <div>
                            <label class="block text-xs font-bold text-rose-950 uppercase mb-1">
                                Alasan Penolakan Pinjaman *
                            </label>
                            <textarea name="catatan_pengurus" rows="4" required placeholder="Sertakan alasan mengapa permohonan pinjaman ditolak..." 
                                      class="w-full p-2 bg-white border border-rose-300 rounded-xl text-xs">{{ old('catatan_pengurus') }}</textarea>
                        </div>

                        <button type="button" 
                                @click="confirmAction('showRejectConfirm')"
                                class="w-full py-2.5 bg-rose-700 hover:bg-rose-800 text-white rounded-xl text-xs font-extrabold shadow transition">
                            ✕ Tolak Permohonan
                        </button>
                    </form>
                </div>

            </div>
        </div>
    @endif

    {{-- Disbursement Action Area (When Status is Disetujui) --}}
    @if($loan->status === 'disetujui')
        <div class="bg-gradient-to-r from-sky-900 to-emerald-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-sky-300 bg-sky-950/60 px-3 py-1 rounded-full border border-sky-400/30">
                    Langkah Terakhir: Pencairan Dana
                </span>
                <h3 class="text-xl font-black mt-2">Cairkan Pinjaman Sebesar Rp {{ number_format((float) $loan->nominal_disetujui, 0, ',', '.') }}</h3>
                <p class="text-xs text-sky-200 mt-1 max-w-xl">
                    Setelah dana diserahkan/ditransfer ke rekening anggota, klik tombol pencairan di samping. Sistem akan otomatis menerbitkan {{ $loan->tenor_bulan }} baris jadwal angsuran bulanan ke tabel pemotongan payroll.
                </p>
            </div>

            <form action="{{ route('staff.loans.disburse', $loan) }}" method="POST" id="disburseForm">
                @csrf
                <button type="button" 
                        @click="confirmAction('showDisburseConfirm')"
                        class="px-6 py-3.5 bg-amber-400 hover:bg-amber-300 text-emerald-950 font-black rounded-2xl shadow-lg transition whitespace-nowrap active:scale-95">
                    💵 Konfirmasi Pencairan Dana &rarr;
                </button>
            </form>
        </div>
    @endif

    {{-- Installment Schedule Table --}}
    @if($loan->installments->isNotEmpty())
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="font-bold text-sm text-slate-800">Tabel Amortisasi Jadwal Angsuran</h4>
                    <p class="text-xs text-slate-400">Terintegrasi otomatis dengan modul pemotongan gaji payroll POLTEK CWE.</p>
                </div>
                <span class="text-xs font-bold text-slate-700">Total: {{ $loan->installments->count() }} Angsuran</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-3">Angsuran Ke</th>
                            <th class="py-3 px-3">Jatuh Tempo</th>
                            <th class="py-3 px-3">Pokok</th>
                            <th class="py-3 px-3">Bunga ({{ $loan->bunga_persen }}%)</th>
                            <th class="py-3 px-3 font-bold text-slate-900">Total Angsuran</th>
                            <th class="py-3 px-3">Sisa Saldo Pokok</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3 text-right">Tanggal Bayar</th>
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
                                <td class="py-2.5 px-3 text-right text-slate-400">
                                    {{ $inst->tanggal_bayar?->format('d M Y') ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Confirmation Modals --}}
    {{-- Approve Confirmation --}}
    <div x-show="showApproveConfirm" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 flex items-center justify-center p-4" 
         style="display: none;">
        
        <div @click.away="showApproveConfirm = false" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-emerald-200">
            
            <h3 class="text-lg font-extrabold text-slate-900 mb-2">Konfirmasi Persetujuan</h3>
            <p class="text-sm text-slate-600 mb-6">Apakah Anda yakin ingin menyetujui pinjaman ini dengan nominal tersebut?</p>
            
            <div class="flex gap-3">
                <button @click="showApproveConfirm = false" 
                        class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-bold transition">
                    Batal
                </button>
                <button @click="submitForm('approveForm')" 
                        class="flex-1 px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-sm font-extrabold transition">
                    Ya, Setujui
                </button>
            </div>
        </div>
    </div>

    {{-- Reject Confirmation --}}
    <div x-show="showRejectConfirm" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 flex items-center justify-center p-4" 
         style="display: none;">
        
        <div @click.away="showRejectConfirm = false" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-rose-200">
            
            <h3 class="text-lg font-extrabold text-slate-900 mb-2">Konfirmasi Penolakan</h3>
            <p class="text-sm text-slate-600 mb-6">Apakah Anda yakin ingin menolak permohonan pinjaman ini?</p>
            
            <div class="flex gap-3">
                <button @click="showRejectConfirm = false" 
                        class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-bold transition">
                    Batal
                </button>
                <button @click="submitForm('rejectForm')" 
                        class="flex-1 px-4 py-2.5 bg-rose-700 hover:bg-rose-800 text-white rounded-xl text-sm font-extrabold transition">
                    Ya, Tolak
                </button>
            </div>
        </div>
    </div>

    {{-- Disburse Confirmation --}}
    <div x-show="showDisburseConfirm" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 flex items-center justify-center p-4" 
         style="display: none;">
        
        <div @click.away="showDisburseConfirm = false" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-amber-200">
            
            <h3 class="text-lg font-extrabold text-slate-900 mb-2">Konfirmasi Pencairan</h3>
            <p class="text-sm text-slate-600 mb-6">Konfirmasi pencairan dana pinjaman sebesar Rp {{ number_format((float) $loan->nominal_disetujui, 0, ',', '.') }}? Jadwal angsuran akan langsung aktif.</p>
            
            <div class="flex gap-3">
                <button @click="showDisburseConfirm = false" 
                        class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-bold transition">
                    Batal
                </button>
                <button @click="submitForm('disburseForm')" 
                        class="flex-1 px-4 py-2.5 bg-amber-400 hover:bg-amber-300 text-emerald-950 rounded-xl text-sm font-extrabold transition">
                    Ya, Cairkan
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

