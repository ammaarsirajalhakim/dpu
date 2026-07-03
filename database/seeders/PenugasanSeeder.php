<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Penugasan;
use App\Models\AnggotaPenugasan;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;

class PenugasanSeeder extends Seeder
{
    public function run(): void
    {
        $tugas = Tugas::first(); 
        $admin = User::where('role', 'admin')->first() ?? User::first();
        $users = User::where('role', '!=', 'admin')->get();

        if (!$tugas || (!$admin) || $users->isEmpty()) {
            $this->command->info('Data referensi (Tugas/User) belum lengkap. Tidak dapat membuat seeder penugasan.');
            return;
        }

        $penugasan = Penugasan::create([
            'kodetugas'         => $tugas->kodetugas,
            'id_admin'          => $admin->nip, // UBAH KE NIP
            'batas_waktu_lapor' => Carbon::now()->addDays(7), 
        ]);

        if (isset($users[0])) {
            AnggotaPenugasan::create([
                'id_penugasan' => $penugasan->id,
                'id_user'      => $users[0]->nip, // UBAH KE NIP
            ]);
        }

        if (isset($users[1])) {
            AnggotaPenugasan::create([
                'id_penugasan' => $penugasan->id,
                'id_user'      => $users[1]->nip, // UBAH KE NIP
            ]);
        }
    }
}