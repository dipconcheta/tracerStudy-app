<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CekPeran
{
    /**
     * Periksa apakah pengguna memiliki peran yang diizinkan.
     *
     * Contoh pemakaian di route:
     *   ->middleware('cek.peran:admin')
     *   ->middleware('cek.peran:alumni')
     *   ->middleware('cek.peran:admin,alumni')
     */
    public function handle(Request $request, Closure $next, string ...$peran): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $penggunaPeran = Auth::user()->peran;

        if (! in_array($penggunaPeran, $peran)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Akun nonaktif
        if (! Auth::user()->aktif) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Akun Anda dinonaktifkan.']);
        }

        return $next($request);
    }
}
