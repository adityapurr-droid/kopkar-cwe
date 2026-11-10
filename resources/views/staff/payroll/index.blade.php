@extends('layouts.staff')

@section('title', 'Pemotongan Gaji Payroll')
@section('page_title', 'Kelola Pemotongan Gaji (Payroll CWE)')
@section('page_description', 'Generate matriks potongan simpanan wajib & cicilan pinjaman bulanan untuk payroll POLTEK CWE')

@section('content')
<div x-data="{ openModal: false }" class="space-y-6">

    {{-- Info Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Simpanan Wajib Bulanan</span>
            <h3 class="text-2xl font-black text-emerald-700 mt-1">Rp {{ number_format($simpananWajibNominal, 0, ',', '.') }}</h3>
            <span class="text-[11px] text-slate-500">Per anggota aktif setiap bulan</span>
        </div>
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Anggota Aktif Terkena Potongan</span>
            <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $activeMembersCount }} <span class="text-xs font-normal text-slate-500">orang</span></h3>
            <span class="text-[11px] text-slate-500">Termasuk dosen & staf POLTEK CWE</span>
        </div>
        <div class="bg-gradient-to-r from-emerald-800 to-teal-900 rounded-3xl p-5 text-white shadow-lg flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-200">Pinjaman Berjalan</span>
                <h3 class="text-2xl font-black text-amber-300 mt-1">{{ $activeLoansCount }} <span class="text-xs font-normal text-emerald-200">rekening</span></h3>
                <span class="text-[11px] text-emerald-200/80">Cicilan dipotong otomatis</span>
            </div>
            <button @click="openModal = true" class="px-4 py-2.5 bg-amber-400 hover:bg-amber-300 text-emerald-950 rounded-2xl text-xs font-black shadow transition active:scale-95">
                ⚡ Generate Baru
            </button>
        </div>
    </div>

    {{-- Period List --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-sm text-slate-900">Daftar Rekapitulasi Periode Payroll</h3>
                <p class="text-xs text-slate-400">Riwayat berkas pemotongan gaji yang telah diterbitkan untuk bagian keuangan/payroll kampus.</p>
            </div>

            <button @click="openModal = true" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                <span>Generate Periode Baru</span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-4">Kode Periode</th>
                        <th class="py-3 px-4">Nama Periode</th>
                        <th class="py-3 px-4">Tanggal Cut-off</th>
                        <th class="py-3 px-4">Simpanan Wajib</th>
                        <th class="py-3 px-4">Angsuran Pinjaman</th>
                        <th class="py-3 px-4 font-black text-slate-900">Total Potongan Gaji</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($periods as $p)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                {{ $p->kode_periode }}
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                {{ $p->nama_periode }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ $p->tanggal_cutoff->format('d M Y') }}
                            </td>
                            <td class="py-3.5 px-4 text-emerald-700 font-bold">
                                Rp {{ number_format((float) $p->total_simpanan_wajib, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-amber-700 font-bold">
                                Rp {{ number_format((float) $p->total_angsuran_pinjaman, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 font-black text-slate-900 text-sm">
                                Rp {{ number_format((float) $p->total_pemotongan, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($p->status === 'selesai')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        ✓ Lunas Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                        Draft Terbit
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <a href="{{ route('staff.payroll.show', $p->kode_periode) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-emerald-50 hover:text-emerald-800 text-slate-800 font-bold transition">
                                    Buka Rekap &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10 text-slate-400">
                                Belum ada data periode payroll. Klik tombol "Generate Periode Baru" untuk membuat rekap bulan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $periods->links() }}
        </div>
    </div>

    {{-- Modal Generate Periode Baru --}}
    <div x-show="openModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 flex items-center justify-center p-4" 
         style="display: none;">
        
        <div @click.away="openModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl space-y-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-extrabold text-slate-900">Generate Periode Pemotongan Gaji</h3>
                <button @click="openModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>

            <form action="{{ route('staff.payroll.generate') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Kode Periode (Tahun-Bulan) *
                    </label>
                    <input type="text" name="kode_periode" value="{{ now()->format('Y-m') }}" required placeholder="YYYY-MM"
                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono font-bold">
                    <span class="text-[10px] text-slate-400 mt-1 block">Contoh: {{ now()->format('Y-m') }} untuk bulan ini</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Nama Periode *
                    </label>
                    <input type="text" name="nama_periode" value="Gaji {{ now()->translatedFormat('F Y') }}" required
                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Tanggal Cut-Off Penggajian *
                    </label>
                    <input type="date" name="tanggal_cutoff" value="{{ now()->format('Y-m-25') }}" required
                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                    <span class="text-[10px] text-slate-400 mt-1 block">Biasanya tanggal 25 setiap bulan</span>
                </div>

                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-900 leading-relaxed">
                    Sistem akan secara otomatis menghitung Simpanan Wajib (Rp 50.000) dan tagihan angsuran pinjaman aktif seluruh anggota POLTEK CWE untuk periode tersebut.
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="openModal = false" class="px-4 py-2 border border-slate-200 rounded-xl text-xs font-bold text-slate-600">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow transition">
                        Generate Sekarang &rarr;
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

