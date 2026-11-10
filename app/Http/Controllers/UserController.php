<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with('member');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:admin,pengurus,anggota'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => strtolower($request->username),
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => true,
        ]);

        AuditLog::record(
            'Pengguna',
            'CREATE',
            "Membuat akun pengguna baru: {$user->name} ({$user->username}) role {$user->role}",
            null,
            $user->toArray()
        );

        return back()->with('success', "Pengguna {$user->name} berhasil ditambahkan.");
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'in:admin,pengurus,anggota'],
            'is_active' => ['required', 'boolean'],
        ]);

        $sebelum = $user->toArray();

        $user->update([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'role' => $request->role,
            'is_active' => $request->is_active,
        ]);

        AuditLog::record(
            'Pengguna',
            'UPDATE',
            "Memperbarui data pengguna: {$user->name}",
            $sebelum,
            $user->toArray()
        );

        return back()->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        AuditLog::record(
            'Pengguna',
            'RESET_PASSWORD',
            "Reset password untuk pengguna {$user->name} ({$user->username})"
        );

        return back()->with('success', "Password untuk pengguna {$user->name} berhasil di-reset.");
    }
}
