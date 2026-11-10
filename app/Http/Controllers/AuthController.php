<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$loginField => $credentials['login'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $user = Auth::user();

            if (! $user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors(['login' => 'Akun Anda dinonaktifkan oleh Administrator.']);
            }

            $request->session()->regenerate();

            AuditLog::record('Auth', 'LOGIN', 'Pengguna '.$user->name.' ('.$user->role.') berhasil login.');

            return $this->redirectBasedOnRole()->with('success', 'Selamat datang kembali, '.$user->name.'!');
        }

        return back()->withErrors([
            'login' => 'Username/Email atau password yang Anda masukkan salah.',
        ])->onlyInput('login');
    }

    public function logout(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            AuditLog::record('Auth', 'LOGOUT', 'Pengguna '.Auth::user()->name.' logout dari sistem.');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah berhasil keluar dari sistem.');
    }

    protected function redirectBasedOnRole(): RedirectResponse
    {
        $user = Auth::user();

        if ($user->hasStaffAccess()) {
            return redirect()->route('staff.dashboard');
        }

        return redirect()->route('member.dashboard');
    }
}
