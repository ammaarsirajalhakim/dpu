<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnggotaPenugasan extends Model
{
    use HasFactory;

    protected $table = 'anggota_penugasans';

    protected $fillable = [
        'id_penugasan',
        'id_user', // Kolom ini sekarang menyimpan NIP (string)
        'status_keterlambatan',
        'alasan_keterlambatan',
        'custom_deadline',
    ];

    /**
     * Relasi ke model Penugasan
     */
    public function penugasan()
    {
        return $this->belongsTo(Penugasan::class, 'id_penugasan');
    }

    /**
     * Relasi ke model User
     */
    public function user()
    {
        // PERUBAHAN: Tambahkan parameter ketiga 'nip' untuk menggantikan referensi 'id' default
        return $this->belongsTo(User::class, 'id_user', 'nip');
    }
}