@extends('layouts.staff')

@section('title', 'Tambah Anggota Baru')
@section('page_title', 'Pendaftaran Anggota Koperasi')
@section('page_description', 'Membuat akun pengguna dan profil anggota baru POLTEK CWE')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('staff.members.index') }}" class="text-xs font-bold text-slate-500 hover:text-emerald-700 transition">
            &larr; Kembali ke Daftar Anggota
        </a>
    </div>

    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm">
        
        <form action="{{ route('staff.members.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">Informasi Kepegawaian & Pribadi</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap & Gelar *</label>
                        <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required placeholder="Contoh: Ir. Budi Santoso, M.T."
                               class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-600 focus:bg-white transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">NIK Karyawan POLTEK CWE *</label>
                        <input type="text" name="nik_karyawan" value="{{ old('nik_karyawan') }}" required placeholder="Contoh: CWE-2024-105"
                               class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-600 focus:bg-white transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Divisi / Bagian</label>
                        <select name="division_id" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                            <option value="">-- Pilih Divisi --</option>
                            @foreach($divisions as $div)
                                <option value="{{ $div->id }}" {{ old('division_id') == $div->id ? 'selected' : '' }}>{{ $div->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jabatan (Menentukan Batas Plafon Pinjaman)</label>
                        <select name="position_id" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                            <option value="">-- Pilih Jabatan --</option>
                            @foreach($positions as $pos)
                                <option value="{{ $pos->id }}" {{ old('position_id') == $pos->id ? 'selected' : '' }}>
                                    {{ $pos->nama }} (Plafon: Rp {{ number_format((float) $pos->plafon_pinjaman, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">No. Handphone / WhatsApp</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx"
                               class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Bergabung *</label>
                        <input type="date" name="tanggal_bergabung" value="{{ old('tanggal_bergabung', now()->toDateString()) }}" required
                               class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Domisili</label>
                        <textarea name="alamat" rows="2" placeholder="Alamat lengkap..." class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">{{ old('alamat') }}</textarea>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">Akun Login Portal Anggota</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Username *</label>
                        <input type="text" name="username" value="{{ old('username') }}" required placeholder="budi.santoso"
                               class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="email@poltekcwe.ac.id"
                               class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kata Sandi Awal *</label>
                        <input type="text" name="password" value="{{ old('password', 'password') }}" required
                               class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono">
                    </div>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="bayar_simpanan_pokok" value="1" checked class="w-4 h-4 text-emerald-600 rounded border-slate-300">
                    <div>
                        <span class="text-xs font-bold text-emerald-950 block">Otomatis Catat Pembayaran Simpanan Pokok Awal</span>
                        <span class="text-[11px] text-emerald-800">Nominal: <strong>Rp {{ number_format($simpananPokokNominal, 0, ',', '.') }}</strong> (Sesuai Konfigurasi Koperasi)</span>
                    </div>
                </label>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('staff.members.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-md transition">
                    Simpan Anggota Baru &rarr;
                </button>
            </div>
        </form>

    </div>

</div>
@endsection

