@extends('layouts.staff')

@section('title', 'Detail Anggota ' . $member->nama_lengkap)
@section('page_title', 'Profil Anggota')
@section('page_description', 'Detail data anggota ' . $member->no_anggota . ' (' . $member->nik_karyawan . ')')

@section('content')
<div x-data="{ tab: 'profil' }" class="space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('staff.members.index') }}" class="text-xs font-bold text-slate-500 hover:text-emerald-700 transition">
            &larr; Kembali ke Daftar Anggota
        </a>
        <span class="text-xs text-slate-400">Terdaftar sejak: {{ $member->tanggal_bergabung->format('d M Y') }}</span>
    </div>

    {{-- Quick Profile Card --}}
    <div class="bg-gradient-to-r from-emerald-900 to-teal-900 rounded-3xl p-6 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-800/80 rounded-full text-xs font-semibold text-emerald-200 mb-2">
                <span>No. Anggota:</span>
                <strong class="text-white">{{ $member->no_anggota }}</strong>
                &bull;
                <span>Status:</span>
                <strong class="text-white uppercase">{{ $member->status_keanggotaan }}</strong>
            </div>
            <h2 class="text-2xl font-black">{{ $member->nama_lengkap }}</h2>
            <p class="text-xs text-emerald-200 mt-1">
                NIK: {{ $member->nik_karyawan }} &bull; {{ $member->position?->nama ?? '-' }} &bull; {{ $member->division?->nama ?? '-' }}
            </p>
        </div>

        <div class="flex items-center gap-4 text-center border-t md:border-t-0 md:border-l border-emerald-800/60 pt-4 md:pt-0 md:pl-6">
            <div>
                <span class="text-[10px] text-emerald-300 block uppercase">Total Saldo Simpanan</span>
                <span class="text-xl font-black text-amber-300">Rp {{ number_format($member->totalSaldoSimpanan(), 0, ',', '.') }}</span>
            </div>
            <div class="border-l border-emerald-800/60 pl-4">
                <span class="text-[10px] text-emerald-300 block uppercase">Sisa Hutang Pinjaman</span>
                <span class="text-xl font-black text-rose-300">Rp {{ number_format($member->sisaHutangPinjaman(), 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex items-center gap-2 border-b border-slate-200">
        <button type="button" @click="tab = 'profil'"
                :class="tab === 'profil' ? 'border-emerald-700 text-emerald-800 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800 font-medium'"
                class="py-3 px-4 text-xs border-b-2 transition">
            Edit Profil Anggota
        </button>
        <button type="button" @click="tab = 'simpanan'"
                :class="tab === 'simpanan' ? 'border-emerald-700 text-emerald-800 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800 font-medium'"
                class="py-3 px-4 text-xs border-b-2 transition">
            Mutasi Simpanan ({{ $member->savings->count() }})
        </button>
        <button type="button" @click="tab = 'pinjaman'"
                :class="tab === 'pinjaman' ? 'border-emerald-700 text-emerald-800 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800 font-medium'"
                class="py-3 px-4 text-xs border-b-2 transition">
            Pinjaman ({{ $member->loans->count() }})
        </button>
    </div>

    {{-- Tab 1: Edit Profile --}}
    <div x-show="tab === 'profil'" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm">
        <form action="{{ route('staff.members.update', $member) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap & Gelar</label>
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $member->nama_lengkap) }}" required
                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">NIK Karyawan POLTEK CWE</label>
                    <input type="text" name="nik_karyawan" value="{{ old('nik_karyawan', $member->nik_karyawan) }}" required
                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Divisi</label>
                    <select name="division_id" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                        <option value="">-- Tanpa Divisi --</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ old('division_id', $member->division_id) == $div->id ? 'selected' : '' }}>{{ $div->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jabatan & Plafon</label>
                    <select name="position_id" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                        <option value="">-- Tanpa Jabatan --</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}" {{ old('position_id', $member->position_id) == $pos->id ? 'selected' : '' }}>
                                {{ $pos->nama }} (Plafon: Rp {{ number_format((float) $pos->plafon_pinjaman, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">No. Handphone / WA</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $member->no_hp) }}"
                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status Keanggotaan</label>
                    <select name="status_keanggotaan" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                        <option value="aktif" {{ old('status_keanggotaan', $member->status_keanggotaan) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status_keanggotaan', $member->status_keanggotaan) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        <option value="keluar" {{ old('status_keanggotaan', $member->status_keanggotaan) == 'keluar' ? 'selected' : '' }}>Keluar</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Bergabung</label>
                    <input type="date" name="tanggal_bergabung" value="{{ old('tanggal_bergabung', $member->tanggal_bergabung->toDateString()) }}" required
                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat</label>
                    <textarea name="alamat" rows="2" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">{{ old('alamat', $member->alamat) }}</textarea>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow-md transition">
                    Simpan Perubahan &rarr;
                </button>
            </div>
        </form>
    </div>

    {{-- Tab 2: Simpanan --}}
    <div x-show="tab === 'simpanan'" style="display: none;" class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm overflow-hidden">
        <h4 class="font-bold text-sm text-slate-800 mb-4">Riwayat Simpanan Anggota</h4>
        
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-3">Tanggal</th>
                        <th class="py-3 px-3">Jenis</th>
                        <th class="py-3 px-3">Keterangan</th>
                        <th class="py-3 px-3">Metode</th>
                        <th class="py-3 px-3 text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($member->savings as $s)
                        <tr>
                            <td class="py-2.5 px-3">{{ $s->tanggal_transaksi->format('d M Y') }}</td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $s->jenis_simpanan === 'pokok' ? 'bg-amber-100 text-amber-900' : ($s->jenis_simpanan === 'wajib' ? 'bg-emerald-100 text-emerald-900' : 'bg-teal-100 text-teal-900') }}">
                                    {{ $s->jenis_simpanan }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-slate-600">{{ $s->keterangan }}</td>
                            <td class="py-2.5 px-3">{{ ucfirst(str_replace('_', ' ', $s->metode_bayar)) }}</td>
                            <td class="py-2.5 px-3 text-right font-extrabold {{ $s->tipe === 'masuk' ? 'text-emerald-700' : 'text-rose-600' }}">
                                {{ $s->tipe === 'masuk' ? '+' : '-' }} Rp {{ number_format((float) $s->nominal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-400">Belum ada transaksi simpanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tab 3: Pinjaman --}}
    <div x-show="tab === 'pinjaman'" style="display: none;" class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm overflow-hidden">
        <h4 class="font-bold text-sm text-slate-800 mb-4">Daftar Pinjaman Anggota</h4>

        <div class="space-y-4">
            @forelse($member->loans as $l)
                <div class="p-4 rounded-2xl border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-extrabold text-slate-900">{{ $l->no_pinjaman }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100">{{ $l->status }}</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">
                            Plafon Disetujui: <strong>Rp {{ number_format((float) $l->nominal_disetujui, 0, ',', '.') }}</strong> &bull; Tenor: {{ $l->tenor_bulan }} Bln &bull; Angsuran: Rp {{ number_format((float) $l->total_angsuran_perbulan, 0, ',', '.') }}/bln
                        </p>
                    </div>
                    <a href="{{ route('staff.loans.show', $l) }}" class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 font-bold text-xs hover:bg-emerald-100 transition">
                        Kelola Pinjaman &rarr;
                    </a>
                </div>
            @empty
                <p class="text-xs text-slate-400 py-6 text-center">Anggota ini belum pernah mengajukan pinjaman.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection

