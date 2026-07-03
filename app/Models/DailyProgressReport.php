<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyProgressReport extends Model
{
    use HasFactory;

    protected $table = 'daily_progress_reports';

    protected $fillable = [
        'id_penugasan',
        'id_user',
        'tanggal_laporan',
        'progres',
        'kendala',
        'rencana_lanjut',
        'file_path',
        'file_name',
    ];

    protected $casts = [
        'tanggal_laporan' => 'date',
    ];

    public function penugasan()
    {
        return $this->belongsTo(Penugasan::class, 'id_penugasan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'nip');
    }
}
