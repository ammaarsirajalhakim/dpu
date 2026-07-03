<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporans';

    protected $fillable = [
        'id_penugasan',
        'user_id',
        'status',
        'teks_laporan',
        'file_final_path'
    ];

    public function penugasan()
    {
        return $this->belongsTo(Penugasan::class, 'id_penugasan');
    }

    public function files()
    {
        return $this->hasMany(LaporanFile::class, 'id_laporan');
    }

    public function chats()
    {
        return $this->hasMany(LaporanRevisiChat::class, 'id_laporan');
    }
}
