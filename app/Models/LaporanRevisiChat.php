<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanRevisiChat extends Model
{
    use HasFactory;

    protected $table = 'laporan_revisi_chats';

    protected $fillable = [
        'id_laporan', 
        'id_user', // Kolom ini sekarang menyimpan NIP (string)
        'pesan', 
        'is_from_admin_panel',
    ];

    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'id_laporan');
    }

    // Relasi ke tabel user untuk mengetahui siapa pengirim pesannya
    public function user()
    {
        // PERUBAHAN: Tambahkan parameter ketiga 'nip' di sini
        return $this->belongsTo(User::class, 'id_user', 'nip');
    }
}