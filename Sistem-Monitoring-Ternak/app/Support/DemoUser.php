<?php

namespace App\Support;

/**
 * Pengguna contoh. Aplikasi ini belum memakai autentikasi; profil disimpan di
 * session. Setelah autentikasi dipasang (Breeze/Fortify), ganti current()
 * dengan auth()->user() dan simpan perubahan profil ke database.
 */
class DemoUser
{
    private const SESSION_KEY = 'profile';

    private const DEFAULTS = [
        'name' => 'Budi Santoso',
        'role' => 'Peternak',
        'email' => 'budi.santoso@email.com',
        'phone' => '0812 3456 7890',
        'address' => 'Desa Sukamaju, Kepulauan Riau',
    ];

    public static function current(): array
    {
        return [...self::DEFAULTS, ...(array) session(self::SESSION_KEY, [])];
    }

    /** @param array<string, string|null> $data */
    public static function update(array $data): void
    {
        session([self::SESSION_KEY => array_merge(
            (array) session(self::SESSION_KEY, []),
            array_intersect_key($data, array_flip(['name', 'email', 'phone', 'address']))
        )]);
    }
}
