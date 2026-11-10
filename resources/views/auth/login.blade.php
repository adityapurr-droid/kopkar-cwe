@extends('layouts.app')

@section('title', 'Login Sistem')

@section('body')
<div class="min-h-screen bg-slate-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center px-4">
        
        {{-- Logo & Brand --}}
        <div class="inline-flex items-center justify-center p-3.5 bg-white rounded-3xl shadow-xl shadow-slate-200/60 mb-4 border border-slate-100">
            <img src="{{ asset('images/logo-cwe.png') }}" alt="Logo Politeknik Citra Widya Edukasi" class="h-20 w-auto object-contain">
        </div>
        
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
            KOPKAR POLTEK CWE
        </h2>
        <p class="mt-2 text-sm text-slate-600 font-medium">
            Koperasi Karyawan Politeknik Kelapa Sawit Citra Widya Edukasi
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4">
        <div class="bg-white py-8 px-6 sm:px-10 shadow-xl shadow-slate-200/50 rounded-3xl border border-slate-100">
            
            <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="login" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Username atau Email
                    </label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <input type="text" name="login" id="login" required autofocus
                               value="{{ old('login') }}"
                               placeholder="admin / budi / siti / email"
                               class="block w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:bg-white transition">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Kata Sandi
                    </label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input type="password" name="password" id="password" required
                               placeholder="••••••••"
                               class="block w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:bg-white transition">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                        <span class="text-xs text-slate-600 font-medium">Ingat Saya</span>
                    </label>
                    <span class="text-xs text-slate-400">Default sandi: <strong class="text-slate-600">password</strong></span>
                </div>

                <div>
                    <button type="submit" 
                            class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-emerald-700 hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition active:scale-[0.99]">
                        Masuk ke Sistem &rarr;
                    </button>
                </div>
            </form>

            {{-- Demo Accounts Quick Fill --}}
            <div class="mt-8 pt-6 border-t border-slate-100">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 text-center mb-3">Akun Demo Pengujian Cepat</p>
                <div class="grid grid-cols-2 gap-2 text-xs" x-data>
                    <button type="button" 
                            @click="document.getElementById('login').value = 'admin'; document.getElementById('password').value = 'password';"
                            class="p-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 text-left transition">
                        <span class="block font-bold">👑 Admin</span>
                        <span class="text-[10px] text-amber-700">admin / password</span>
                    </button>
                    <button type="button" 
                            @click="document.getElementById('login').value = 'pengurus'; document.getElementById('password').value = 'password';"
                            class="p-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-900 border border-emerald-200 text-left transition">
                        <span class="block font-bold">💼 Pengurus</span>
                        <span class="text-[10px] text-emerald-700">pengurus / password</span>
                    </button>
                    <button type="button" 
                            @click="document.getElementById('login').value = 'budi'; document.getElementById('password').value = 'password';"
                            class="p-2 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-900 border border-sky-200 text-left transition">
                        <span class="block font-bold">👨‍🏫 Anggota 1 (Dosen)</span>
                        <span class="text-[10px] text-sky-700">budi / password</span>
                    </button>
                    <button type="button" 
                            @click="document.getElementById('login').value = 'siti'; document.getElementById('password').value = 'password';"
                            class="p-2 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-900 border border-purple-200 text-left transition">
                        <span class="block font-bold">👩‍💻 Anggota 2 (Staf)</span>
                        <span class="text-[10px] text-purple-700">siti / password</span>
                    </button>
                </div>
            </div>

        </div>

        <p class="mt-6 text-center text-xs text-slate-400">
            &copy; {{ now()->year }} Politeknik Kelapa Sawit Citra Widya Edukasi (CWE). All rights reserved.
        </p>
    </div>
</div>
@endsection

