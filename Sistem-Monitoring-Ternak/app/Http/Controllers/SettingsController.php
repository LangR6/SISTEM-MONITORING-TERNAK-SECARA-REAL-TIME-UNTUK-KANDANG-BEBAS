<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    /**
     * Menampilkan halaman pengaturan.
     */
    public function index(Request $request): View
    {
        $tab = $request->query('tab') === 'password'
            ? 'password'
            : 'profile';

        /** @var User $authenticatedUser */
        $authenticatedUser = $request->user();

        // Siapkan data dalam bentuk array agar sesuai dengan Blade.
        $user = [
            'name' => $authenticatedUser->name,
            'email' => $authenticatedUser->email,
            'phone' => $authenticatedUser->phone,
            'address' => $authenticatedUser->address,
            'role' => $authenticatedUser->role ?? 'Peternak',
        ];

        return view('pages.settings', [
            'tab' => $tab,
            'user' => $user,
        ]);
    }

    /**
     * Menyimpan perubahan data diri.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],
            'address' => [
                'nullable',
                'string',
                'max:200',
            ],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 150 karakter.',
            'email.unique' => 'Email sudah digunakan akun lain.',
            'phone.max' => 'Nomor telepon maksimal 30 karakter.',
            'address.max' => 'Alamat maksimal 200 karakter.',
        ]);

        // Simpan data ke database.
        $user->update($data);

        return redirect()
            ->route('settings', ['tab' => 'profile'])
            ->with('status', 'Data diri berhasil disimpan.');
    }

    /**
     * Mengubah password pengguna.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $data = $request->validate([
            'current_password' => [
                'required',
                'current_password',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->letters()->numbers(),
                'different:current_password',
            ],
        ], [
            'current_password.required' =>
                'Password saat ini wajib diisi.',
            'current_password.current_password' =>
                'Password saat ini tidak sesuai.',
            'password.required' =>
                'Password baru wajib diisi.',
            'password.confirmed' =>
                'Konfirmasi password tidak cocok.',
            'password.min' =>
                'Password minimum 8 karakter.',
            'password.letters' =>
                'Password harus mengandung huruf.',
            'password.numbers' =>
                'Password harus mengandung angka.',
            'password.different' =>
                'Password baru harus berbeda dari password saat ini.',
        ]);

        // Simpan password baru.
        // Model User menggunakan casts 'hashed'.
        $user->update([
            'password' => $data['password'],
        ]);

        return redirect()
            ->route('settings', ['tab' => 'password'])
            ->with('status', 'Password berhasil diubah.');
    }
}
