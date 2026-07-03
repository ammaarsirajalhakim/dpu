<?php

namespace App\Http\Controllers;

use App\Models\DailyProgressReport;
use App\Models\Penugasan;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DailyProgressReportController extends Controller
{
    public function show($id)
    {
        $penugasan = Penugasan::with([
            'tugas',
            'admin',
            'anggota.user',
            'laporan',
            'dailyProgressReports' => function ($query) {
                $query->where('id_user', Auth::id())->orderBy('tanggal_laporan');
            },
        ])->findOrFail($id);

        $this->authorizeUserAccess($penugasan);

        $progressData = $this->buildProgressData($penugasan);
        $calendarDays = $progressData['calendarDays'];
        $startDate = $progressData['startDate'];
        $endDate = $progressData['endDate'];
        $missingCount = $progressData['missingCount'];
        $isFinalReportAllowed = $missingCount === 0;
        $isDeadlinePassed = now()->greaterThan(Carbon::parse($penugasan->batas_waktu_lapor));
        $extensionRequest = $penugasan->anggota()->where('id_user', Auth::id())->first();

        $today = now()->startOfDay();
        $firstMissingDate = $calendarDays->firstWhere('status', 'belum_lapor')['date_key'] ?? null;

        if ($firstMissingDate) {
            $selectedDate = old('tanggal_laporan', $firstMissingDate);
        } elseif ($today->betweenIncluded($startDate, $endDate)) {
            $selectedDate = old('tanggal_laporan', $today->toDateString());
        } else {
            $selectedDate = old('tanggal_laporan', $startDate->toDateString());
        }

        return view('detailpenugasanuser-progress', compact(
            'penugasan',
            'calendarDays',
            'selectedDate',
            'missingCount',
            'startDate',
            'endDate',
            'isFinalReportAllowed',
            'isDeadlinePassed',
            'extensionRequest'
        ));
    }

    public function store(Request $request, $id_penugasan)
    {
        $penugasan = Penugasan::with('tugas')->findOrFail($id_penugasan);

        $this->authorizeUserAccess($penugasan);

        $startDate = Carbon::parse($penugasan->tugas->tanggal_mulai ?? $penugasan->created_at)->startOfDay();
        $endDate = Carbon::parse($penugasan->tugas->tanggal_selesai ?? $penugasan->batas_waktu_lapor)->endOfDay();

        $validated = $request->validate([
            'tanggal_laporan' => ['required', 'date', 'after_or_equal:' . $startDate->toDateString(), 'before_or_equal:' . $endDate->toDateString()],
            'progres' => ['required', 'string', 'max:3000'],
            'kendala' => ['nullable', 'string', 'max:3000'],
            'rencana_lanjut' => ['nullable', 'string', 'max:3000'],
            'file_laporan_harian' => ['nullable', 'file', 'max:5120'],
        ], [
            'tanggal_laporan.required' => 'Tanggal laporan harian wajib diisi.',
            'tanggal_laporan.after_or_equal' => 'Tanggal laporan tidak boleh sebelum tanggal mulai tugas.',
            'tanggal_laporan.before_or_equal' => 'Tanggal laporan tidak boleh melewati tanggal selesai tugas.',
            'progres.required' => 'Isi progres harian wajib diisi.',
            'file_laporan_harian.max' => 'Lampiran laporan harian maksimal 5MB.',
        ]);

        $payload = [
            'progres' => $validated['progres'],
            'kendala' => $validated['kendala'] ?? null,
            'rencana_lanjut' => $validated['rencana_lanjut'] ?? null,
        ];

        if ($request->hasFile('file_laporan_harian')) {
            $file = $request->file('file_laporan_harian');
            $payload['file_path'] = $file->store('laporan_harian_files', 'public');
            $payload['file_name'] = $file->getClientOriginalName();
        }

        DailyProgressReport::updateOrCreate(
            [
                'id_penugasan' => $penugasan->id,
                'id_user' => Auth::id(),
                'tanggal_laporan' => $validated['tanggal_laporan'],
            ],
            $payload
        );

        return redirect()->route('penugasan.show', $penugasan->id)->with('success', 'Laporan progres harian berhasil disimpan.');
    }

    public function pendingSummary()
    {
        $today = now()->toDateString();

        $penugasans = Penugasan::with(['tugas', 'dailyProgressReports' => function ($query) use ($today) {
                $query->where('id_user', Auth::id())->whereDate('tanggal_laporan', $today);
            }])
            ->whereHas('anggota', function ($query) {
                $query->where('id_user', Auth::id());
            })
            ->get()
            ->filter(function ($penugasan) use ($today) {
                $start = Carbon::parse($penugasan->tugas->tanggal_mulai ?? $penugasan->created_at)->toDateString();
                $end = Carbon::parse($penugasan->tugas->tanggal_selesai ?? $penugasan->batas_waktu_lapor)->toDateString();

                return $today >= $start && $today <= $end && $penugasan->dailyProgressReports->isEmpty();
            })
            ->values();

        return response()->json([
            'count' => $penugasans->count(),
            'items' => $penugasans->map(function ($penugasan) {
                return [
                    'id' => $penugasan->id,
                    'nama_tugas' => $penugasan->tugas->nama_tugas ?? 'Penugasan',
                    'url' => route('penugasan.show', $penugasan->id),
                ];
            }),
        ]);
    }

    private function buildProgressData(Penugasan $penugasan): array
    {
        $startDate = Carbon::parse($penugasan->tugas->tanggal_mulai ?? $penugasan->created_at)->startOfDay();
        $endDate = Carbon::parse($penugasan->tugas->tanggal_selesai ?? $penugasan->batas_waktu_lapor)->startOfDay();
        $today = now()->startOfDay();
        $reports = $penugasan->dailyProgressReports->keyBy(fn ($report) => $report->tanggal_laporan->toDateString());
        $calendarDays = collect();

        foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
            $dateKey = $date->toDateString();
            $report = $reports->get($dateKey);
            $isFuture = $date->greaterThan($today);
            $status = $report ? 'sudah_lapor' : ($isFuture ? 'menunggu' : 'belum_lapor');

            $calendarDays->push([
                'date' => $date->copy(),
                'date_key' => $dateKey,
                'status' => $status,
                'report' => $report,
            ]);
        }

        return [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'calendarDays' => $calendarDays,
            'missingCount' => $calendarDays->where('status', 'belum_lapor')->count(),
        ];
    }

    private function authorizeUserAccess(Penugasan $penugasan): void
    {
        $isMember = $penugasan->anggota()->where('id_user', Auth::id())->exists();

        if (!$isMember && !in_array(Auth::user()->role, ['admin', 'superadmin'])) {
            abort(403, 'Anda tidak memiliki akses ke penugasan ini.');
        }
    }
}
