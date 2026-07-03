@extends('layout.layout')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 animate-fade-in">
    
    <div class="space-y-3 animate-slide-up">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
                <li>
                    <a href="{{ route('laporan.index') }}" class="hover:text-blue-600 transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2z"/>
                        </svg>
                        Riwayat Laporan
                    </a>
                </li>
                <li>
                    <svg class="w-3 h-3 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                </li>
                <li class="text-slate-800 font-bold">Rincian Berkas Pengajuan</li>
            </ol>
        </nav>

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div>
                <span class="text-[10px] font-black text-blue-600 bg-blue-50 border border-blue-100 px-2.5 py-0.5 rounded-md uppercase tracking-wider font-mono">
                    ID LAPORAN: #LPR-{{ $laporan->id }}
                </span>
                <h1 class="text-xl md:text-2xl font-black text-slate-900 tracking-tight mt-2 leading-tight">
                    {{ $laporan->penugasan->tugas->nama_tugas ?? 'Detail Laporan Penugasan' }}
                </h1>
                <p class="text-xs font-medium text-slate-400 mt-1">Diajukan ke database log sistem DPU Kabupaten Semarang.</p>
            </div>
            
            <div class="self-start md:self-auto shrink-0">
                @if($laporan->status === 'disetujui')
                    <span class="px-4 py-2 text-xs font-black text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-xl uppercase tracking-wider shadow-sm">VALID / DISETUJUI</span>
                @elseif($laporan->status === 'revisi')
                    <span class="px-4 py-2 text-xs font-black text-amber-700 bg-amber-50 border border-amber-100 rounded-xl uppercase tracking-wider shadow-sm animate-pulse">PERLU REVISI</span>
                @elseif($laporan->status === 'diajukan' || $laporan->status === 'menunggu')
                    <span class="px-4 py-2 text-xs font-black text-blue-700 bg-blue-50 border border-blue-100 rounded-xl uppercase tracking-wider shadow-sm">SEDANG DITINJAU</span>
                @else
                    <span class="px-4 py-2 text-xs font-black text-slate-600 bg-slate-50 border border-slate-200 rounded-xl uppercase tracking-wider shadow-sm">BELUM DIAJUKAN</span>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-start gap-3 animate-slide-up">
        <div class="p-2 bg-emerald-500/10 rounded-xl">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <div>
            <h3 class="text-sm font-bold text-emerald-800">Berhasil!</h3>
            <p class="text-sm font-medium text-emerald-600 mt-0.5">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 bg-red-50 border border-red-100 rounded-2xl flex items-start gap-3 animate-slide-up">
        <div class="p-2 bg-red-500/10 rounded-xl">
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div>
            <h3 class="text-sm font-bold text-red-800">Gagal</h3>
            <p class="text-sm font-medium text-red-600 mt-0.5">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <div class="lg:col-span-2 space-y-6 animate-slide-up" style="animation-delay: 100ms;">
            
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-2 border-b border-slate-100 pb-3">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Uraian & Catatan Progress Kerja Agen
                </h3>
                <div class="text-xs font-medium text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50/50 p-5 rounded-xl border border-slate-100 shadow-inner">
                    {!! nl2br(e($laporan->teks_laporan ?? $laporan->deskripsi)) !!}
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-2 border-b border-slate-100 pb-3">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Lampiran Dokumentasi / Berkas Pendukung Fisik
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @forelse($laporan->files ?? [] as $file)
                        <div class="flex items-center justify-between p-3.5 bg-white border border-slate-200/80 rounded-xl hover:border-blue-400 hover:shadow-sm transition-all group">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg shrink-0 font-mono text-[10px] font-black uppercase tracking-wider">
                                    {{ pathinfo($file->file_path, PATHINFO_EXTENSION) ?: 'DOC' }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-800 truncate group-hover:text-blue-600 transition-colors">
                                        {{ basename($file->file_path) }}
                                    </p>
                                    <p class="text-[9px] font-mono text-slate-400 uppercase mt-0.5">Berkas Lampiran</p>
                                </div>
                            </div>
                            
                            <a href="{{ asset('storage/' . $file->file_path) }}" download
                               class="p-2 bg-slate-50 group-hover:bg-slate-900 group-hover:text-white rounded-lg text-slate-400 transition-colors shadow-sm"
                               title="Unduh Berkas Lampiran Ini">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            </a>
                        </div>
                    @empty
                        <div class="col-span-1 sm:col-span-2 text-center py-6 text-slate-400 italic text-xs">
                            Tidak ada file lampiran berkas dukung digital dalam pengajuan laporan ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-2 border-b border-slate-100 pb-3">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Ruang Diskusi & Revisi
                </h3>
                
                <div class="space-y-4 max-h-96 overflow-y-auto pr-2 pb-2">
                    @forelse($laporan->chats ?? [] as $chat)
                        @if($chat->is_from_admin_panel)
                            <div class="flex flex-col items-start gap-1 w-full">
                                <span class="text-[10px] font-bold text-slate-400 ml-2 uppercase tracking-wider">Reviewer / Admin • {{ \Carbon\Carbon::parse($chat->created_at)->format('d M, H:i') }}</span>
                                <div class="bg-slate-100 text-slate-700 px-4 py-3 rounded-2xl rounded-tl-sm text-xs font-medium border border-slate-200/60 max-w-[85%] leading-relaxed">
                                    {{ $chat->pesan }}
                                </div>
                            </div>
                        @else
                            <div class="flex flex-col items-end gap-1 w-full">
                                <span class="text-[10px] font-bold text-slate-400 mr-2 uppercase tracking-wider">Anda • {{ \Carbon\Carbon::parse($chat->created_at)->format('d M, H:i') }}</span>
                                <div class="bg-blue-600 text-white px-4 py-3 rounded-2xl rounded-tr-sm text-xs font-medium shadow-sm shadow-blue-500/20 max-w-[85%] leading-relaxed">
                                    {{ $chat->pesan }}
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="text-center py-8">
                            <div class="w-12 h-12 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </div>
                            <p class="text-slate-400 text-xs font-medium">Belum ada catatan revisi atau diskusi.</p>
                        </div>
                    @endforelse
                </div>

                @if($laporan->status !== 'revisi')
                <form action="{{ route('laporan.chat.store') }}" method="POST" class="mt-4 flex gap-2 border-t border-slate-100 pt-4">
                    @csrf
                    <input type="hidden" name="id_laporan" value="{{ $laporan->id }}">
                    <input type="text" name="pesan" placeholder="Ketik pesan atau tanggapan revisi Anda di sini..." required
                           class="w-full px-4 py-2.5 text-xs font-medium text-slate-800 bg-slate-50 border border-slate-200 rounded-xl focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all outline-none">
                    <button type="submit" class="shrink-0 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-sm shadow-blue-500/20 text-xs font-black uppercase tracking-wider transition-all hover:-translate-y-0.5">
                        Kirim
                    </button>
                </form>
                @endif
            </div>

        </div>

        <div class="space-y-6 animate-slide-up" style="animation-delay: 150ms;">
            
            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-4">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-2 border-b border-slate-100 pb-3">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Kronologi Validasi
                </h3>
                
                <div class="space-y-4 text-xs font-medium">
                    <div class="space-y-1">
                        <span class="text-slate-400 block text-[10px] font-bold uppercase tracking-wider">Tanggal Pengajuan Berkas</span>
                        <span class="text-slate-800 font-bold block bg-slate-50 border border-slate-100 px-3 py-1.5 rounded-lg font-mono">
                            {{ \Carbon\Carbon::parse($laporan->created_at)->locale('id')->translatedFormat('d F Y, H:i') }} WIB
                        </span>
                    </div>

                    <div class="space-y-1">
                        <span class="text-slate-400 block text-[10px] font-bold uppercase tracking-wider">Pembaruan Verifikasi Terakhir</span>
                        <span class="text-slate-800 font-bold block bg-slate-50 border border-slate-100 px-3 py-1.5 rounded-lg font-mono">
                            {{ \Carbon\Carbon::parse($laporan->updated_at)->locale('id')->translatedFormat('d F Y, H:i') }} WIB
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-4">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Rekomendasi Tindakan DSS
                </h3>

                @if($laporan->status === 'revisi')
                    <div class="bg-amber-50/50 border border-amber-100 p-4 rounded-xl space-y-4">
                        <p class="text-xs font-semibold text-amber-800 leading-normal">
                            Laporan membutuhkan tindakan revisi. Anda dapat mengunggah berkas baru serta mengirimkan tanggapan perbaikan secara instan di bawah ini.
                        </p>
                        
                        <form action="{{ route('laporan.submitRevisi', $laporan->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-[10px] font-bold text-amber-900 uppercase tracking-wider mb-1">Catatan Perbaikan</label>
                                <textarea name="pesan" rows="3" required class="w-full px-3 py-2 text-xs font-medium text-slate-700 bg-white border border-slate-200 rounded-xl focus:border-amber-500 outline-none resize-none" placeholder="Tulis penjelasan singkat mengenai perbaikan laporan Anda..."></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-[10px] font-bold text-amber-900 uppercase tracking-wider mb-1">Lampirkan Berkas Baru</label>
                                <input type="file" name="file_laporan[]" multiple class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200 cursor-pointer">
                            </div>
                            
                            <button type="submit" class="w-full px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-md shadow-amber-500/10">
                                KIRIM REVISI LAPORAN
                            </button>
                        </form>
                    </div>
                @elseif($laporan->status === 'disetujui')
                    <div class="bg-emerald-50/40 border border-emerald-100 p-4 rounded-xl text-center space-y-2">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <p class="text-xs font-black text-emerald-800 uppercase tracking-wider">ARSIP LAPORAN KUAT</p>
                        <p class="text-[11px] font-medium text-emerald-600/80 leading-normal">Pekerjaan divalidasi penuh. Tidak ada aksi lanjutan yang diperlukan dari akun Anda.</p>
                    </div>
                @else
                    <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl text-center space-y-2 shadow-inner">
                        <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-1 animate-spin-slow">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18"/></svg>
                        </div>
                        <p class="text-xs font-bold text-slate-800">Menunggu Antrean</p>
                        <p class="text-[11px] font-medium text-slate-400 leading-normal">Data laporan terkunci aman dalam antrean reviu berkas fisik DPU Kabupaten Semarang.</p>
                    </div>
                @endif
            </div>

        </div>
        
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    
    .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
    .animate-slide-up { animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .animate-spin-slow { animation: spin 12s linear infinite; }
</style>
@endsection