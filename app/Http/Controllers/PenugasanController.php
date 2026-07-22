<?php

namespace App\Http\Controllers;

use App\Models\Penugasan;
use App\Models\AnggotaPenugasan;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PenugasanController extends Controller
{
    public function index(Request $request)
    {
        $query = Penugasan::with(['tugas', 'admin', 'anggota.user', 'laporan']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->whereHas('tugas', function ($q) use ($search) {
                $q->where('kodetugas', 'like', "%{$search}%")
                    ->orWhere('nama_tugas', 'like', "%{$search}%");
            })->orWhereHas('anggota.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $penugasan = $query->orderBy('created_at', 'desc')->get();

        return view('admin.penugasan', compact('penugasan'));
    }

    public function create()
    {
        $tugas = Tugas::all();
        $users = User::where('role', 'user')->get();

        return view('admin.tambahpenugasan', compact('tugas', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kodetugas' => 'required|exists:tugas,kodetugas',
            'batas_waktu_lapor' => 'required|date',
            'anggota' => 'required|array|min:1',
            'anggota.*.id_user' => 'required|exists:users,nip',
        ], $this->deadlineValidationMessages());

        $tugas = Tugas::where('kodetugas', $validated['kodetugas'])->firstOrFail();

        if (!$this->isDeadlineValidForTask($validated['batas_waktu_lapor'], $tugas)) {
            return back()
                ->withInput()
                ->with('error', 'Batas waktu lapor tidak boleh kurang dari tanggal selesai tugas. Batas waktu lapor boleh sama dengan tanggal selesai.');
        }

        DB::beginTransaction();

        try {
            $penugasan = Penugasan::create([
                'kodetugas' => $validated['kodetugas'],
                'id_admin' => Auth::user()->nip,
                'batas_waktu_lapor' => $validated['batas_waktu_lapor'],
            ]);

            foreach ($validated['anggota'] as $item) {
                AnggotaPenugasan::create([
                    'id_penugasan' => $penugasan->id,
                    'id_user' => $item['id_user'],
                ]);
            }

            DB::commit();

            return redirect()->route('admin.penugasan.index')->with('success', 'Penugasan berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function checkExisting($kodetugas)
    {
        $penugasan = Penugasan::with(['anggota.user', 'laporan'])
            ->where('kodetugas', $kodetugas)
            ->first();

        return response()->json([
            'exists' => (bool) $penugasan,
            'data' => $penugasan,
        ]);
    }

    public function show($id)
    {
        $penugasan = Penugasan::with(['tugas', 'admin', 'anggota.user', 'laporan'])->findOrFail($id);

        if (!in_array(Auth::user()->role, ['admin', 'superadmin'])) {
            $isMember = $penugasan->anggota->contains('id_user', Auth::user()->nip);

            if (!$isMember) {
                abort(403, 'Anda tidak memiliki akses ke detail penugasan ini.');
            }
        }

        return view('detailpenugasanuser', compact('penugasan'));
    }

    public function showAdmin($id)
    {
        $penugasan = Penugasan::with(['tugas', 'admin', 'anggota.user', 'laporan'])->findOrFail($id);
        $extensionRequests = $penugasan->anggota->where('status_keterlambatan', 'mengajukan')->values();

        return view('admin.detailpenugasan', compact('penugasan', 'extensionRequests'));
    }

    public function indexUser()
    {
        $penugasans = Penugasan::whereHas('anggota', function ($query) {
            $query->where('id_user', Auth::user()->nip);
        })->with([
            'tugas',
            'admin',
            'laporan',
            'dailyProgressReports' => function ($query) {
                $query->where('id_user', Auth::user()->nip);
            },
        ])->orderBy('created_at', 'desc')->get();

        return view('penugasanuser', compact('penugasans'));
    }

    public function edit($id)
    {
        $penugasan = Penugasan::with(['anggota', 'laporan'])->findOrFail($id);
        $tugas = Tugas::all();
        $users = User::where('role', 'user')->get();

        return view('admin.tambahpenugasan', compact('penugasan', 'tugas', 'users'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'batas_waktu_lapor' => 'required|date',
            'anggota' => 'required|array|min:1',
            'anggota.*.id_user' => 'required|exists:users,nip',
        ], $this->deadlineValidationMessages());

        $penugasan = Penugasan::with('tugas')->findOrFail($id);

        if (!$this->isDeadlineValidForTask($validated['batas_waktu_lapor'], $penugasan->tugas)) {
            return back()
                ->withInput()
                ->with('error', 'Batas waktu lapor tidak boleh kurang dari tanggal selesai tugas. Batas waktu lapor boleh sama dengan tanggal selesai.');
        }

        DB::beginTransaction();

        try {
            $penugasan->update([
                'batas_waktu_lapor' => $validated['batas_waktu_lapor'],
            ]);

            AnggotaPenugasan::where('id_penugasan', $id)->delete();

            foreach ($validated['anggota'] as $item) {
                AnggotaPenugasan::create([
                    'id_penugasan' => $id,
                    'id_user' => $item['id_user'],
                ]);
            }

            DB::commit();

            return redirect()->route('admin.penugasan.index')->with('success', 'Penugasan berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updateDeadline(Request $request, $id)
    {
        $validated = $request->validate([
            'batas_waktu_lapor' => 'required|date',
            'catatan_admin' => 'nullable|string|max:1000',
        ], $this->deadlineValidationMessages());

        $penugasan = Penugasan::with(['tugas', 'anggota'])->findOrFail($id);

        if (!$this->isDeadlineValidForTask($validated['batas_waktu_lapor'], $penugasan->tugas)) {
            return back()
                ->withInput()
                ->with('error', 'Batas waktu baru tidak boleh kurang dari tanggal selesai tugas.');
        }

        DB::beginTransaction();

        try {
            $penugasan->update([
                'batas_waktu_lapor' => $validated['batas_waktu_lapor'],
            ]);

            AnggotaPenugasan::where('id_penugasan', $penugasan->id)
                ->where('status_keterlambatan', 'mengajukan')
                ->update([
                    'status_keterlambatan' => 'disetujui',
                    'custom_deadline' => $validated['batas_waktu_lapor'],
                ]);

            DB::commit();

            return redirect()->back()->with('success', 'Batas waktu laporan berhasil diperpanjang.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $penugasan = Penugasan::findOrFail($id);
        $penugasan->delete();

        return redirect()->route('admin.penugasan.index')->with('success', 'Penugasan berhasil dihapus!');
    }

    public function export()
    {
        $penugasan = Penugasan::with(['tugas', 'admin', 'anggota.user', 'laporan'])
            ->orderBy('created_at', 'desc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Data Penugasan');

        $headers = [
            'ID',
            'Kode Tugas',
            'Nama Tugas',
            'Admin',
            'Anggota',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Batas Waktu Lapor',
            'Status Penugasan',
            'Tanggal Dibuat',
        ];

        foreach ($headers as $index => $header) {
            $sheet->setCellValue(chr(65 + $index) . '1', $header);
        }

        $row = 2;

        foreach ($penugasan as $item) {
            $anggota = $item->anggota
                ->map(fn ($a) => ($a->user->name ?? $a->id_user) . ' (' . $a->id_user . ')')
                ->implode(', ');

            $sheet->setCellValue('A' . $row, $item->id);
            $sheet->setCellValue('B' . $row, $item->kodetugas);
            $sheet->setCellValue('C' . $row, $item->tugas->nama_tugas ?? '-');
            $sheet->setCellValue('D' . $row, $item->admin->name ?? $item->id_admin);
            $sheet->setCellValue('E' . $row, $anggota);
            $sheet->setCellValue('F' . $row, $item->tugas->tanggal_mulai ?? '-');
            $sheet->setCellValue('G' . $row, $item->tugas->tanggal_selesai ?? '-');
            $sheet->setCellValue('H' . $row, $item->batas_waktu_lapor);
            $sheet->setCellValue('I' . $row, $item->status_penugasan_label);
            $sheet->setCellValue('J' . $row, $item->created_at);
            $row++;
        }

        foreach (range('A', 'J') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Data_Penugasan_' . date('Y-m-d') . '.xlsx';

        if (ob_get_length()) {
            ob_end_clean();
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function template()
    {
        $spreadsheet = new Spreadsheet();

        $sheetTemplate = $spreadsheet->getActiveSheet();
        $sheetTemplate->setTitle('Template Penugasan');
        $sheetTemplate->setCellValue('A1', 'Kode Tugas');
        $sheetTemplate->setCellValue('B1', 'Batas Waktu Lapor (YYYY-MM-DD)');
        $sheetTemplate->setCellValue('C1', 'NIP Anggota (Pisahkan dengan koma)');

        $sheetTemplate->setCellValue('A2', 'CONTOH-KODE-TGS');
        $sheetTemplate->setCellValue('B2', \Carbon\Carbon::now()->addDays(7)->format('Y-m-d'));
        $sheetTemplate->setCellValue('C2', '199001012024011001, 199001012024011002');

        $sheetTemplate->getStyle('A1:C1')->getFont()->setBold(true);

        foreach (range('A', 'C') as $columnID) {
            $sheetTemplate->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheetTugas = $spreadsheet->createSheet();
        $sheetTugas->setTitle('Daftar Tugas');
        $sheetTugas->setCellValue('A1', 'Kode Tugas');
        $sheetTugas->setCellValue('B1', 'Nama Tugas');
        $sheetTugas->setCellValue('C1', 'Tanggal Mulai');
        $sheetTugas->setCellValue('D1', 'Tanggal Selesai');

        $sheetTugas->getStyle('A1:D1')->getFont()->setBold(true);

        $rowTugas = 2;

        foreach (Tugas::all() as $tugas) {
            $sheetTugas->setCellValue('A' . $rowTugas, $tugas->kodetugas);
            $sheetTugas->setCellValue('B' . $rowTugas, $tugas->nama_tugas);
            $sheetTugas->setCellValue('C' . $rowTugas, $tugas->tanggal_mulai);
            $sheetTugas->setCellValue('D' . $rowTugas, $tugas->tanggal_selesai);
            $rowTugas++;
        }

        foreach (range('A', 'D') as $columnID) {
            $sheetTugas->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheetUser = $spreadsheet->createSheet();
        $sheetUser->setTitle('Daftar User');
        $sheetUser->setCellValue('A1', 'NIP');
        $sheetUser->setCellValue('B1', 'Nama');
        $sheetUser->setCellValue('C1', 'Email');

        $sheetUser->getStyle('A1:C1')->getFont()->setBold(true);

        $rowUser = 2;

        foreach (User::where('role', 'user')->get() as $user) {
            $sheetUser->setCellValue('A' . $rowUser, $user->nip);
            $sheetUser->setCellValue('B' . $rowUser, $user->name);
            $sheetUser->setCellValue('C' . $rowUser, $user->email);
            $rowUser++;
        }

        foreach (range('A', 'C') as $columnID) {
            $sheetUser->getColumnDimension($columnID)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $filename = 'Template_Import_Penugasan_Lengkap.xlsx';

        if (ob_get_length()) {
            ob_end_clean();
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function importProcess(Request $request)
    {
        $penugasanData = $request->input('penugasan');

        if (!$penugasanData) {
            return redirect()->route('admin.penugasan.index')->with('error', 'Tidak ada data penugasan yang diproses.');
        }

        DB::beginTransaction();

        try {
            foreach ($penugasanData as $index => $row) {
                $nomorBaris = $index + 1;

                $kodeTugas = trim($row['kodetugas'] ?? $row['kode_tugas'] ?? '');
                $batasWaktuLapor = $this->formatTanggalMySQL($row['batas_waktu_lapor'] ?? $row['batas_lapor'] ?? null);
                $anggotaRaw = $row['anggota'] ?? $row['nip_anggota'] ?? $row['id_user'] ?? '';

                if (!$kodeTugas) {
                    DB::rollBack();

                    return redirect()->route('admin.penugasan.index')
                        ->with('error', "Import penugasan baris {$nomorBaris} gagal: kode tugas wajib diisi.");
                }

                $tugas = Tugas::where('kodetugas', $kodeTugas)->first();

                if (!$tugas) {
                    DB::rollBack();

                    return redirect()->route('admin.penugasan.index')
                        ->with('error', "Import penugasan baris {$nomorBaris} gagal: kode tugas {$kodeTugas} tidak ditemukan.");
                }

                if (!$batasWaktuLapor) {
                    DB::rollBack();

                    return redirect()->route('admin.penugasan.index')
                        ->with('error', "Import penugasan baris {$nomorBaris} gagal: batas waktu lapor wajib valid.");
                }

                if (!$this->isDeadlineValidForTask($batasWaktuLapor, $tugas)) {
                    DB::rollBack();

                    return redirect()->route('admin.penugasan.index')
                        ->with('error', "Import penugasan baris {$nomorBaris} gagal: batas waktu lapor tidak boleh kurang dari tanggal selesai tugas.");
                }

                $anggotaIds = collect(explode(',', $anggotaRaw))
                    ->map(fn ($item) => trim($item))
                    ->filter()
                    ->unique()
                    ->values();

                if ($anggotaIds->isEmpty()) {
                    DB::rollBack();

                    return redirect()->route('admin.penugasan.index')
                        ->with('error', "Import penugasan baris {$nomorBaris} gagal: minimal satu NIP anggota wajib diisi.");
                }

                foreach ($anggotaIds as $idUser) {
                    if (!User::where('nip', $idUser)->exists()) {
                        DB::rollBack();

                        return redirect()->route('admin.penugasan.index')
                            ->with('error', "Import penugasan baris {$nomorBaris} gagal: NIP {$idUser} tidak ditemukan.");
                    }
                }

                $penugasan = Penugasan::create([
                    'kodetugas' => $kodeTugas,
                    'id_admin' => Auth::user()->nip,
                    'batas_waktu_lapor' => $batasWaktuLapor,
                ]);

                foreach ($anggotaIds as $idUser) {
                    AnggotaPenugasan::create([
                        'id_penugasan' => $penugasan->id,
                        'id_user' => $idUser,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.penugasan.index')->with('success', 'Data penugasan berhasil diimport!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('admin.penugasan.index')->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    private function formatTanggalMySQL($tanggal)
    {
        if (empty($tanggal)) {
            return null;
        }

        if (is_numeric($tanggal)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggal)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        $tanggal = trim($tanggal);

        $formats = [
            'd/m/Y',
            'd-m-Y',
            'd.m.Y',
            'Y-m-d',
            'Y/m/d',
            'm/d/Y',
            'Y-m-d H:i:s',
            'Y-m-d\TH:i',
        ];

        foreach ($formats as $format) {
            try {
                return \Carbon\Carbon::createFromFormat($format, $tanggal)->format('Y-m-d');
            } catch (\Exception $e) {
                continue;
            }
        }

        try {
            return \Carbon\Carbon::parse($tanggal)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function isDeadlineValidForTask($deadline, ?Tugas $tugas): bool
    {
        if (!$tugas || !$tugas->tanggal_selesai) {
            return false;
        }

        return \Carbon\Carbon::parse($deadline)
            ->startOfDay()
            ->gte(\Carbon\Carbon::parse($tugas->tanggal_selesai)->startOfDay());
    }

    private function deadlineValidationMessages(): array
    {
        return [
            'kodetugas.required' => 'Kode tugas wajib dipilih.',
            'kodetugas.exists' => 'Kode tugas tidak ditemukan.',
            'batas_waktu_lapor.required' => 'Batas waktu lapor wajib diisi.',
            'batas_waktu_lapor.date' => 'Batas waktu lapor harus berupa tanggal yang valid.',
            'anggota.required' => 'Minimal satu anggota wajib dipilih.',
            'anggota.array' => 'Format anggota tidak valid.',
            'anggota.min' => 'Minimal satu anggota wajib dipilih.',
            'anggota.*.id_user.required' => 'NIP anggota wajib diisi.',
            'anggota.*.id_user.exists' => 'NIP anggota tidak ditemukan.',
        ];
    }
}