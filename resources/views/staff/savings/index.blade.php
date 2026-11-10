@extends('layouts.staff')

@section('title', 'Kelola Simpanan')
@section('page_title', 'Kelola Simpanan Anggota')
@section('page_description', 'Pencatatan simpanan pokok, simpanan wajib, dan simpanan sukarela')

@section('content')
<div x-data="{ 
    openModal: false,
    selectAll: false
}" class="space-y-6">

    {{-- Metric Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Simpanan Masuk</span>
            <h3 class="text-2xl font-black text-emerald-700 mt-1">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Penarikan (Sukarela)</span>
            <h3 class="text-2xl font-black text-rose-600 mt-1">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-gradient-to-r from-emerald-800 to-teal-900 rounded-3xl p-5 text-white shadow-lg">
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-200">Saldo Bersih Kas Simpanan</span>
            <h3 class="text-2xl font-black text-amber-300 mt-1">Rp {{ number_format($saldoTotal, 0, ',', '.') }}</h3>
        </div>
    </div>

    {{-- Filter & Action Bar --}}
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        
        <form action="{{ route('staff.savings.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1" id="filterForm">
            <select name="member_id" onchange="this.form.submit()" class="py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 min-w-[200px]">
                <option value="">Semua Anggota</option>
                @foreach($members as $m)
                    <option value="{{ $m->id }}" {{ request('member_id') == $m->id ? 'selected' : '' }}>{{ $m->no_anggota }} - {{ $m->nama_lengkap }}</option>
                @endforeach
            </select>

            <select name="jenis_simpanan" onchange="this.form.submit()" class="py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700">
                <option value="">Semua Jenis</option>
                <option value="pokok" {{ request('jenis_simpanan') == 'pokok' ? 'selected' : '' }}>Pokok</option>
                <option value="wajib" {{ request('jenis_simpanan') == 'wajib' ? 'selected' : '' }}>Wajib</option>
                <option value="sukarela" {{ request('jenis_simpanan') == 'sukarela' ? 'selected' : '' }}>Sukarela</option>
            </select>

            <select name="tipe" onchange="this.form.submit()" class="py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700">
                <option value="">Semua Tipe</option>
                <option value="masuk" {{ request('tipe') == 'masuk' ? 'selected' : '' }}>Masuk (Setor)</option>
                <option value="keluar" {{ request('tipe') == 'keluar' ? 'selected' : '' }}>Keluar (Tarik)</option>
            </select>

            @if(request()->anyFilled(['member_id', 'jenis_simpanan', 'tipe', 'tanggal_dari']))
                <a href="{{ route('staff.savings.index') }}" class="text-xs text-rose-600 font-bold hover:underline">Reset</a>
            @endif
        </form>

        <button @click="openModal = true" class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            <span>Catat Transaksi Simpanan</span>
        </button>

    </div>

    {{-- Transactions Table --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Anggota</th>
                        <th class="py-3 px-4">Jenis Simpanan</th>
                        <th class="py-3 px-4">Metode & Keterangan</th>
                        <th class="py-3 px-4 text-right">Nominal</th>
                        <th class="py-3 px-4 text-right">Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($savings as $s)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-semibold text-slate-900 whitespace-nowrap">
                                {{ $s->tanggal_transaksi->format('d M Y') }}
                            </td>
                            <td class="py-3 px-4">
                                <a href="{{ route('staff.members.show', $s->member) }}" class="font-bold text-slate-900 hover:text-emerald-700 block">
                                    {{ $s->member->nama_lengkap }}
                                </a>
                                <span class="text-[11px] text-slate-400">{{ $s->member->no_anggota }} &bull; {{ $s->member->division?->nama ?? '-' }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $s->jenis_simpanan === 'pokok' ? 'bg-amber-100 text-amber-900' : ($s->jenis_simpanan === 'wajib' ? 'bg-emerald-100 text-emerald-900' : 'bg-teal-100 text-teal-900') }}">
                                    {{ $s->jenis_simpanan }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-slate-800 font-semibold block">{{ ucfirst(str_replace('_', ' ', $s->metode_bayar)) }}</span>
                                <span class="text-[11px] text-slate-500">{{ $s->keterangan ?: '-' }}</span>
                            </td>
                            <td class="py-3 px-4 text-right font-extrabold whitespace-nowrap {{ $s->tipe === 'masuk' ? 'text-emerald-700' : 'text-rose-600' }}">
                                {{ $s->tipe === 'masuk' ? '+' : '-' }} Rp {{ number_format((float) $s->nominal, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right text-slate-400 text-[11px] whitespace-nowrap">
                                {{ $s->creator?->name ?? 'Sistem' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-slate-400">Belum ada transaksi simpanan ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $savings->links() }}
        </div>
    </div>

    {{-- Modal Tambah Transaksi Simpanan --}}
    <div x-show="openModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 flex items-center justify-center p-4" 
         style="display: none;">
        
        <div @click.away="openModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl space-y-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-extrabold text-slate-900">Catat Transaksi Simpanan Baru</h3>
                <button @click="openModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>

            <form action="{{ route('staff.savings.store.batch') }}" method="POST" class="space-y-4">
                @csrf

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Anggota *</label>
                        <label class="flex items-center gap-2 text-xs font-bold text-emerald-700 cursor-pointer">
                            <input type="checkbox" x-model="selectAll" name="select_all" value="1" class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <span>Pilih Semua Anggota Aktif</span>
                        </label>
                    </div>
                    <div x-show="!selectAll">
                        <select name="member_id" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                            <option value="">-- Pilih Anggota --</option>
                            @foreach($members as $m)
                                <option value="{{ $m->id }}">{{ $m->no_anggota }} - {{ $m->nama_lengkap }} ({{ $m->nik_karyawan }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jenis Simpanan *</label>
                        <select name="jenis_simpanan" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                            <option value="sukarela">Simpanan Sukarela</option>
                            <option value="pokok">Simpanan Pokok</option>
                            <option value="wajib">Simpanan Wajib</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tipe Transaksi *</label>
                        <select name="tipe" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                            <option value="masuk">Setoran (Masuk)</option>
                            <option value="keluar">Penarikan (Keluar)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nominal (Rp) *</label>
                        <input type="number" name="nominal" min="1000" step="1000" required placeholder="Contoh: 100000"
                               class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Transaksi *</label>
                        <input type="date" name="tanggal_transaksi" value="{{ now()->toDateString() }}" required
                               class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Metode Pembayaran *</label>
                    <select name="metode_bayar" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                        <option value="tunai">Tunai / Kasir</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="potong_gaji">Pemotongan Gaji</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Keterangan (Opsional)</label>
                    <input type="text" name="keterangan" placeholder="Keterangan transaksi..."
                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="openModal = false" class="px-4 py-2 border border-slate-200 rounded-xl text-xs font-bold text-slate-600">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow transition">
                        Simpan Transaksi &rarr;
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

