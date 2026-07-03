<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    use HasFactory;

    protected $table = 'tugas';

    // Sesuaikan primary key ke kodetugas
    protected $primaryKey = 'kodetugas';

    // Matikan incrementing karena primary key adalah string, bukan integer auto-increment
    public $incrementing = false;

    // Set tipe data primary key menjadi string
    protected $keyType = 'string';

    // Kolom apa saja yang boleh diisi
    protected $fillable = [
        'kodetugas',
        'nama_tugas',
        'deskripsi',
        'lampiran',
        'tanggal_mulai',
        'tanggal_selesai',
        'id_admin',
    ];

    // Relasi ke User (Admin)
    public function admin()
    {
        // PERUBAHAN: Ubah parameter ketiga dari 'id' menjadi 'nip'
        return $this->belongsTo(User::class, 'id_admin', 'nip');
    }
}