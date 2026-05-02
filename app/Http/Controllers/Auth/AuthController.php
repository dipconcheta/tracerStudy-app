<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // ── Tampilkan Form Login ─────────────────────────────────────────────────

    public function tampilkanLogin()
    {
        if (Auth::check()) {
            return $this->redirectSesuaiPeran();
        }
        return view('auth.login');
    }

    // ── Proses Login ─────────────────────────────────────────────────────────

    public function prosesLogin(Request $request)
    {
        $request->validate([
            'email'      => ['required', 'email'],
            'kata_sandi' => ['required'],
        ], [
            'email.required'      => 'Email wajib diisi.',
            'email.email'         => 'Format email tidak valid.',
            'kata_sandi.required' => 'Kata sandi wajib diisi.',
        ]);

        $kredensial = [
            'email'    => $request->email,
            'password' => $request->kata_sandi,   // Auth::attempt maps via getAuthPassword()
        ];

        $User = User::where('email', $request->email)->first();

        if (! $User || ! Hash::check($request->kata_sandi, $User->kata_sandi)) {
            return back()->withErrors(['email' => 'Email atau kata sandi salah.'])->withInput();
        }

        if (! $User->aktif) {
            return back()->withErrors(['email' => 'Akun Anda dinonaktifkan. Hubungi admin.'])->withInput();
        }

        Auth::login($User, $request->boolean('ingat_saya'));
        $request->session()->regenerate();

        return $this->redirectSesuaiPeran();
    }

    // ── Tampilkan Form Daftar (Alumni) ───────────────────────────────────────

    public function tampilkanDaftar()
    {
        return view('auth.daftar');
    }

    // ── Proses Daftar ────────────────────────────────────────────────────────

    public function prosesDaftar(Request $request)
    {
        $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'unique:User,email'],
            'kata_sandi'   => ['required', 'confirmed', Password::min(8)],
        ], [
            'nama_lengkap.required'  => 'Nama lengkap wajib diisi.',
            'email.required'         => 'Email wajib diisi.',
            'email.unique'           => 'Email sudah terdaftar.',
            'kata_sandi.required'    => 'Kata sandi wajib diisi.',
            'kata_sandi.confirmed'   => 'Konfirmasi kata sandi tidak cocok.',
            'kata_sandi.min'         => 'Kata sandi minimal 8 karakter.',
        ]);

        $User = User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'email'        => $request->email,
            'kata_sandi'   => Hash::make($request->kata_sandi),
            'peran'        => 'alumni',
        ]);

        Auth::login($User);
        $request->session()->regenerate();

        return redirect()->route('alumni.form')->with('sukses', 'Akun berhasil dibuat! Silakan lengkapi data Anda.');
    }

    // ── Logout ───────────────────────────────────────────────────────────────

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // ── Helper ───────────────────────────────────────────────────────────────

    private function redirectSesuaiPeran()
    {
        return Auth::user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('alumni.dashboard');
    }
}
