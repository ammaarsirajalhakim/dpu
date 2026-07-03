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
        $query = Penugasan::with(['tugas', 'admin', 'anggota.user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('tugas', function ($q) use ($search) {
                $q->where('kodetugas', 'like', "%{$search}%")
                    ->orWhere('nama_tugas', 'like', "%{$search}%");
            })->orWhereHas('anggota.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
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
            return back()->withInput()->with('error', 'Batas waktu lapor tidak boleh kurang dari tanggal selesai tugas. Batas waktu lapor boleh sama dengan tanggal selesai.');
        }

        DB::beginTransaction();
        try {
            $penugasan = Penugasan::create([
                'kodetugas' => $validated['kodetugas'],
                'id_admin' => Auth::id(),
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
        $penugasan = Penugasan::with(['anggota.user'])->where('kodetugas', $kodetugas)->first();
        return response()->json(['exists' => (bool) $penugasan, 'data' => $penugasan]);
    }

    public function show($id)
    {
        $penugasan = Penugasan::with(['tugas', 'admin', 'anggota.user', 'laporan'])->findOrFail($id);

        if (!in_array(Auth::user()->role, ['admin', 'superadmin'])) {
            $isMember = $penugasan->anggota->contains('id_user', Auth::id());
            if (!$isMember) abort(403, 'Anda tidak memiliki akses ke detail penugasan ini.');
        }

        return view('detailpenugasanuser', compact('penugasan'));
    }

    public function showAdmin($id)
    {
        $penugasan = Penugasan::with(['tugas', 'admin', 'anggota.user'])->findOrFail($id);
        $extensionRequests = $penugasan->anggota->where('status_keterlambatan', 'mengajukan')->values();
        return view('admin.detailpenugasan', compact('penugasan', 'extensionRequests'));
    }

    public function indexUser()
    {
        $penugasans = Penugasan::whereHas('anggota', function ($query) {
            $query->where('id_user', Auth::id());
        })->with([
            'tugas',
            'admin',
            'laporan',
            'dailyProgressReports' => function ($query) {
                $query->where('id_user', Auth::id());
            }
        ])->orderBy('created_at', 'desc')->get();

        return view('penugasanuser', compact('penugasans'));
    }

    public function edit($id)
    {
        $penugasan = Penugasan::with('anggota')->findOrFail($id);
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
            return back()->withInput()->with('error', 'Batas waktu lapor tidak boleh kurang dari tanggal selesai tugas. Batas waktu lapor boleh sama dengan tanggal selesai.');
        }

        DB::beginTransaction();
        try {
            $penugasan->update(['batas_waktu_lapor' => $validated['batas_waktu_lapor']]);

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
            return back()->withInput()->with('error', 'Batas waktu baru tidak boleh kurang dari tanggal selesai tugas.');
        }

        $penugasan->update(['batas_waktu_lapor' => $validated['batas_waktu_lapor']]);

        AnggotaPenugasan::where('id_penugasan', $penugasan->id)
            ->where('status_keterlambatan', 'mengajukan')
            ->update([
                'status_keterlambatan' => 'disetujui',
                'custom_deadline' => $validated['batas_waktu_lapor'],
            ]);

        return redirect()->back()->with('success', 'Batas waktu laporan berhasil diperpanjang.');
    }

    public function destroy($id)
    {
        $penugasan = Penugasan::findOrFail($id);
        $penugasan->delete();
        return redirect()->route('admin.penugasan.index')->with('success', 'Penugasan berhasil dihapus!');
    }

    public function export()
    {
        $penugasan = Penugasan::with(['tugas', 'admin', 'anggota.user'])->orderBy('created_at', 'desc')->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Penugasan');

        $headers = ['ID', 'Kode Tugas', 'Nama Tugas', 'Admin', 'Anggota', 'Batas Waktu Lapor', 'Tanggal Dibuat'];
        foreach ($headers as $index => $header) {
            $sheet->setCellValue(chr(65 + $index) . '1', $header);
        }

        $row = 2;
        foreach ($penugasan as $item) {
            $anggota = $item->anggota->map(fn ($a) => ($a->user->name ?? $a->id_user) . ' (' . $a->id_user . ')')->implode(', ');
            $sheet->setCellValue('A' . $row, $item->id);
            $sheet->setCellValue('B' . $row, $item->kodetugas);
            $sheet->setCellValue('C' . $row, $item->tugas->nama_tugas ?? '-');
            $sheet->setCellValue('D' . $row, $item->admin->name ?? $item->id_admin);
            $sheet->setCellValue('E' . $row, $anggota);
            $sheet->setCellValue('F' . $row, $item->batas_waktu_lapor);
            $sheet->setCellValue('G' . $row, $item->created_at);
            $row++;
        }

        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Data_Penugasan_' . date('Y-m-d') . '.xlsx';

        if (ob_get_length()) ob_end_clean();

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
        $sheetTugas->setCellValue('C1', 'Tanggal Selesai');
        $sheetTugas->getStyle('A1:C1')->getFont()->setBold(true);

        $rowTugas = 2;
        foreach (Tugas::all() as $tugas) {
            $sheetTugas->setCellValue('A' . $rowTugas, $tugas->kodetugas);
            $sheetTugas->setCellValue('B' . $rowTugas, $tugas->nama_tugas);
            $sheetTugas->setCellValue('C' . $rowTugas, $tugas->tanggal_selesai);
            $rowTugas++;
        }

        $sheetUser = $spreadsheet->createSheet();
        $sheetUser->setTitle('Daftar User');
        $sheetUser->setCellValue('A1', 'NIP');
        $sheetUser->setCellValue('B1', 'Nama');
        $sheetUser->setCellValue('C1', 'Email');
        $sheetUser->getStyle('A1:C1')->getFont()->setBold(true);

        $rowUser = 2;
        foreach (User::all() as $user) {
            $sheetUser->setCellValue('A' . $rowUser, $user->nip);
            $sheetUser->setCellValue('B' . $rowUser, $user->name);
            $sheetUser->setCellValue('C' . $rowUser, $user->email);
            $rowUser++;
        }

        $spreadsheet->setActiveSheetIndex(0);
        $writer = new Xlsx($spreadsheet);
        $filename = 'Template_Import_Penugasan_Lengkap.xlsx';

        if (ob_get_length()) ob_end_clean();

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
            return redirect()->route('admin.penugasan.index')->with('error', 'Tidak ada data yang diproses.');
        }

        foreach ($penugasanData as $index => $row) {
            if (empty($row['kodetugas'])) continue;

            $tugas = Tugas::where('kodetugas', $row['kodetugas'])->first();
            $batasWaktuLapor = $this->formatTanggalMySQL($row['batas_waktu_lapor'] ?? null);
            $nomorBaris = $index + 1;

            if (!$tugas) {
                return redirect()->route('admin.penugasan.index')->with('error', "Import penugasan baris {$nomorBaris} gagal: kode tugas tidak ditemukan.");
            }

            if (!$batasWaktuLapor || !$this->isDeadlineValidForTask($batasWaktuLapor, $tugas)) {
                return redirect()->route('admin.penugasan.index')->with('error', "Import penugasan baris {$nomorBaris} gagal: batas waktu lapor tidak boleh kurang dari tanggal selesai tugas.");
            }

            $penugasan = Penugasan::create([
                'kodetugas' => $row['kodetugas'],
                'batas_waktu_lapor' => $batasWaktuLapor,
                'id_admin' => Auth::user()->nip,
            ]);

            if (!empty($row['nip_anggota'])) {
                foreach (explode(',', $row['nip_anggota']) as $nip) {
                    $nipBersih = trim($nip);
                    if (!empty($nipBersih)) {
                        AnggotaPenugasan::create([
                            'id_penugasan' => $penugasan->id,
                            'id_user' => $nipBersih,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('admin.penugasan.index')->with('success', 'Data penugasan dan anggota berhasil diimport!');
    }

    private function isDeadlineValidForTask($deadline, ?Tugas $tugas): bool
    {
        if (!$tugas || !$tugas->tanggal_selesai || !$deadline) return false;

        return \Carbon\Carbon::parse($deadline)->startOfDay()
            ->greaterThanOrEqualTo(\Carbon\Carbon::parse($tugas->tanggal_selesai)->startOfDay());
    }

    private function formatTanggalMySQL($tanggal)
    {
        if (empty($tanggal)) return null;

        if (is_numeric($tanggal)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggal)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        $tanggal = trim($tanggal);
        $formats = ['d/m/Y', 'd-m-Y', 'd.m.Y', 'Y-m-d', 'Y/m/d', 'm/d/Y'];

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

    private function deadlineValidationMessages(): array
    {
        return [
            'batas_waktu_lapor.required' => 'Batas waktu lapor wajib diisi.',
            'batas_waktu_lapor.date' => 'Batas waktu lapor harus berupa tanggal yang valid.',
            'anggota.required' => 'Minimal satu anggota wajib dipilih.',
        ];
    }
}
