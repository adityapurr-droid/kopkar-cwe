@extends('layouts.staff')

@section('title', 'Konfigurasi Koperasi')
@section('page_title', 'Konfigurasi Sistem Koperasi')
@section('page_description', 'Atur suku bunga flat pinjaman, nominal simpanan pokok & wajib, serta kebijakan operasional')

@section('content')
<div class="max-w-4xl space-y-6">

    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm">
        
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
            @csrf

            {{-- 1. Parameter Pinjaman --}}
            <div>
                <div class="flex items-center gap-3 pb-3 border-b border-slate-100 mb-4">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm">
                        %
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm text-slate-900">Kebijakan Suku Bunga & Pinjaman</h3>
                        <p class="text-[11px] text-slate-400">Aturan perhitungan bunga flat dan tenor maksimal pinjaman karyawan</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Suku Bunga Pinjaman (% Flat per Bulan) *
                        </label>
                        <div class="relative">
                            <input type="number" step="0.01" min="0" max="100" name="bunga_pinjaman_persen" 
                                   value="{{ old('bunga_pinjaman_persen', $settings['bunga_pinjaman_persen']->value ?? '1.00') }}" required
                                   class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 pr-10">
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs font-bold text-slate-400">% / bln</span>
                        </div>
                        <span class="text-[10px] text-slate-400 mt-1 block">Contoh: 1.00 untuk bunga 1% flat setiap bulan</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Batas Tenor Maksimal (Bulan) *
                        </label>
                        <div class="relative">
                            <input type="number" min="1" max="120" name="tenor_maksimal_bulan" 
                                   value="{{ old('tenor_maksimal_bulan', $settings['tenor_maksimal_bulan']->value ?? '24') }}" required
                                   class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 pr-12">
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs font-bold text-slate-400">Bulan</span>
                        </div>
                        <span class="text-[10px] text-slate-400 mt-1 block">Maksimal lama cicilan yang dapat dipilih anggota</span>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Plafon Pinjaman Standar / Default (Rp) *
                        </label>
                        <input type="number" min="0" step="500000" name="default_plafon_pinjaman" 
                               value="{{ old('default_plafon_pinjaman', $settings['default_plafon_pinjaman']->value ?? '10000000') }}" required
                               class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                        <span class="text-[10px] text-slate-400 mt-1 block">Digunakan jika anggota tidak memiliki jabatan khusus dengan batas plafon tersendiri.</span>
                    </div>
                </div>
            </div>

            {{-- 2. Parameter Simpanan --}}
            <div>
                <div class="flex items-center gap-3 pb-3 border-b border-slate-100 mb-4">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                        Rp
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm text-slate-900">Ketentuan Nominal Simpanan</h3>
                        <p class="text-[11px] text-slate-400">Besaran simpanan pokok pendaftaran dan simpanan wajib bulanan payroll</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Simpanan Wajib Rutin (Rp / Bulan) *
                        </label>
                        <input type="number" min="0" step="5000" name="simpanan_wajib_nominal" 
                               value="{{ old('simpanan_wajib_nominal', $settings['simpanan_wajib_nominal']->value ?? '50000') }}" required
                               class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-emerald-800">
                        <span class="text-[10px] text-emerald-700 mt-1 block">Dipotong langsung dari gaji bulanan POLTEK CWE (Default: Rp 50.000)</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Simpanan Pokok Awal (Rp Sekali Bayar) *
                        </label>
                        <input type="number" min="0" step="10000" name="simpanan_pokok_nominal" 
                               value="{{ old('simpanan_pokok_nominal', $settings['simpanan_pokok_nominal']->value ?? '100000') }}" required
                               class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                        <span class="text-[10px] text-slate-400 mt-1 block">Dibayarkan 1x saat pertama kali bergabung menjadi anggota</span>
                    </div>
                </div>
            </div>

            {{-- 3. Identitas Organisasi --}}
            <div>
                <div class="flex items-center gap-3 pb-3 border-b border-slate-100 mb-4">
                    <div class="w-8 h-8 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-sm">
                        🏢
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm text-slate-900">Identitas Lembaga Koperasi</h3>
                        <p class="text-[11px] text-slate-400">Nama resmi yang dicetak pada laporan, slip, dan ekspor payroll</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Organisasi Koperasi *</label>
                        <input type="text" name="nama_koperasi" 
                               value="{{ old('nama_koperasi', $settings['nama_koperasi']->value ?? 'Koperasi Karyawan Politeknik Kelapa Sawit Citra Widya Edukasi') }}" required
                               class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Singkatan / Kode Brand *</label>
                        <input type="text" name="singkatan_koperasi" 
                               value="{{ old('singkatan_koperasi', $settings['singkatan_koperasi']->value ?? 'KOPKAR POLTEK CWE') }}" required
                               class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold">
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow-md transition">
                    Simpan Konfigurasi &rarr;
                </button>
            </div>
        </form>

    </div>

</div>
@endsection

