<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TugasController extends Controller
{
    public function index(Request $request)
    {
        $query = Tugas::with(['admin', 'penugasan.laporan']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('kodetugas', 'like', "%{$search}%")
                    ->orWhere('nama_tugas', 'like', "%{$search}%");
            });
        }

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_mulai', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_mulai', $request->tahun);
        }

        if ($request->filled('status')) {
            $today = now()->toDateString();

            if ($request->status === 'akan_datang') {
                $query->whereDate('tanggal_mulai', '>', $today)
                    ->whereDoesntHave('penugasan.laporan', function ($q) {
                        $q->where('status', 'disetujui');
                    });
            } elseif ($request->status === 'aktif') {
                $query->whereDate('tanggal_mulai', '<=', $today)
                    ->whereDate('tanggal_selesai', '>=', $today)
                    ->whereDoesntHave('penugasan.laporan', function ($q) {
                        $q->where('status', 'disetujui');
                    });
            } elseif ($request->status === 'terlewat') {
                $query->whereDate('tanggal_selesai', '<', $today)
                    ->whereDoesntHave('penugasan.laporan', function ($q) {
                        $q->where('status', 'disetujui');
                    });
            } elseif ($request->status === 'selesai') {
                $query->whereHas('penugasan.laporan', function ($q) {
                    $q->where('status', 'disetujui');
                });
            }
        }

        $tugas = $query->orderBy('created_at', 'desc')->get();

        return view('admin.tugas', compact('tugas'));
    }

    public function create()
    {
        return view('admin.tambahtugas');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kodetugas' => 'required|string|max:10|unique:tugas,kodetugas',
            'nama_tugas' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'lampiran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], $this->dateValidationMessages());

        $data = collect($validated)->except('lampiran')->toArray();
        $data['id_admin'] = Auth::user()->nip;

        if ($request->hasFile('lampiran')) {
            $data['lampiran'] = $request->file('lampiran')->store('lampiran_tugas', 'public');
        }

        Tugas::create($data);

        return redirect()->route('admin.tugas.index')->with('success', 'Tugas baru berhasil ditambahkan!');
    }

    public function edit($kodetugas)
    {
        $tugas = Tugas::findOrFail($kodetugas);

        return view('admin.edittugas', compact('tugas'));
    }

    public function update(Request $request, $kodetugas)
    {
        $tugas = Tugas::findOrFail($kodetugas);

        $validated = $request->validate([
            'nama_tugas' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'lampiran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], $this->dateValidationMessages());

        $data = collect($validated)->except('lampiran')->toArray();

        if ($request->hasFile('lampiran')) {
            if ($tugas->lampiran && Storage::disk('public')->exists($tugas->lampiran)) {
                Storage::disk('public')->delete($tugas->lampiran);
            }

            $data['lampiran'] = $request->file('lampiran')->store('lampiran_tugas', 'public');
        }

        $tugas->update($data);

        return redirect()->route('admin.tugas.index')->with('success', 'Data tugas berhasil diperbarui!');
    }

    public function destroy($kodetugas)
    {
        $tugas = Tugas::findOrFail($kodetugas);

        if ($tugas->lampiran && Storage::disk('public')->exists($tugas->lampiran)) {
            Storage::disk('public')->delete($tugas->lampiran);
        }

        $tugas->delete();

        return redirect()->route('admin.tugas.index')->with('success', 'Tugas beserta lampirannya berhasil dihapus!');
    }

    public function show($kodetugas)
    {
        $tugas = Tugas::with(['admin', 'penugasan.laporan', 'penugasan.anggota.user'])->findOrFail($kodetugas);

        return view('admin.detailtugas', compact('tugas'));
    }

    public function template()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Template Tugas');
        $sheet->setCellValue('A1', 'Kode Tugas');
        $sheet->setCellValue('B1', 'Nama Tugas');
        $sheet->setCellValue('C1', 'Deskripsi');
        $sheet->setCellValue('D1', 'Tanggal Mulai (YYYY-MM-DD)');
        $sheet->setCellValue('E1', 'Tanggal Selesai (YYYY-MM-DD)');

        $sheet->setCellValue('A2', 'TGS001');
        $sheet->setCellValue('B2', 'Contoh: Pengecekan Server');
        $sheet->setCellValue('C2', 'Mengecek ketersediaan server mingguan yang ada di rak utama.');
        $sheet->setCellValue('D2', date('Y-m-d'));
        $sheet->setCellValue('E2', date('Y-m-d', strtotime('+7 days')));

        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        foreach (range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Template_Import_Tugas.xlsx';

        if (ob_get_length()) {
            ob_end_clean();
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function export()
    {
        $tugas = Tugas::with(['admin', 'penugasan.laporan'])
            ->orderBy('created_at', 'desc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'Kode Tugas',
            'Nama Tugas',
            'Deskripsi',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Admin Pembuat',
            'Status',
        ];

        foreach ($headers as $index => $header) {
            $sheet->setCellValue(chr(65 + $index) . '1', $header);
        }

        $row = 2;

        foreach ($tugas as $item) {
            $sheet->setCellValue('A' . $row, $item->kodetugas);
            $sheet->setCellValue('B' . $row, $item->nama_tugas);
            $sheet->setCellValue('C' . $row, $item->deskripsi);
            $sheet->setCellValue('D' . $row, $item->tanggal_mulai);
            $sheet->setCellValue('E' . $row, $item->tanggal_selesai);
            $sheet->setCellValue('F' . $row, $item->admin->name ?? 'Admin');
            $sheet->setCellValue('G' . $row, $item->status_penugasan_label);
            $row++;
        }

        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Data_Tugas_' . date('Y-m-d') . '.xlsx';

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
        $tugasData = $request->input('tugas');

        if (!$tugasData) {
            return redirect()->route('admin.tugas.index')->with('error', 'Tidak ada data yang diproses.');
        }

        foreach ($tugasData as $index => $row) {
            if (empty($row['nama_tugas'])) {
                continue;
            }

            $tanggalMulai = $this->formatTanggalMySQL($row['tanggal_mulai'] ?? null);
            $tanggalSelesai = $this->formatTanggalMySQL($row['tanggal_selesai'] ?? null);
            $nomorBaris = $index + 1;

            if (!$tanggalMulai || !$tanggalSelesai) {
                return redirect()->route('admin.tugas.index')
                    ->with('error', "Import tugas baris {$nomorBaris} gagal: tanggal mulai dan tanggal selesai wajib valid.");
            }

            if (\Carbon\Carbon::parse($tanggalSelesai)->lt(\Carbon\Carbon::parse($tanggalMulai))) {
                return redirect()->route('admin.tugas.index')
                    ->with('error', "Import tugas baris {$nomorBaris} gagal: tanggal selesai tidak boleh kurang dari tanggal mulai.");
            }

            $kode = !empty($row['kodetugas']) ? trim($row['kodetugas']) : 'TGS' . strtoupper(Str::random(5));

            while (Tugas::where('kodetugas', $kode)->exists()) {
                $kode = 'TGS' . strtoupper(Str::random(5));
            }

            $lampiranPath = null;

            if ($request->hasFile("tugas.{$index}.lampiran")) {
                $lampiranPath = $request->file("tugas.{$index}.lampiran")->store('lampiran_tugas', 'public');
            }

            Tugas::create([
                'kodetugas' => $kode,
                'nama_tugas' => $row['nama_tugas'],
                'deskripsi' => $row['deskripsi'] ?? '-',
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'lampiran' => $lampiranPath,
                'id_admin' => Auth::user()->nip,
            ]);
        }

        return redirect()->route('admin.tugas.index')->with('success', 'Data tugas beserta lampiran berhasil diimport!');
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

    private function dateValidationMessages(): array
    {
        return [
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh kurang dari tanggal mulai. Tanggal selesai boleh sama dengan tanggal mulai.',
        ];
    }
}