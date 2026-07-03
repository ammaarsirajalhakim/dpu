<?php

namespace App\Http\Controllers;

use App\Models\DailyProgressReport;
use App\Models\Laporan;
use App\Models\LaporanFile;
use App\Models\LaporanRevisiChat;
use App\Models\Penugasan;
use App\Models\AnggotaPenugasan;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $isAdminRoute = $request->routeIs('admin.*') || $request->is('admin/*');

        if ($isAdminRoute && (Auth::user()->role === 'admin' || Auth::user()->role === 'superadmin')) {
            $search = $request->search;

            $query = Laporan::with(['penugasan.tugas', 'penugasan.anggota.user']);

            if ($request->filled('search')) {
                $query->whereHas('penugasan.tugas', function ($q) use ($search) {
                    $q->where('nama_tugas', 'like', '%' . $search . '%')
                      ->orWhere('kodetugas', 'like', '%' . $search . '%');
                })->orWhere('user_id', 'like', '%' . $search . '%');
            }

            $laporans = $query->orderBy('updated_at', 'desc')->paginate(10);

            $dailyReports = DailyProgressReport::with(['penugasan.tugas', 'user'])
                ->when($request->filled('search'), function ($q) use ($search) {
                    $q->whereHas('penugasan.tugas', function ($taskQuery) use ($search) {
                        $taskQuery->where('nama_tugas', 'like', '%' . $search . '%')
                            ->orWhere('kodetugas', 'like', '%' . $search . '%');
                    })->orWhere('id_user', 'like', '%' . $search . '%');
                })
                ->orderBy('tanggal_laporan', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(25)
                ->get();

            $extensionRequests = AnggotaPenugasan::with(['penugasan.tugas', 'user'])
                ->where('status_keterlambatan', 'mengajukan')
                ->when($request->filled('search'), function ($q) use ($search) {
                    $q->whereHas('penugasan.tugas', function ($taskQuery) use ($search) {
                        $taskQuery->where('nama_tugas', 'like', '%' . $search . '%')
                            ->orWhere('kodetugas', 'like', '%' . $search . '%');
                    })->orWhere('id_user', 'like', '%' . $search . '%');
                })
                ->orderBy('updated_at', 'desc')
                ->get();

            return view('admin.laporan', compact('laporans', 'dailyReports', 'extensionRequests'));
        }

        $query = Laporan::with(['penugasan.tugas', 'penugasan.anggota.user']);

        $query->whereHas('penugasan.anggota', function ($q) {
            $q->where('id_user', Auth::user()->nip);
        });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('penugasan.tugas', function ($q) use ($search) {
                $q->where('nama_tugas', 'like', '%' . $search . '%')
                  ->orWhere('kodetugas', 'like', '%' . $search . '%');
            });
        }

        $laporans = $query->orderBy('updated_at', 'desc')->paginate(10);

        return view('laporanuser', compact('laporans'));
    }

    public function create($id_penugasan)
    {
        $penugasan = Penugasan::with(['tugas', 'dailyProgressReports' => function ($query) {
            $query->where('id_user', Auth::user()->nip);
        }])->findOrFail($id_penugasan);

        $anggota = AnggotaPenugasan::where('id_penugasan', $id_penugasan)
            ->where('id_user', Auth::user()->nip)
            ->firstOrFail();

        if (!$this->dailyReportsComplete($penugasan, Auth::user()->nip)) {
            return redirect()->route('penugasan.show', $penugasan->id)
                ->with('error', 'Laporan akhir belum bisa dibuat. Lengkapi semua laporan harian terlebih dahulu.');
        }

        $batas_waktu = $anggota->custom_deadline ?? $penugasan->batas_waktu_lapor ?? $penugasan->batas_lapor ?? ($penugasan->tugas->tanggal_selesai ?? now());
        $is_waktu_habis = now()->greaterThan(Carbon::parse($batas_waktu));

        return view('buatlaporan', compact('penugasan', 'anggota', 'is_waktu_habis', 'batas_waktu'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_penugasan' => 'required|exists:penugasan,id',
            'teks_laporan' => 'required|string',
            'files.*' => 'file|max:5120',
            'file_laporan.*' => 'file|max:5120'
        ]);

        $penugasan = Penugasan::with(['tugas', 'dailyProgressReports' => function ($query) {
            $query->where('id_user', Auth::user()->nip);
        }])->findOrFail($request->id_penugasan);

        if (!$this->dailyReportsComplete($penugasan, Auth::user()->nip)) {
            return redirect()->route('penugasan.show', $penugasan->id)
                ->with('error', 'Laporan akhir ditolak sistem karena laporan harian belum lengkap.');
        }

        $deadline = Carbon::parse($penugasan->batas_waktu_lapor);
        if (now()->greaterThan($deadline)) {
            return redirect()->route('penugasan.show', $penugasan->id)
                ->with('error', 'Batas waktu laporan akhir sudah lewat. Ajukan perpanjangan waktu terlebih dahulu.');
        }

        DB::beginTransaction();
        try {
            $laporan = Laporan::updateOrCreate(
                ['id_penugasan' => $request->id_penugasan],
                [
                    'user_id' => Auth::user()->nip,
                    'teks_laporan' => $request->teks_laporan,
                    'status' => 'diajukan'
                ]
            );

            $uploadedFiles = [];
            if ($request->hasFile('files')) {
                $uploadedFiles = $request->file('files');
            } elseif ($request->hasFile('file_laporan')) {
                $uploadedFiles = $request->file('file_laporan');
            }

            foreach ($uploadedFiles as $file) {
                $path = $file->store('laporan_files', 'public');
                LaporanFile::create([
                    'id_laporan' => $laporan->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName()
                ]);
            }

            DB::commit();
            return redirect()->route('laporan.index')->with('success', 'Laporan akhir berhasil dikirim');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $laporan = Laporan::with(['penugasan.tugas', 'files', 'chats.user'])->findOrFail($id);
        return view('detaillaporan', compact('laporan'));
    }

    public function showAdmin($id)
    {
        $laporan = Laporan::with([
            'penugasan.tugas',
            'penugasan.anggota.user',
            'penugasan.admin',
            'files',
            'chats.user'
        ])->findOrFail($id);

        $extensionRequests = $laporan->penugasan->anggota
            ->where('status_keterlambatan', 'mengajukan')
            ->values();

        $dailyReports = DailyProgressReport::with('user')
            ->where('id_penugasan', $laporan->id_penugasan)
            ->orderBy('tanggal_laporan', 'desc')
            ->get();

        return view('admin.detaillaporan', compact('laporan', 'extensionRequests', 'dailyReports'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:disetujui,revisi',
            'pesan_revisi' => 'nullable|string'
        ]);

        $laporan = Laporan::findOrFail($id);
        $laporan->update([
            'status' => $request->status
        ]);

        if ($request->status === 'revisi' && $request->filled('pesan_revisi')) {
            LaporanRevisiChat::create([
                'id_laporan' => $laporan->id,
                'id_user' => Auth::user()->nip,
                'pesan' => $request->pesan_revisi,
                'is_from_admin_panel' => true
            ]);
        }

        return redirect()->back()->with('success', 'Status laporan berhasil diperbarui');
    }

    public function ajukanPerpanjangan(Request $request, $id_penugasan)
    {
        $request->validate([
            'alasan_keterlambatan' => 'required|string|max:1000'
        ]);

        $anggota = AnggotaPenugasan::where('id_penugasan', $id_penugasan)
            ->where('id_user', Auth::user()->nip)
            ->firstOrFail();

        $anggota->update([
            'status_keterlambatan' => 'mengajukan',
            'alasan_keterlambatan' => $request->alasan_keterlambatan,
        ]);

        return redirect()->back()->with('success', 'Permohonan perpanjangan waktu berhasil diajukan dan akan tampil sebagai notifikasi admin.');
    }

    public function pendingExtensionSummary()
    {
        $requests = AnggotaPenugasan::with(['penugasan.tugas', 'user'])
            ->where('status_keterlambatan', 'mengajukan')
            ->get();

        return response()->json([
            'count' => $requests->count(),
            'items' => $requests->map(function ($request) {
                return [
                    'id_penugasan' => $request->id_penugasan,
                    'nama_tugas' => $request->penugasan->tugas->nama_tugas ?? 'Penugasan',
                    'user' => $request->user->name ?? $request->id_user,
                    'url' => route('admin.penugasan.show', $request->id_penugasan),
                ];
            }),
        ]);
    }

    public function storeChat(Request $request)
    {
        $request->validate([
            'id_laporan' => 'required|exists:laporans,id',
            'pesan' => 'required|string'
        ]);

        $isFromAdminPanel = $request->routeIs('admin.*') || $request->is('admin/*') ? true : false;

        LaporanRevisiChat::create([
            'id_laporan' => $request->id_laporan,
            'id_user' => Auth::user()->nip,
            'pesan' => $request->pesan,
            'is_from_admin_panel' => $isFromAdminPanel,
        ]);

        return redirect()->back()->with('success', 'Pesan berhasil dikirim.');
    }

    public function submitRevisi(Request $request, $id)
    {
        $request->validate([
            'pesan' => 'required|string|max:2000',
            'files.*' => 'file|max:5120',
            'file_laporan.*' => 'file|max:5120'
        ]);

        DB::beginTransaction();
        try {
            $laporan = Laporan::findOrFail($id);
            $laporan->update([
                'status' => 'diajukan'
            ]);

            LaporanRevisiChat::create([
                'id_laporan' => $laporan->id,
                'id_user' => Auth::user()->nip,
                'pesan' => $request->pesan,
                'is_from_admin_panel' => false,
            ]);

            $uploadedFiles = [];
            if ($request->hasFile('files')) {
                $uploadedFiles = $request->file('files');
            } elseif ($request->hasFile('file_laporan')) {
                $uploadedFiles = $request->file('file_laporan');
            }

            foreach ($uploadedFiles as $file) {
                $path = $file->store('laporan_files', 'public');
                LaporanFile::create([
                    'id_laporan' => $laporan->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName()
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Revisi laporan dan berkas baru berhasil dikirim.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function dailyReportsComplete(Penugasan $penugasan, string $userId): bool
    {
        $startDate = Carbon::parse($penugasan->tugas->tanggal_mulai ?? $penugasan->created_at)->startOfDay();
        $endDate = Carbon::parse($penugasan->tugas->tanggal_selesai ?? $penugasan->batas_waktu_lapor)->startOfDay();
        $reportedDates = DailyProgressReport::where('id_penugasan', $penugasan->id)
            ->where('id_user', $userId)
            ->pluck('tanggal_laporan')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->toArray();

        foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
            if (!in_array($date->toDateString(), $reportedDates, true)) {
                return false;
            }
        }

        return true;
    }
}
