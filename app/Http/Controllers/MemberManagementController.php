<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Division;
use App\Models\Member;
use App\Models\Position;
use App\Models\Saving;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MemberManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = Member::with(['user', 'division', 'position']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nik_karyawan', 'like', "%{$search}%")
                    ->orWhere('no_anggota', 'like', "%{$search}%");
            });
        }

        if ($request->filled('division_id')) {
            $query->where('division_id', $request->division_id);
        }

        if ($request->filled('position_id')) {
            $query->where('position_id', $request->position_id);
        }

        if ($request->filled('status')) {
            $query->where('status_keanggotaan', $request->status);
        }

        $members = $query->latest()->paginate(15)->withQueryString();
        $divisions = Division::orderBy('nama')->get();
        $positions = Position::orderBy('nama')->get();

        return view('staff.members.index', compact('members', 'divisions', 'positions'));
    }

    public function create(): View
    {
        $divisions = Division::orderBy('nama')->get();
        $positions = Position::orderBy('nama')->get();
        $simpananPokokNominal = (float) Setting::get('simpanan_pokok_nominal', 100000);

        return view('staff.members.create', compact('divisions', 'positions', 'simpananPokokNominal'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'nik_karyawan' => ['required', 'string', 'max:50', 'unique:members,nik_karyawan'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'division_id' => ['nullable', 'exists:divisions,id'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'tanggal_bergabung' => ['required', 'date'],
            'bayar_simpanan_pokok' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->nama_lengkap,
                'username' => strtolower($request->username),
                'email' => strtolower($request->email),
                'password' => Hash::make($request->password),
                'role' => 'anggota',
                'is_active' => true,
            ]);

            $noAnggota = 'KOP-'.str_pad((string) (Member::count() + 1), 3, '0', STR_PAD_LEFT);

            $member = Member::create([
                'user_id' => $user->id,
                'nik_karyawan' => $request->nik_karyawan,
                'no_anggota' => $noAnggota,
                'nama_lengkap' => $request->nama_lengkap,
                'division_id' => $request->division_id,
                'position_id' => $request->position_id,
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat,
                'tanggal_bergabung' => $request->tanggal_bergabung,
                'status_keanggotaan' => 'aktif',
            ]);

            if ($request->boolean('bayar_simpanan_pokok')) {
                $nominalPokok = (float) Setting::get('simpanan_pokok_nominal', 100000);
                Saving::create([
                    'member_id' => $member->id,
                    'jenis_simpanan' => 'pokok',
                    'tipe' => 'masuk',
                    'nominal' => $nominalPokok,
                    'tanggal_transaksi' => $request->tanggal_bergabung,
                    'metode_bayar' => 'tunai',
                    'keterangan' => 'Simpanan Pokok Awal Anggota',
                    'created_by' => auth()->id(),
                ]);
            }

            AuditLog::record(
                'Anggota',
                'CREATE',
                "Pendaftaran anggota baru: {$member->nama_lengkap} ({$noAnggota})",
                null,
                $member->toArray()
            );
        });

        return redirect()->route('staff.members.index')
            ->with('success', 'Data anggota berhasil ditambahkan.');
    }

    public function show(Member $member): View
    {
        $member->load(['user', 'division', 'position', 'savings' => function ($q) {
            $q->latest('tanggal_transaksi');
        }, 'loans.installments']);

        $divisions = Division::orderBy('nama')->get();
        $positions = Position::orderBy('nama')->get();

        return view('staff.members.show', compact('member', 'divisions', 'positions'));
    }

    public function update(Request $request, Member $member): RedirectResponse
    {
        $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'nik_karyawan' => ['required', 'string', 'max:50', Rule::unique('members')->ignore($member->id)],
            'division_id' => ['nullable', 'exists:divisions,id'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'tanggal_bergabung' => ['required', 'date'],
            'status_keanggotaan' => ['required', 'in:aktif,nonaktif,keluar'],
        ]);

        $sebelum = $member->toArray();

        $member->update([
            'nama_lengkap' => $request->nama_lengkap,
            'nik_karyawan' => $request->nik_karyawan,
            'division_id' => $request->division_id,
            'position_id' => $request->position_id,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'tanggal_bergabung' => $request->tanggal_bergabung,
            'status_keanggotaan' => $request->status_keanggotaan,
        ]);

        // Update user name and status
        $member->user->update([
            'name' => $request->nama_lengkap,
            'is_active' => $request->status_keanggotaan === 'aktif',
        ]);

        AuditLog::record(
            'Anggota',
            'UPDATE',
            "Pembaruan profil anggota: {$member->nama_lengkap}",
            $sebelum,
            $member->toArray()
        );

        return back()->with('success', 'Data anggota berhasil diperbarui.');
    }
}
