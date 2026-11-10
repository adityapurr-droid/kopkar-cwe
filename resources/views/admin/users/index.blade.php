@extends('layouts.staff')

@section('title', 'Kelola User')
@section('page_title', 'Kelola Pengguna Sistem')
@section('page_description', 'Manajemen akun Administrator, Pengurus Keuangan, dan Anggota Koperasi')

@section('content')
<div x-data="{ openAdd: false, openReset: false, resetUserId: null, resetUserName: '' }" class="space-y-6">

    {{-- Filter & Action Bar --}}
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Cari nama, username, email..." 
                   class="py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs min-w-[200px]">

            <select name="role" onchange="this.form.submit()" class="py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700">
                <option value="">Semua Peran (Role)</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="pengurus" {{ request('role') == 'pengurus' ? 'selected' : '' }}>Pengurus</option>
                <option value="anggota" {{ request('role') == 'anggota' ? 'selected' : '' }}>Anggota</option>
            </select>

            @if(request()->anyFilled(['search', 'role']))
                <a href="{{ route('admin.users.index') }}" class="text-xs text-rose-600 font-bold hover:underline">Reset</a>
            @endif
        </form>

        <button @click="openAdd = true" class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            <span>Tambah Pengguna</span>
        </button>
    </div>

    {{-- Users Table --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-4">Nama & Username</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4">Peran (Role)</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Terdaftar</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @foreach($users as $user)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-900 block">{{ $user->name }}</span>
                                <span class="text-[11px] text-slate-400 font-mono">@<span>{{ $user->username }}</span></span>
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ $user->email }}
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $roleColors = [
                                        'admin' => 'bg-amber-100 text-amber-900',
                                        'pengurus' => 'bg-emerald-100 text-emerald-900',
                                        'anggota' => 'bg-sky-100 text-sky-900',
                                    ];
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $roleColors[$user->role] ?? 'bg-slate-100' }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @if($user->is_active)
                                    <span class="inline-flex items-center gap-1 text-[11px] text-emerald-700 font-bold">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[11px] text-rose-600 font-bold">
                                        <span class="w-2 h-2 rounded-full bg-rose-500"></span> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-400">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="py-3 px-4 text-right">
                                <button type="button" 
                                        @click="openReset = true; resetUserId = {{ $user->id }}; resetUserName = '{{ addslashes($user->name) }}'"
                                        class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-amber-50 hover:text-amber-800 text-slate-700 text-xs font-bold transition">
                                    🔑 Reset Sandi
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $users->links() }}
        </div>
    </div>

    {{-- Modal Tambah User --}}
    <div x-show="openAdd" class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="openAdd = false" class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl space-y-4">
            <h3 class="text-base font-extrabold text-slate-900 pb-2 border-b border-slate-100">Tambah Akun Pengguna</h3>

            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Lengkap *</label>
                    <input type="text" name="name" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Username *</label>
                    <input type="text" name="username" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email *</label>
                    <input type="email" name="email" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Peran (Role) *</label>
                    <select name="role" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                        <option value="pengurus">Pengurus (Keuangan Koperasi)</option>
                        <option value="admin">Admin (Sistem)</option>
                        <option value="anggota">Anggota</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kata Sandi *</label>
                    <input type="password" name="password" required minlength="6" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="openAdd = false" class="px-4 py-2 border rounded-xl text-xs font-bold">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-700 text-white rounded-xl text-xs font-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Reset Password --}}
    <div x-show="openReset" class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="openReset = false" class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl space-y-4">
            <h3 class="text-base font-extrabold text-slate-900 pb-2 border-b border-slate-100">Reset Sandi Pengguna</h3>
            <p class="text-xs text-slate-500">Masukkan kata sandi baru untuk <strong class="text-slate-800" x-text="resetUserName"></strong>:</p>

            <form :action="'{{ url('admin/users') }}/' + resetUserId + '/reset-password'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kata Sandi Baru *</label>
                    <input type="text" name="password" required minlength="6" value="password123" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono">
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="openReset = false" class="px-4 py-2 border rounded-xl text-xs font-bold">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-amber-600 text-white rounded-xl text-xs font-bold">Reset Sekarang</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

