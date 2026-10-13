<?php

namespace App\Http\Controllers;

use App\Support\DemoUser;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->query('tab') === 'password' ? 'password' : 'profile';

        return view('pages.settings', ['tab' => $tab, 'user' => DemoUser::current()]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:200'],
        ]);

        DemoUser::update($data);

        return redirect()->route('settings', ['tab' => 'profile'])->with('status', 'Perubahan berhasil disimpan');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        // Belum ada autentikasi, jadi password lama hanya divalidasi keberadaannya.
        // Setelah memakai auth, ganti dengan aturan 'current_password' dan
        // simpan hash password baru ke user.
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimum 8 karakter.',
            'password.letters' => 'Password harus mengandung huruf.',
            'password.numbers' => 'Password harus mengandung angka.',
        ]);

        return redirect()->route('settings', ['tab' => 'password'])->with('status', 'Perubahan berhasil disimpan');
    }
}
