<?php

namespace App\Models;

use Carbon\Carbon;
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

    public function getStatusPenugasanLabelAttribute(): string
    {
        if ($this->finalReportApproved()) {
            return 'Selesai';
        }

        if (!$this->tugas || !$this->tugas->tanggal_mulai || !$this->tugas->tanggal_selesai) {
            return 'Data Tugas Tidak Lengkap';
        }

        $today = Carbon::today();
        $start = Carbon::parse($this->tugas->tanggal_mulai)->startOfDay();
        $end = Carbon::parse($this->tugas->tanggal_selesai)->startOfDay();

        if ($today->lt($start)) {
            return 'Akan Datang';
        }

        if ($today->betweenIncluded($start, $end)) {
            return 'Aktif';
        }

        return 'Terlewat: Belum Lapor';
    }

    public function getStatusPenugasanClassAttribute(): string
    {
        $status = $this->status_penugasan_label;

        if ($status === 'Selesai') {
            return 'bg-green-100 text-green-700 border border-green-200';
        }

        if ($status === 'Akan Datang') {
            return 'bg-gray-100 text-gray-700 border border-gray-200';
        }

        if ($status === 'Aktif') {
            return 'bg-blue-100 text-blue-700 border border-blue-200';
        }

        if (str_starts_with($status, 'Terlewat')) {
            return 'bg-red-100 text-red-700 border border-red-200';
        }

        return 'bg-yellow-100 text-yellow-700 border border-yellow-200';
    }

    private function finalReportApproved(): bool
    {
        if ($this->relationLoaded('laporan')) {
            return optional($this->laporan)->status === 'disetujui';
        }

        return $this->laporan()
            ->where('status', 'disetujui')
            ->exists();
    }
}