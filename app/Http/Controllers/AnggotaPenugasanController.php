<?php

namespace App\Http\Controllers;

use App\Models\AnggotaPenugasan;
use Illuminate\Http\Request;

class AnggotaPenugasanController extends Controller
{
    // Menambahkan anggota ke suatu penugasan
    public function store(Request $request)
    {
        // Validasi id_jabatan telah dihapus
        $request->validate([
            'id_penugasan' => 'required|exists:penugasan,id',
            'id_user'      => 'required|exists:users,nip',
        ]);

        // Cek agar user tidak ditambahkan 2x di penugasan yang sama
        $exists = AnggotaPenugasan::where('id_penugasan', $request->id_penugasan)
                                  ->where('id_user', $request->id_user)
                                  ->first();

        if ($exists) {
            return back()->with('error', 'User tersebut sudah ada di dalam penugasan ini!');
        }

        // Penyimpanan id_jabatan telah dihapus
        AnggotaPenugasan::create([
            'id_penugasan' => $request->id_penugasan,
            'id_user'      => $request->id_user,
        ]);

        return back()->with('success', 'Anggota berhasil ditambahkan ke penugasan.');
    }

    // Menghapus anggota dari penugasan
    public function destroy($id)
    {
        $anggota = AnggotaPenugasan::findOrFail($id);
        $anggota->delete();

        return back()->with('success', 'Anggota berhasil dihapus dari penugasan.');
    }
}