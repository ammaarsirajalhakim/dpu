<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TugasSeeder extends Seeder
{
    public function run(): void
    {
        // Gunakan NIP Superadmin yang dibuat di UserSeeder
        $adminNip = '199001012024011001';

        DB::table('tugas')->insert([
            [
                'kodetugas' => 'KGD' . strtoupper(Str::random(5)),
                'nama_tugas' => 'Audit Inventaris Bulanan',
                'deskripsi' => 'Melakukan pengecekan fisik barang di gudang utama.',
                'lampiran' => 'panduan_audit.pdf',
                'tanggal_mulai' => Carbon::now(),
                'tanggal_selesai' => Carbon::now()->addDays(7),
                'id_admin' => $adminNip, // PERUBAHAN: Menggunakan NIP
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kodetugas' => 'KGD' . strtoupper(Str::random(5)),
                'nama_tugas' => 'Input Barang Masuk Vendor A',
                'deskripsi' => 'Memasukkan data pengiriman dari Vendor A ke sistem.',
                'lampiran' => null,
                'tanggal_mulai' => Carbon::now()->addDay(),
                'tanggal_selesai' => Carbon::now()->addDays(3),
                'id_admin' => $adminNip, // PERUBAHAN: Menggunakan NIP
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}