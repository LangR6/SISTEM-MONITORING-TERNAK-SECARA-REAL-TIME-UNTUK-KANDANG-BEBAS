<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /** Akun demonstrasi (ditampilkan di halaman login saat APP_ENV=local). Ganti sebelum produksi. */
    public const DEMO_EMAIL = 'budi.santoso@email.com';

    public const DEMO_PASSWORD = 'password123';

    public function run(): void
    {
        User::updateOrCreate(
            ['email' => self::DEMO_EMAIL],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make(self::DEMO_PASSWORD),
            ],
        );
    }
}
