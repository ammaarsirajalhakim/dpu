<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    use HasFactory;

    protected $table = 'tugas';

    protected $primaryKey = 'kodetugas';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'kodetugas',
        'nama_tugas',
        'deskripsi',
        'lampiran',
        'tanggal_mulai',
        'tanggal_selesai',
        'id_admin',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'id_admin', 'nip');
    }

    public function penugasan()
    {
        return $this->hasMany(Penugasan::class, 'kodetugas', 'kodetugas');
    }

    public function getStatusPenugasanLabelAttribute(): string
    {
        if ($this->hasApprovedFinalReport()) {
            return 'Selesai';
        }

        $hasAssignment = $this->hasAssignment();

        if (!$this->tanggal_mulai || !$this->tanggal_selesai) {
            return $hasAssignment
                ? 'Data Tanggal Tidak Lengkap (Ditugaskan)'
                : 'Data Tanggal Tidak Lengkap (Belum Ditugaskan)';
        }

        $today = Carbon::today();
        $start = Carbon::parse($this->tanggal_mulai)->startOfDay();
        $end = Carbon::parse($this->tanggal_selesai)->startOfDay();

        if ($today->lt($start)) {
            return $hasAssignment
                ? 'Akan Datang (Ditugaskan)'
                : 'Akan Datang (Belum Ditugaskan)';
        }

        if ($today->betweenIncluded($start, $end)) {
            return $hasAssignment
                ? 'Aktif (Ditugaskan)'
                : 'Aktif (Belum Ditugaskan)';
        }

        return $hasAssignment
            ? 'Terlewat: Belum Lapor'
            : 'Terlewat: Belum Ditugaskan';
    }

    public function getStatusPenugasanClassAttribute(): string
    {
        $status = $this->status_penugasan_label;

        if ($status === 'Selesai') {
            return 'bg-green-100 text-green-700 border border-green-200';
        }

        if (str_starts_with($status, 'Akan Datang')) {
            return 'bg-gray-100 text-gray-700 border border-gray-200';
        }

        if (str_starts_with($status, 'Aktif')) {
            return 'bg-blue-100 text-blue-700 border border-blue-200';
        }

        if (str_starts_with($status, 'Terlewat')) {
            return 'bg-red-100 text-red-700 border border-red-200';
        }

        return 'bg-yellow-100 text-yellow-700 border border-yellow-200';
    }

    private function hasAssignment(): bool
    {
        if ($this->relationLoaded('penugasan')) {
            return $this->penugasan->isNotEmpty();
        }

        return $this->penugasan()->exists();
    }

    private function hasApprovedFinalReport(): bool
    {
        if ($this->relationLoaded('penugasan')) {
            return $this->penugasan->contains(function ($penugasan) {
                if ($penugasan->relationLoaded('laporan')) {
                    return optional($penugasan->laporan)->status === 'disetujui';
                }

                return $penugasan->laporan()
                    ->where('status', 'disetujui')
                    ->exists();
            });
        }

        return $this->penugasan()
            ->whereHas('laporan', function ($query) {
                $query->where('status', 'disetujui');
            })
            ->exists();
    }
}