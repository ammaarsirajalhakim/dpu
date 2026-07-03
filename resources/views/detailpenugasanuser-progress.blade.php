@extends('layout.layout')

@section('content')
@php
    $status = 'belum_lapor';
    if ($penugasan->laporan) {
        $status = $penugasan->laporan->status;
    }
@endphp

<div class="max-w-6xl mx-auto space-y-6 animate-fade-in">
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl text-sm font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-100 rounded-2xl text-sm font-semibold text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 bg-red-50 border border-red-100 rounded-2xl text-sm font-semibold text-red-700">
            <div class="font-bold mb-1">Laporan harian belum bisa disimpan:</div>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="space-y-3">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
                <li><a href="{{ route('penugasan.index') }}" class="hover:text-blue-600 transition-colors">Tugas Saya</a></li>
                <li class="text-slate-300">/</li>
                <li class="text-slate-800 font-bold">Detail Penugasan</li>
            </ol>
        </nav>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div>
                <span class="text-[10px] font-black text-blue-600 bg-blue-50 border border-blue-100 px-2.5 py-0.5 rounded-md uppercase tracking-wider font-mono">
                    KODE: {{ $penugasan->tugas->kodetugas }}
                </span>
                <h1 class="text-xl md:text-2xl font-black text-slate-900 tracking-tight mt-2 leading-tight">
                    {{ $penugasan->tugas->nama_tugas }}
                </h1>
                <p class="text-xs font-medium text-slate-400 mt-1">
                    Periode: {{ $startDate->locale('id')->translatedFormat('d F Y') }} - {{ $endDate->locale('id')->translatedFormat('d F Y') }}
                </p>
            </div>

            <div class="shrink-0 flex flex-col gap-2 items-start md:items-end">
                @if($status === 'disetujui')
                    <span class="px-4 py-2 text-xs font-black text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-xl uppercase tracking-wider">LAPORAN AKHIR DISETUJUI</span>
                @elseif($status === 'revisi')
                    <span class="px-4 py-2 text-xs font-black text-amber-700 bg-amber-50 border border-amber-100 rounded-xl uppercase tracking-wider">LAPORAN AKHIR REVISI</span>
                @elseif($status === 'diajukan')
                    <span class="px-4 py-2 text-xs font-black text-blue-700 bg-blue-50 border border-blue-100 rounded-xl uppercase tracking-wider">LAPORAN AKHIR DITINJAU</span>
                @else
                    <span class="px-4 py-2 text-xs font-black text-slate-600 bg-slate-50 border border-slate-200 rounded-xl uppercase tracking-wider">SEDANG BERJALAN</span>
                @endif
                <span class="text-[10px] font-bold {{ $missingCount > 0 ? 'text-red-600' : 'text-emerald-600' }} uppercase tracking-widest">
                    {{ $missingCount }} hari belum dilaporkan
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-3">Deskripsi Detail & Instruksi Proyek</h3>
                <div class="text-xs font-medium text-slate-600 leading-relaxed whitespace-pre-line bg-slate-50/50 p-4 rounded-xl border border-slate-100 shadow-inner">
                    {!! nl2br(e($penugasan->tugas->deskripsi ?? 'Tidak ada instruksi deskripsi tambahan dari pengawas teknis.')) !!}
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-3">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">Track Record Laporan Harian</h3>
                    <div class="flex flex-wrap gap-2 text-[10px] font-bold uppercase">
                        <span class="inline-flex items-center gap-1 text-emerald-700"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>Sudah</span>
                        <span class="inline-flex items-center gap-1 text-red-700"><span class="w-2 h-2 rounded-full bg-red-500"></span>Belum</span>
                        <span class="inline-flex items-center gap-1 text-slate-500"><span class="w-2 h-2 rounded-full bg-slate-300"></span>Menunggu</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-3">
                    @foreach($calendarDays as $day)
                        @php
                            $boxClass = match($day['status']) {
                                'sudah_lapor' => 'bg-emerald-50 border-emerald-200 text-emerald-800',
                                'belum_lapor' => 'bg-red-50 border-red-200 text-red-800',
                                default => 'bg-slate-50 border-slate-200 text-slate-400',
                            };
                            $badgeText = match($day['status']) {
                                'sudah_lapor' => 'Sudah',
                                'belum_lapor' => 'Belum',
                                default => 'Nanti',
                            };
                        @endphp
                        <div class="border rounded-2xl p-3 {{ $boxClass }}">
                            <div class="text-[10px] font-black uppercase tracking-widest">{{ $day['date']->locale('id')->translatedFormat('D') }}</div>
                            <div class="text-lg font-black leading-tight">{{ $day['date']->format('d') }}</div>
                            <div class="text-[10px] font-bold mt-1 uppercase">{{ $badgeText }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-3">Riwayat Laporan Harian</h3>
                <div class="space-y-3">
                    @forelse($penugasan->dailyProgressReports as $report)
                        <div class="p-4 rounded-2xl border border-emerald-100 bg-emerald-50/40">
                            <div class="flex items-center justify-between gap-3 mb-2">
                                <p class="text-xs font-black text-emerald-800 uppercase tracking-wider">{{ $report->tanggal_laporan->locale('id')->translatedFormat('d F Y') }}</p>
                                <span class="text-[10px] font-black text-emerald-700 bg-white border border-emerald-100 px-2 py-1 rounded-lg uppercase">Terlapor</span>
                            </div>
                            <p class="text-xs font-medium text-slate-700 leading-relaxed whitespace-pre-line">{{ $report->progres }}</p>
                            @if($report->kendala)
                                <p class="text-[11px] font-semibold text-amber-700 mt-2">Kendala: {{ $report->kendala }}</p>
                            @endif
                            @if($report->rencana_lanjut)
                                <p class="text-[11px] font-semibold text-blue-700 mt-1">Rencana lanjut: {{ $report->rencana_lanjut }}</p>
                            @endif
                            @if($report->file_path)
                                <a href="{{ asset('storage/' . $report->file_path) }}" target="_blank" class="inline-flex mt-3 text-[10px] font-black text-blue-700 bg-blue-50 border border-blue-100 px-3 py-1.5 rounded-lg uppercase tracking-wider">
                                    Lihat Lampiran: {{ $report->file_name ?? 'Berkas' }}
                                </a>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic text-center py-6">Belum ada laporan harian yang dibuat.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-4">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">Aksi Laporan</h3>

                <a href="#form-laporan-harian" class="w-full inline-flex justify-center px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-md shadow-blue-500/10">
                    Buat Laporan Harian
                </a>

                @if($isFinalReportAllowed && !$isDeadlinePassed)
                    <a href="{{ route('laporan.create', $penugasan->id) }}" class="w-full inline-flex justify-center px-4 py-3 bg-slate-900 hover:bg-black text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-md shadow-slate-900/10">
                        Buat Laporan Akhir
                    </a>
                @elseif($isFinalReportAllowed && $isDeadlinePassed)
                    <div class="p-4 rounded-xl bg-red-50 border border-red-100 text-xs font-semibold text-red-700">
                        Laporan harian sudah lengkap, tetapi batas waktu laporan akhir sudah lewat. Ajukan perpanjangan waktu untuk membuka kembali akses laporan akhir.
                    </div>
                @else
                    <div class="p-4 rounded-xl bg-amber-50 border border-amber-100 text-xs font-semibold text-amber-700">
                        Laporan akhir baru bisa dibuat setelah semua laporan harian dalam periode tugas lengkap.
                    </div>
                @endif

                @if($isDeadlinePassed && $status === 'belum_lapor')
                    <form action="{{ route('laporan.ajukanPerpanjangan', $penugasan->id) }}" method="POST" class="space-y-3">
                        @csrf
                        <textarea name="alasan_keterlambatan" rows="4" required class="w-full px-4 py-3 rounded-xl border border-red-200 bg-red-50 text-xs font-medium text-slate-800 focus:bg-white focus:border-red-500 outline-none resize-none" placeholder="Jelaskan alasan membutuhkan perpanjangan waktu..."></textarea>
                        <button type="submit" class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-black uppercase tracking-wider">
                            Ajukan Perpanjangan Waktu
                        </button>
                    </form>
                    @if($extensionRequest && $extensionRequest->status_keterlambatan === 'mengajukan')
                        <div class="p-3 bg-amber-50 border border-amber-100 rounded-xl text-[11px] font-semibold text-amber-700">
                            Permohonan perpanjangan sudah dikirim dan menunggu keputusan admin.
                        </div>
                    @endif
                @endif
            </div>

            <div id="form-laporan-harian" class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-4">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">Buat Laporan Harian</h3>

                @if($missingCount > 0)
                    <div class="bg-red-50 border border-red-100 p-4 rounded-xl text-xs font-semibold text-red-700">
                        Ada {{ $missingCount }} hari dalam periode tugas yang belum memiliki laporan harian.
                    </div>
                @else
                    <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl text-xs font-semibold text-emerald-700">
                        Semua hari sampai hari ini sudah dilaporkan.
                    </div>
                @endif

                <form action="{{ route('daily-progress.store', $penugasan->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Tanggal Laporan</label>
                        <input type="date" name="tanggal_laporan" value="{{ $selectedDate }}" min="{{ $startDate->toDateString() }}" max="{{ $endDate->toDateString() }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold text-slate-800 focus:bg-white focus:border-blue-600 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Progres Hari Ini</label>
                        <textarea name="progres" rows="5" required class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium text-slate-800 focus:bg-white focus:border-blue-600 outline-none resize-none" placeholder="Tulis progres pekerjaan pada tanggal tersebut...">{{ old('progres') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Kendala</label>
                        <textarea name="kendala" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium text-slate-800 focus:bg-white focus:border-blue-600 outline-none resize-none" placeholder="Opsional">{{ old('kendala') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Rencana Lanjut</label>
                        <textarea name="rencana_lanjut" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium text-slate-800 focus:bg-white focus:border-blue-600 outline-none resize-none" placeholder="Opsional">{{ old('rencana_lanjut') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Lampiran Harian</label>
                        <input type="file" name="file_laporan_harian" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium text-slate-700 focus:bg-white focus:border-blue-600 outline-none">
                        <p class="text-[10px] text-slate-400 mt-1">Opsional. Maksimal 5MB.</p>
                    </div>
                    <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-md shadow-blue-500/10">Simpan Laporan Harian</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
</style>
@endsection
