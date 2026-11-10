@extends('layouts.staff')

@section('title', 'Master Divisi & Jabatan')
@section('page_title', 'Master Divisi & Jabatan Karyawan')
@section('page_description', 'Kelola struktur organisasi POLTEK CWE dan batas plafon pinjaman masing-masing jabatan')

@section('content')
<div class="space-y-8" x-data="{
    showDeletePos: false,
    showDeleteDiv: false,
    deleteId: null,
    deleteType: null,
    confirmDelete(type, id) {
        this.deleteType = type;
        this.deleteId = id;
        if (type === 'position') this.showDeletePos = true;
        else this.showDeleteDiv = true;
    },
    submitDelete() {
        const form = document.getElementById('deleteForm');
        const input = document.getElementById('deleteId');
        const inputType = document.getElementById('deleteType');
        
        if (this.deleteId && this.deleteType) {
            input.value = this.deleteId;
            inputType.value = this.deleteType;
            form.submit();
        }
    }
}">

    {{-- Section 1: Jabatan & Plafon Pinjaman --}}
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <h3 class="font-extrabold text-base text-slate-900">Jabatan & Plafon Pinjaman Maksimal</h3>
                <p class="text-xs text-slate-500">Batas maksimal pinjaman disesuaikan berdasarkan jabatan karyawan di POLTEK CWE.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            {{-- Form Tambah Jabatan --}}
            <div class="lg:col-span-4 p-5 rounded-2xl bg-slate-50 border border-slate-200/80">
                <h4 class="font-bold text-xs uppercase tracking-wider text-slate-700 mb-3">Tambah Jabatan Baru</h4>
                <form action="{{ route('admin.positions.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Nama Jabatan *</label>
                        <input type="text" name="nama" required placeholder="Contoh: Dosen Tetap" 
                               class="w-full p-2 bg-white border border-slate-200 rounded-xl text-xs">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Plafon Maksimal Pinjaman (Rp) *</label>
                        <input type="number" name="plafon_pinjaman" required min="0" step="500000" placeholder="15000000" 
                               class="w-full p-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Deskripsi Singkat</label>
                        <input type="text" name="deskripsi" placeholder="Keterangan..." 
                               class="w-full p-2 bg-white border border-slate-200 rounded-xl text-xs">
                    </div>
                    <button type="submit" class="w-full py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow transition">
                        Simpan Jabatan &rarr;
                    </button>
                </form>
            </div>

            {{-- Table Jabatan --}}
            <div class="lg:col-span-8 overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-3">Nama Jabatan</th>
                            <th class="py-3 px-3">Plafon Maksimal Pinjaman</th>
                            <th class="py-3 px-3">Anggota</th>
                            <th class="py-3 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($positions as $pos)
                            <tr x-data="{ editing: false, nama: '{{ addslashes($pos->nama) }}', plafon: {{ (float) $pos->plafon_pinjaman }}, deskripsi: '{{ addslashes($pos->deskripsi ?? '') }}' }" 
                                class="hover:bg-slate-50 transition">
                                
                                <td class="py-3 px-3">
                                    <template x-if="!editing">
                                        <div>
                                            <span class="font-bold text-slate-900 block" x-text="nama"></span>
                                            <span class="text-[10px] text-slate-400" x-text="deskripsi || '-'"></span>
                                        </div>
                                    </template>
                                    <template x-if="editing">
                                        <input type="text" x-model="nama" class="w-full p-1.5 border rounded-lg text-xs">
                                    </template>
                                </td>

                                <td class="py-3 px-3">
                                    <template x-if="!editing">
                                        <span class="font-black text-emerald-700 text-sm">
                                            Rp <span x-text="Number(plafon).toLocaleString('id-ID')"></span>
                                        </span>
                                    </template>
                                    <template x-if="editing">
                                        <input type="number" x-model.number="plafon" step="500000" class="w-full p-1.5 border rounded-lg text-xs font-bold">
                                    </template>
                                </td>

                                <td class="py-3 px-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                        {{ $pos->members_count }} orang
                                    </span>
                                </td>

                                <td class="py-3 px-3 text-right whitespace-nowrap">
                                    <template x-if="!editing">
                                        <div class="flex justify-end gap-1">
                                            <button @click="editing = true" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold">
                                                Edit
                                            </button>
                                            @if($pos->members_count === 0)
                                                <button type="button" 
                                                        @click="confirmDelete('position', '{{ $pos->id }}')"
                                                        class="px-2 py-1 text-rose-600 hover:underline text-[11px] font-bold">
                                                    Hapus
                                                </button>
                                            @endif
                                        </div>
                                    </template>
                                    <template x-if="editing">
                                        <form :action="'{{ url('admin/jabatan') }}/{{ $pos->id }}'" method="POST" class="inline-flex gap-1">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="nama" :value="nama">
                                            <input type="hidden" name="plafon_pinjaman" :value="plafon">
                                            <input type="hidden" name="deskripsi" :value="deskripsi">
                                            <button type="submit" class="px-2 py-1 bg-emerald-700 text-white rounded text-[11px] font-bold">Simpan</button>
                                            <button type="button" @click="editing = false" class="px-2 py-1 bg-slate-200 text-slate-700 rounded text-[11px]">Batal</button>
                                        </form>
                                    </template>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    {{-- Section 2: Master Divisi --}}
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <h3 class="font-extrabold text-base text-slate-900">Divisi & Unit Kerja Kampus POLTEK CWE</h3>
                <p class="text-xs text-slate-500">Pengelompokan divisi kerja dosen, riset perkebunan, dan staf administrasi.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            {{-- Form Tambah Divisi --}}
            <div class="lg:col-span-4 p-5 rounded-2xl bg-slate-50 border border-slate-200/80">
                <h4 class="font-bold text-xs uppercase tracking-wider text-slate-700 mb-3">Tambah Divisi Baru</h4>
                <form action="{{ route('admin.divisions.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Nama Divisi *</label>
                        <input type="text" name="nama" required placeholder="Contoh: Divisi Akademik" 
                               class="w-full p-2 bg-white border border-slate-200 rounded-xl text-xs">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Kode Singkatan</label>
                        <input type="text" name="kode" placeholder="AKD" 
                               class="w-full p-2 bg-white border border-slate-200 rounded-xl text-xs font-mono uppercase">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Deskripsi</label>
                        <input type="text" name="deskripsi" placeholder="Tugas & fungsi divisi..." 
                               class="w-full p-2 bg-white border border-slate-200 rounded-xl text-xs">
                    </div>
                    <button type="submit" class="w-full py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow transition">
                        Simpan Divisi &rarr;
                    </button>
                </form>
            </div>

            {{-- Table Divisi --}}
            <div class="lg:col-span-8 overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-3">Kode</th>
                            <th class="py-3 px-3">Nama Divisi</th>
                            <th class="py-3 px-3">Deskripsi</th>
                            <th class="py-3 px-3">Anggota</th>
                            <th class="py-3 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($divisions as $div)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-3 font-mono font-bold text-emerald-800">{{ $div->kode ?: '-' }}</td>
                                <td class="py-3 px-3 font-bold text-slate-900">{{ $div->nama }}</td>
                                <td class="py-3 px-3 text-slate-500">{{ $div->deskripsi ?: '-' }}</td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                        {{ $div->members_count }} orang
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right">
                                    @if($div->members_count === 0)
                                        <form action="{{ route('admin.divisions.destroy', $div) }}" method="POST" onsubmit="return confirm('Hapus divisi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:underline text-[11px] font-bold">Hapus</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    {{-- Hidden Delete Forms --}}
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    {{-- Confirmation Modals --}}
    <div x-show="showDeletePos || showDeleteDiv" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 flex items-center justify-center p-4" 
         style="display: none;">
        
        <div @click.away="showDeletePos = false; showDeleteDiv = false" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-rose-200">
            
            <h3 class="text-lg font-extrabold text-slate-900 mb-2">Konfirmasi Hapus</h3>
            <p class="text-sm text-slate-600 mb-6">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
            
            <div class="flex gap-3">
                <button @click="showDeletePos = false; showDeleteDiv = false" 
                        class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-bold transition">
                    Batal
                </button>
                <button @click="
                        const form = document.getElementById('deleteForm');
                        form.action = deleteType === 'position' 
                            ? '{{ route('admin.positions.index') }}/' + deleteId 
                            : '{{ route('admin.divisions.index') }}/' + deleteId;
                        form.submit();" 
                        class="flex-1 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-sm font-extrabold transition">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

