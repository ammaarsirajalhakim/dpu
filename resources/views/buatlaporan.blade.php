@extends('layout.layout')

@section('content')
@php
    $deadline = $batas_waktu ?? ($anggota->custom_deadline ?? $penugasan->batas_waktu_lapor ?? $penugasan->batas_lapor ?? null);
@endphp

<div class="max-w-3xl mx-auto space-y-6 animate-fade-in">
    <div class="space-y-3 animate-slide-up">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
                <li><a href="{{ route('penugasan.index') }}" class="hover:text-blue-600 transition-colors">Tugas Saya</a></li>
                <li class="text-slate-300">/</li>
                <li>Kirim Laporan Akhir</li>
            </ol>
        </nav>

        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Kirim Laporan Akhir</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Laporan akhir hanya dapat dibuat setelah seluruh laporan harian pada periode tugas sudah lengkap.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl text-sm font-semibold text-emerald-700">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-100 rounded-2xl text-sm font-semibold text-red-700">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 text-xs font-semibold rounded-xl space-y-1 shadow-sm">
            <div class="font-bold text-red-700 mb-1">Form laporan akhir belum lengkap:</div>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden relative">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-indigo-500"></div>

        @if(!$is_waktu_habis)
            <form action="{{ route('laporan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id_penugasan" value="{{ $penugasan->id }}">

                <div class="p-6 sm:p-8 space-y-6">
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 flex items-start gap-4">
                        <div class="p-3 bg-white rounded-xl shadow-sm border border-slate-200">
                            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Informasi Tugas</h4>
                            <p class="text-base font-bold text-slate-800">{{ $penugasan->tugas->nama_tugas ?? '-' }}</p>
                            @if($deadline)
                                <p class="text-sm font-medium text-slate-500 mt-1">Batas Waktu Laporan Akhir: <span class="text-indigo-600">{{ \Carbon\Carbon::parse($deadline)->format('d M Y, H:i') }}</span></p>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label for="judul" class="block text-sm font-bold text-slate-700">Judul Laporan Akhir <span class="text-slate-400 text-xs">(Opsional)</span></label>
                        <input type="text" name="judul" id="judul" value="{{ old('judul') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-medium text-slate-800 placeholder-slate-400 bg-slate-50 focus:bg-white" placeholder="Contoh: Laporan Akhir Penyelesaian Survei Lapangan">
                    </div>

                    <div class="space-y-1">
                        <label for="teks_laporan" class="block text-sm font-bold text-slate-700">Isi Laporan Akhir <span class="text-red-500">*</span></label>
                        <textarea name="teks_laporan" id="teks_laporan" rows="7" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-medium text-slate-800 placeholder-slate-400 bg-slate-50 focus:bg-white resize-none" placeholder="Jelaskan rangkuman akhir pekerjaan, capaian, hasil, dan bukti penyelesaian...">{{ old('teks_laporan') }}</textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-700">Lampiran Laporan Akhir <span class="text-slate-400 text-xs">(Opsional)</span></label>
                        <input type="file" name="file_laporan[]" id="files" multiple class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-sm font-medium text-slate-700 focus:bg-white focus:border-blue-500 outline-none" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip">
                        <p class="text-[10px] font-mono text-slate-400 uppercase" id="file-info">Belum ada file yang dipilih</p>
                    </div>
                </div>

                <div class="p-6 sm:p-8 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('penugasan.show', $penugasan->id) }}" class="px-6 py-2.5 text-xs font-bold text-slate-500 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl transition-colors">BATALKAN</a>
                    <button type="submit" class="px-6 py-2.5 text-xs font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 rounded-xl shadow-md shadow-blue-500/10 transition-all duration-200 hover:-translate-y-0.5">KIRIM LAPORAN AKHIR</button>
                </div>
            </form>
        @else
            <div class="p-12 text-center space-y-4">
                <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <h3 class="text-sm font-bold text-slate-800">Batas Waktu Laporan Akhir Habis</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto leading-relaxed">Anda belum dapat mengirim laporan akhir karena batas waktu sudah terlewat. Ajukan perpanjangan waktu dari halaman detail penugasan.</p>
                <a href="{{ route('penugasan.show', $penugasan->id) }}" class="inline-flex px-6 py-2.5 bg-slate-900 hover:bg-black text-white text-xs font-bold rounded-xl uppercase tracking-wider transition-all">Kembali Ke Detail Penugasan</a>
            </div>
        @endif
    </div>
</div>

<script>
    const fileInput = document.getElementById('files');
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const fileCount = e.target.files.length;
            const infoText = document.getElementById('file-info');
            infoText.textContent = fileCount > 0 ? fileCount + ' berkas siap diunggah' : 'Belum ada file yang dipilih';
        });
    }
</script>

<style>
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.4s ease-out; }
    .animate-slide-up { animation: slideUp 0.4s ease-out forwards; }
</style>
@endsection
