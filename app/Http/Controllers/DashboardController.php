<?php

namespace App\Http\Controllers;

use App\Models\AnggotaPenugasan;
use App\Models\DailyProgressReport;
use App\Models\Laporan;
use App\Models\Penugasan;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUser = User::count();
        $totalTugas = Tugas::count();
        $totalPenugasan = Penugasan::count();

        $menungguReview = Laporan::where('status', 'diajukan')->count();
        $selesaiBulanIni = Laporan::where('status', 'disetujui')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();
        $totalLaporanHarian = DailyProgressReport::count();
        $permohonanPerpanjangan = AnggotaPenugasan::where('status_keterlambatan', 'mengajukan')->count();

        $kodeTugasDitugaskan = Penugasan::pluck('kodetugas')->unique()->values();

        $kodeTugasSelesai = Penugasan::whereHas('laporan', function ($query) {
                $query->where('status', 'disetujui');
            })
            ->pluck('kodetugas')
            ->unique()
            ->values();

        $kodeTugasProses = Penugasan::whereHas('laporan', function ($query) {
                $query->whereIn('status', ['diajukan', 'revisi']);
            })
            ->whereNotIn('kodetugas', $kodeTugasSelesai)
            ->pluck('kodetugas')
            ->unique()
            ->values();

        $kodeTugasTerlambat = Penugasan::whereDoesntHave('laporan')
            ->whereNotIn('kodetugas', $kodeTugasSelesai)
            ->whereNotIn('kodetugas', $kodeTugasProses)
            ->where('batas_waktu_lapor', '<', now())
            ->pluck('kodetugas')
            ->unique()
            ->values();

        $kodeTugasBaruDitugaskan = Penugasan::whereDoesntHave('laporan')
            ->whereNotIn('kodetugas', $kodeTugasSelesai)
            ->whereNotIn('kodetugas', $kodeTugasProses)
            ->whereNotIn('kodetugas', $kodeTugasTerlambat)
            ->where('batas_waktu_lapor', '>=', now())
            ->pluck('kodetugas')
            ->unique()
            ->values();

        $totalSelesai = $kodeTugasSelesai->count();
        $totalProses = $kodeTugasProses->count();
        $totalTerlambat = $kodeTugasTerlambat->count();
        $totalDitugaskan = $kodeTugasBaruDitugaskan->count();
        $belumDitugaskan = Tugas::whereNotIn('kodetugas', $kodeTugasDitugaskan)->count();
        $tugasAktif = $totalProses + $totalDitugaskan + $totalTerlambat;

        $statusPenugasan = [
            'Selesai' => $totalSelesai,
            'Proses Review' => $totalProses,
            'Terlambat' => $totalTerlambat,
            'Ditugaskan' => $totalDitugaskan,
            'Belum Ditugaskan' => $belumDitugaskan,
        ];

        $statusPenugasanTotal = array_sum($statusPenugasan);

        $urgentTugas = Penugasan::with(['tugas', 'laporan'])
            ->whereDoesntHave('laporan', function ($query) {
                $query->where('status', 'disetujui');
            })
            ->where('batas_waktu_lapor', '<=', now()->addDays(3))
            ->orderBy('batas_waktu_lapor', 'asc')
            ->take(5)
            ->get();

        $timelineTugas = Penugasan::with(['tugas', 'laporan'])
            ->orderBy('batas_waktu_lapor', 'asc')
            ->take(12)
            ->get();

        $laporanBaru = Laporan::with(['penugasan.tugas', 'penugasan.anggota.user'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $trendLabels = [];
        $trendMasuk = [];
        $trendSelesai = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $trendLabels[] = $date->translatedFormat('M Y');

            $trendMasuk[] = Tugas::whereMonth('tanggal_mulai', $date->month)
                ->whereYear('tanggal_mulai', $date->year)
                ->count();

            $trendSelesai[] = Laporan::where('status', 'disetujui')
                ->whereMonth('updated_at', $date->month)
                ->whereYear('updated_at', $date->year)
                ->count();
        }

        return view('admin.dashboardadmin', compact(
            'totalUser',
            'totalTugas',
            'totalPenugasan',
            'tugasAktif',
            'menungguReview',
            'selesaiBulanIni',
            'totalLaporanHarian',
            'permohonanPerpanjangan',
            'urgentTugas',
            'laporanBaru',
            'timelineTugas',
            'totalSelesai',
            'totalProses',
            'totalDitugaskan',
            'totalTerlambat',
            'belumDitugaskan',
            'statusPenugasan',
            'statusPenugasanTotal',
            'trendLabels',
            'trendMasuk',
            'trendSelesai'
        ));
    }

    public function indexUser()
    {
        $userId = Auth::id();

        $penugasanUser = Penugasan::whereHas('anggota', function ($q) use ($userId) {
            $q->where('id_user', $userId);
        })->with(['tugas', 'laporan'])->get();

        $totalTugasDikerjakan = $penugasanUser->count();

        $totalLaporSegera = $penugasanUser->filter(function ($p) {
            $isSelesai = $p->laporan && $p->laporan->status === 'disetujui';
            $isMendesak = Carbon::parse($p->batas_waktu_lapor)->lte(now()->addDay());
            return !$isSelesai && $isMendesak;
        })->count();

        $totalPerluUnggah = $penugasanUser->filter(function ($p) {
            return !$p->laporan || $p->laporan->status === 'revisi';
        })->count();

        $totalSelesai = $penugasanUser->filter(function ($p) {
            return $p->laporan && $p->laporan->status === 'disetujui';
        })->count();

        $rekomendasiPrioritas = $penugasanUser->filter(function ($p) {
            return !$p->laporan || $p->laporan->status !== 'disetujui';
        })->sortBy('batas_waktu_lapor')->take(5);

        $statusPelaporan = $penugasanUser->map(function ($p) {
            $statusText = 'belum diajukan';
            if ($p->laporan) {
                $statusText = $p->laporan->status;
            }

            return [
                'kodetugas' => $p->kodetugas,
                'nama_tugas' => $p->tugas->nama_tugas ?? '-',
                'batas_waktu' => Carbon::parse($p->batas_waktu_lapor)->translatedFormat('d M Y'),
                'status' => $statusText,
            ];
        });

        $tepatWaktu = $penugasanUser->filter(function ($p) {
            if ($p->laporan && $p->laporan->status === 'disetujui') {
                return Carbon::parse($p->laporan->updated_at)->lte(Carbon::parse($p->batas_waktu_lapor));
            }
            return false;
        })->count();

        $rasioAkurasi = $totalSelesai > 0 ? round(($tepatWaktu / $totalSelesai) * 100, 1) : 100;

        return view('dashboard', compact(
            'totalTugasDikerjakan',
            'totalLaporSegera',
            'totalPerluUnggah',
            'totalSelesai',
            'rekomendasiPrioritas',
            'statusPelaporan',
            'rasioAkurasi'
        ));
    }
}
