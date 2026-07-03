<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // 1. Update/Buat 1 Superadmin
        User::updateOrCreate(
            ['nip' => '199001012024011001'],
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@test.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'superadmin',
            ]
        );

        // 2. Buat 4 Admin
        for ($i = 1; $i <= 4; $i++) {
            User::updateOrCreate(
                ['nip' => $faker->unique()->numerify('##################')], // 18 digit NIP acak
                [
                    'name' => $faker->name,
                    'email' => "admin{$i}@test.com",
                    'email_verified_at' => now(),
                    'password' => Hash::make('password123'),
                    'role' => 'admin',
                ]
            );
        }

        // 3. Buat 7 User Biasa (Sisa dari total 10 user baru + 2 user lama yang disesuaikan)
        for ($i = 1; $i <= 7; $i++) {
            User::updateOrCreate(
                ['nip' => $faker->unique()->numerify('##################')],
                [
                    'name' => $faker->name,
                    'email' => $faker->unique()->safeEmail,
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                    'role' => 'user',
                ]
            );
        }
    }
}