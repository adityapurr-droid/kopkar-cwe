@extends('layouts.staff')

@section('title', 'Rekap Potongan Gaji ' . $period->nama_periode)
@section('page_title', 'Rekap Pemotongan Gaji (Payroll CWE)')
@section('page_description', $period->nama_periode . ' &bull; Cut-off: ' . $period->tanggal_cutoff->format('d M Y'))

@section('content')
<div class="space-y-6" x-data="{ showSettleConfirm: false }">

    {{-- Breadcrumb & Action Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('staff.payroll.index') }}" class="text-xs font-bold text-slate-500 hover:text-emerald-700 transition">
            &larr; Kembali ke Daftar Periode Payroll
        </a>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('staff.payroll.export-excel', $period) }}" 
               class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Unduh Excel (.xlsx)</span>
            </a>

            <a href="{{ route('staff.payroll.export-pdf', $period) }}" target="_blank"
               class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak PDF</span>
            </a>

            @if($period->status !== 'selesai')
                <form action="{{ route('staff.payroll.settle', $period) }}" method="POST" id="settleForm">
                    @csrf
                    <button type="button" 
                            @click="showSettleConfirm = true"
                            class="px-5 py-2 bg-amber-400 hover:bg-amber-300 text-emerald-950 font-black rounded-xl text-xs shadow-md transition active:scale-95">
                        ✓ Selesaikan & Sinkronkan Pelunasan
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Period KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Karyawan</span>
            <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $deductions->count() }} <span class="text-xs font-normal text-slate-500">orang</span></h3>
        </div>
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Simpanan Wajib</span>
            <h3 class="text-2xl font-black text-emerald-700 mt-1">Rp {{ number_format((float) $period->total_simpanan_wajib, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Angsuran Pinjaman</span>
            <h3 class="text-2xl font-black text-amber-600 mt-1">Rp {{ number_format((float) $period->total_angsuran_pinjaman, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-gradient-to-r from-emerald-800 to-teal-900 rounded-3xl p-5 text-white shadow-lg">
            <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-200">Total Potongan Payroll</span>
            <h3 class="text-2xl font-black text-amber-300 mt-1">Rp {{ number_format((float) $period->total_pemotongan, 0, ',', '.') }}</h3>
        </div>
    </div>

    {{-- Detailed Matrix Table --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h4 class="font-bold text-sm text-slate-800">Daftar Rincian Potongan Gaji Karyawan POLTEK CWE</h4>
            <span class="text-xs font-bold px-3 py-1 rounded-full {{ $period->status === 'selesai' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                Status Periode: {{ strtoupper($period->status) }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">No. Anggota</th>
                        <th class="py-3 px-4">Nama Lengkap & NIK</th>
                        <th class="py-3 px-4">Divisi & Jabatan</th>
                        <th class="py-3 px-4 text-right">Simpanan Wajib</th>
                        <th class="py-3 px-4 text-right">Angsuran Pinjaman</th>
                        <th class="py-3 px-4 text-right font-black text-slate-900">Total Potongan Gaji</th>
                        <th class="py-3 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @foreach($deductions as $index => $d)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 text-slate-400 font-mono">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">{{ $d->member->no_anggota }}</td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-900 block">{{ $d->member->nama_lengkap }}</span>
                                <span class="text-[11px] text-slate-400">NIK: {{ $d->member->nik_karyawan }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-slate-800 block">{{ $d->member->division?->nama ?? '-' }}</span>
                                <span class="text-[11px] text-emerald-700 font-semibold">{{ $d->member->position?->nama ?? '-' }}</span>
                            </td>
                            <td class="py-3 px-4 text-right font-bold text-emerald-700">
                                Rp {{ number_format((float) $d->nominal_simpanan_wajib, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right font-bold {{ (float) $d->nominal_angsuran_pinjaman > 0 ? 'text-amber-700' : 'text-slate-400' }}">
                                Rp {{ number_format((float) $d->nominal_angsuran_pinjaman, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right font-black text-slate-900 text-sm">
                                Rp {{ number_format((float) $d->total_potongan, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($d->status === 'lunas')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        ✓ Lunas Potong
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                        Pending
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50 font-black text-slate-900 border-t border-slate-200">
                    <tr>
                        <td colspan="4" class="py-3.5 px-4 text-right uppercase tracking-wider text-xs">Total Seluruh Potongan:</td>
                        <td class="py-3.5 px-4 text-right text-emerald-700">Rp {{ number_format((float) $period->total_simpanan_wajib, 0, ',', '.') }}</td>
                        <td class="py-3.5 px-4 text-right text-amber-700">Rp {{ number_format((float) $period->total_angsuran_pinjaman, 0, ',', '.') }}</td>
                        <td class="py-3.5 px-4 text-right text-base text-slate-900">Rp {{ number_format((float) $period->total_pemotongan, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Confirmation Modal --}}
    <div x-show="showSettleConfirm" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 flex items-center justify-center p-4" 
         style="display: none;">
        
        <div @click.away="showSettleConfirm = false" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-amber-200">
            
            <h3 class="text-lg font-extrabold text-slate-900 mb-2">Konfirmasi Sinkronisasi</h3>
            <p class="text-sm text-slate-600 mb-6">Apakah pemotongan gaji untuk periode ini sudah selesai dipotong oleh bagian Keuangan/Payroll CWE? Setelah dikonfirmasi, saldo simpanan wajib akan bertambah dan cicilan pinjaman akan otomatis ditandai lunas.</p>
            
            <div class="flex gap-3">
                <button @click="showSettleConfirm = false" 
                        class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-bold transition">
                    Batal
                </button>
                <button @click="document.getElementById('settleForm').submit()" 
                        class="flex-1 px-4 py-2.5 bg-amber-400 hover:bg-amber-300 text-emerald-950 rounded-xl text-sm font-extrabold transition">
                    Ya, Sinkronkan
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

