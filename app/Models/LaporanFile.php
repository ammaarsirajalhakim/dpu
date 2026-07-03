<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanFile extends Model
{
    use HasFactory;

    protected $table = 'laporan_files';

    protected $fillable = [
        'id_laporan', 
        'file_name', 
        'file_path'
    ];

    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'id_laporan');
    }
}