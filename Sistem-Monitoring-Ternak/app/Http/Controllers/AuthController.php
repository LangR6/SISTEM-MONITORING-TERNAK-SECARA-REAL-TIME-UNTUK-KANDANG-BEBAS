<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /** Halaman login (hanya untuk tamu; pengguna yang sudah masuk dialihkan oleh middleware "guest"). */
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Cegah session fixation: ganti ID session setelah berhasil masuk.
        $request->session()->regenerate();

        // Kembali ke halaman yang tadi dituju sebelum diarahkan ke login (bila ada).
        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('status', 'Anda berhasil keluar dari sistem.');
    }
}
