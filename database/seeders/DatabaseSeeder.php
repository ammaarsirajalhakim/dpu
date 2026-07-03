<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Daftarkan semua seeder di sini sesuai urutan yang benar
        $this->call([
            UserSeeder::class,
            // JabatanSeeder::class,     // Tambahkan ini
            TugasSeeder::class,       // Tambahkan ini (jika ada)
            PenugasanSeeder::class // Tambahkan ini (jika ada)
        ]);
    }
}