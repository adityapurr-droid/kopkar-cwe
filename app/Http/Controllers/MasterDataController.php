<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Division;
use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterDataController extends Controller
{
    public function index(): View
    {
        $divisions = Division::withCount('members')->orderBy('nama')->get();
        $positions = Position::withCount('members')->orderBy('nama')->get();

        return view('admin.master.index', compact('divisions', 'positions'));
    }

    public function storeDivision(Request $request): RedirectResponse
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'kode' => ['nullable', 'string', 'max:20', 'unique:divisions,kode'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $division = Division::create($request->all());

        AuditLog::record('Master Data', 'CREATE', "Menambahkan divisi: {$division->nama}");

        return back()->with('success', "Divisi {$division->nama} berhasil ditambahkan.");
    }

    public function updateDivision(Request $request, Division $division): RedirectResponse
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'kode' => ['nullable', 'string', 'max:20', 'unique:divisions,kode,'.$division->id],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $division->update($request->all());

        AuditLog::record('Master Data', 'UPDATE', "Memperbarui divisi: {$division->nama}");

        return back()->with('success', "Divisi {$division->nama} berhasil diperbarui.");
    }

    public function destroyDivision(Division $division): RedirectResponse
    {
        if ($division->members()->exists()) {
            return back()->with('error', 'Divisi tidak dapat dihapus karena masih digunakan oleh data anggota.');
        }

        $nama = $division->nama;
        $division->delete();

        AuditLog::record('Master Data', 'DELETE', "Menghapus divisi: {$nama}");

        return back()->with('success', "Divisi {$nama} berhasil dihapus.");
    }

    public function storePosition(Request $request): RedirectResponse
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'plafon_pinjaman' => ['required', 'numeric', 'min:0'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $position = Position::create($request->all());

        AuditLog::record('Master Data', 'CREATE', "Menambahkan jabatan: {$position->nama} (Plafon: Rp ".number_format((float) $position->plafon_pinjaman, 0, ',', '.').')');

        return back()->with('success', "Jabatan {$position->nama} berhasil ditambahkan.");
    }

    public function updatePosition(Request $request, Position $position): RedirectResponse
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'plafon_pinjaman' => ['required', 'numeric', 'min:0'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $position->update($request->all());

        AuditLog::record('Master Data', 'UPDATE', "Memperbarui jabatan: {$position->nama} (Plafon: Rp ".number_format((float) $position->plafon_pinjaman, 0, ',', '.').')');

        return back()->with('success', "Jabatan {$position->nama} berhasil diperbarui.");
    }

    public function destroyPosition(Position $position): RedirectResponse
    {
        if ($position->members()->exists()) {
            return back()->with('error', 'Jabatan tidak dapat dihapus karena masih digunakan oleh data anggota.');
        }

        $nama = $position->nama;
        $position->delete();

        AuditLog::record('Master Data', 'DELETE', "Menghapus jabatan: {$nama}");

        return back()->with('success', "Jabatan {$nama} berhasil dihapus.");
    }
}
