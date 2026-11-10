<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = Setting::all()->keyBy('key');

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'simpanan_pokok_nominal' => ['required', 'numeric', 'min:0'],
            'simpanan_wajib_nominal' => ['required', 'numeric', 'min:0'],
            'bunga_pinjaman_persen' => ['required', 'numeric', 'min:0', 'max:100'],
            'tenor_maksimal_bulan' => ['required', 'integer', 'min:1', 'max:120'],
            'default_plafon_pinjaman' => ['required', 'numeric', 'min:0'],
            'nama_koperasi' => ['required', 'string', 'max:255'],
            'singkatan_koperasi' => ['required', 'string', 'max:50'],
        ]);

        $sebelum = Setting::pluck('value', 'key')->toArray();

        foreach ($request->only([
            'simpanan_pokok_nominal',
            'simpanan_wajib_nominal',
            'bunga_pinjaman_persen',
            'tenor_maksimal_bulan',
            'default_plafon_pinjaman',
            'nama_koperasi',
            'singkatan_koperasi',
        ]) as $key => $val) {
            Setting::set($key, $val);
        }

        AuditLog::record(
            'Pengaturan',
            'UPDATE',
            'Pembaruan konfigurasi umum koperasi',
            $sebelum,
            Setting::pluck('value', 'key')->toArray()
        );

        return back()->with('success', 'Konfigurasi koperasi berhasil disimpan.');
    }
}
