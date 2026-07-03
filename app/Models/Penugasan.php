<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penugasan extends Model
{
    use HasFactory;

    protected $table = 'penugasan';

    protected $fillable = ['kodetugas', 'id_admin', 'batas_waktu_lapor'];

    public function tugas()
    {
        return $this->belongsTo(Tugas::class, 'kodetugas', 'kodetugas');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'id_admin', 'nip');
    }

    public function anggota()
    {
        return $this->hasMany(AnggotaPenugasan::class, 'id_penugasan');
    }

    public function laporan()
    {
        return $this->hasOne(Laporan::class, 'id_penugasan');
    }

    public function dailyProgressReports()
    {
        return $this->hasMany(DailyProgressReport::class, 'id_penugasan');
    }
}
