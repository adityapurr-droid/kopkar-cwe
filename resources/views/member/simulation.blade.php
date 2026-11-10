@extends('layouts.member')

@section('title', 'Simulasi & Pengajuan Pinjaman')

@section('content')
<div x-data="{
        nominal: 5000000,
        tenor: 10,
        bungaPersen: {{ $bungaPersen }},
        maxPlafon: {{ $maxPlafon }},
        maxTenor: {{ $tenorMaksimal }},
        keperluan: '',
        showConfirm: false,
        
        get angsuranPokok() {
            return this.tenor > 0 ? Math.round(this.nominal / this.tenor) : 0;
        },
        get angsuranBunga() {
            return Math.round(this.nominal * (this.bungaPersen / 100));
        },
        get totalBunga() {
            return this.angsuranBunga * this.tenor;
        },
        get totalAngsuranPerBulan() {
            return this.angsuranPokok + this.angsuranBunga;
        },
        get totalPinjaman() {
            return Number(this.nominal) + this.totalBunga;
        },
        formatRupiah(num) {
            return 'Rp ' + Number(num).toLocaleString('id-ID');
        }
     }" 
     class="space-y-6">

    {{-- Header --}}
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
        <h2 class="text-xl font-extrabold text-slate-900">Kalkulator Simulasi Pinjaman</h2>
        <p class="text-xs text-slate-500 mt-1">
            Hitung perkiraan angsuran potongan gaji bulanan dengan suku bunga flat <strong>{{ $bungaPersen }}% per bulan</strong>. Batas plafon Anda sebagai <strong>{{ $member->position?->nama ?? 'Anggota' }}</strong> adalah <strong>Rp {{ number_format($maxPlafon, 0, ',', '.') }}</strong>.
        </p>
    </div>

    {{-- Interactive Calculator Inputs & Live Summary --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        {{-- Controls --}}
        <div class="lg:col-span-7 bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-6">
            
            {{-- Nominal Input & Slider --}}
            <div>
                <div class="flex justify-between items-center mb-2">
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Nominal Pinjaman</label>
                    <span class="text-sm font-extrabold text-emerald-700" x-text="formatRupiah(nominal)"></span>
                </div>
                
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-slate-400">Rp</span>
                    <input type="number" x-model.number="nominal" :max="maxPlafon" min="500000" step="500000"
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:bg-white transition">
                </div>

                <input type="range" x-model.number="nominal" min="500000" :max="maxPlafon" step="500000"
                       class="w-full mt-3 accent-emerald-700 cursor-pointer">

                <div class="flex justify-between text-[11px] text-slate-400 mt-1">
                    <span>Min: Rp 500.000</span>
                    <span>Maks Plafon: <strong class="text-slate-700" x-text="formatRupiah(maxPlafon)"></strong></span>
                </div>
            </div>

            {{-- Tenor Input & Slider --}}
            <div>
                <div class="flex justify-between items-center mb-2">
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Jangka Waktu (Tenor)</label>
                    <span class="text-sm font-extrabold text-emerald-700"><span x-text="tenor"></span> Bulan</span>
                </div>

                <input type="range" x-model.number="tenor" min="1" :max="maxTenor" step="1"
                       class="w-full accent-emerald-700 cursor-pointer">

                <div class="grid grid-cols-5 gap-2 mt-2">
                    <template x-for="t in [3, 6, 10, 12, maxTenor]">
                        <button type="button" @click="tenor = t"
                                :class="tenor === t ? 'bg-emerald-700 text-white font-bold' : 'bg-slate-50 text-slate-600 hover:bg-slate-100'"
                                class="py-1.5 px-2 rounded-xl text-xs border border-slate-200 transition text-center">
                            <span x-text="t + ' Bln'"></span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Policy Note --}}
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-900 flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <p class="font-bold">Ketentuan Pemotongan Gaji:</p>
                    <p class="mt-0.5 text-amber-800 leading-relaxed">
                        Angsuran pinjaman akan otomatis dipotong langsung dari slip gaji bulanan Anda oleh bagian Payroll POLTEK CWE bersamaan dengan Simpanan Wajib rutin (Rp 50.000).
                    </p>
                </div>
            </div>

        </div>

        {{-- Live Result Card --}}
        <div class="lg:col-span-5 bg-gradient-to-b from-slate-900 to-emerald-950 text-white rounded-3xl p-6 shadow-xl flex flex-col justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-400 bg-emerald-900/60 px-3 py-1 rounded-full border border-emerald-700/50">
                    Estimasi Potongan Gaji
                </span>

                <div class="mt-6">
                    <p class="text-xs text-slate-300">Total Potongan per Bulan</p>
                    <h3 class="text-3xl font-black text-amber-400 mt-1" x-text="formatRupiah(totalAngsuranPerBulan)"></h3>
                    <p class="text-[11px] text-emerald-200/70 mt-1">selama <span x-text="tenor"></span> bulan</p>
                </div>

                <div class="mt-6 pt-5 border-t border-emerald-800/60 space-y-3 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-300">Pokok Pinjaman:</span>
                        <span class="font-bold text-white" x-text="formatRupiah(nominal)"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-300">Cicilan Pokok / Bln:</span>
                        <span class="font-bold text-white" x-text="formatRupiah(angsuranPokok)"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-300">Bunga (<span x-text="bungaPersen"></span>% Flat) / Bln:</span>
                        <span class="font-bold text-emerald-300" x-text="formatRupiah(angsuranBunga)"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-300">Total Biaya Bunga:</span>
                        <span class="font-bold text-emerald-300" x-text="formatRupiah(totalBunga)"></span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-emerald-800/40 font-bold">
                        <span class="text-amber-300">Total Pengembalian:</span>
                        <span class="text-amber-400" x-text="formatRupiah(totalPinjaman)"></span>
                    </div>
                </div>
            </div>

            {{-- Form Pengajuan Pinjaman --}}
            <div class="mt-6 pt-4 border-t border-emerald-800/60">
                <form action="{{ route('member.loan.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="nominal_pengajuan" :value="nominal">
                    <input type="hidden" name="tenor_bulan" :value="tenor">

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-emerald-300 mb-1">
                            Tujuan / Keperluan Pinjaman:
                        </label>
                        <textarea name="keperluan" required rows="2" 
                                  placeholder="Contoh: Renovasi rumah, biaya perlengkapan sekolah anak..."
                                  class="w-full p-2.5 bg-emerald-900/60 border border-emerald-700/60 rounded-xl text-xs text-white placeholder-emerald-400/50 focus:outline-none focus:ring-2 focus:ring-amber-400"></textarea>
                    </div>

                    <button type="button" 
                            @click="showConfirm = true"
                            class="w-full py-3 bg-amber-400 hover:bg-amber-300 text-emerald-950 font-extrabold text-sm rounded-xl shadow-lg transition active:scale-95">
                        Kirim Formulir Pengajuan &rarr;
                    </button>
                </form>
            </div>

        </div>

    </div>

    {{-- Confirmation Modal --}}
    <div x-show="showConfirm" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 flex items-center justify-center p-4" 
         style="display: none;">
        
        <div @click.away="showConfirm = false" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-gradient-to-br from-emerald-900 to-emerald-950 rounded-3xl max-w-md w-full p-6 shadow-2xl border border-emerald-700/30">
            
            <h3 class="text-lg font-extrabold text-white mb-2">Konfirmasi Pengajuan</h3>
            <p class="text-sm text-emerald-200 mb-6">Apakah Anda yakin ingin mengajukan pinjaman ini untuk diproses pengurus?</p>
            
            <div class="flex gap-3">
                <button @click="showConfirm = false" 
                        class="flex-1 px-4 py-2.5 bg-emerald-800/50 hover:bg-emerald-800 text-white rounded-xl text-sm font-bold transition">
                    Batal
                </button>
                <button @click="$el.closest('div[x-data]').querySelector('form').submit()" 
                        class="flex-1 px-4 py-2.5 bg-amber-400 hover:bg-amber-300 text-emerald-950 rounded-xl text-sm font-extrabold transition">
                    Ya, Ajukan
                </button>
            </div>
        </div>
    </div>

    {{-- Live Amortization Schedule Table Preview --}}
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm overflow-hidden">
        <h4 class="font-bold text-sm text-slate-800 mb-1">Simulasi Rencana Jadwal Angsuran</h4>
        <p class="text-xs text-slate-400 mb-4">Estimasi jadwal pembayaran bulanan jika pinjaman disetujui penuh.</p>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-3">Bulan Ke</th>
                        <th class="py-3 px-3">Angsuran Pokok</th>
                        <th class="py-3 px-3">Angsuran Bunga ({{ $bungaPersen }}%)</th>
                        <th class="py-3 px-3 font-extrabold text-slate-800">Total Potongan Gaji</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    <template x-for="i in tenor" :key="i">
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-2.5 px-3 font-bold" x-text="'Bulan Ke-' + i"></td>
                            <td class="py-2.5 px-3 text-slate-600" x-text="formatRupiah(angsuranPokok)"></td>
                            <td class="py-2.5 px-3 text-emerald-700" x-text="formatRupiah(angsuranBunga)"></td>
                            <td class="py-2.5 px-3 font-bold text-slate-900" x-text="formatRupiah(totalAngsuranPerBulan)"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

