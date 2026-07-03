@extends('layout.layout')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 animate-fade-in">
    
    <div class="space-y-3 animate-slide-up">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
                <li>
                    <a href="{{ route('penugasan.index') }}" class="hover:text-blue-600 transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Tugas Saya
                    </a>
                </li>
                <li>
                    <svg class="w-3 h-3 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                </li>
                <li class="text-slate-800 font-bold">Detail Berkas Teknis</li>
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
                <p class="text-xs font-medium text-slate-400 mt-1">Diterbitkan resmi oleh Administrator Pengawas DPU Kabupaten Semarang.</p>
            </div>
            
            <div class="self-start md:self-auto shrink-0">
                @php
                    $status = 'belum_lapor';
                    if ($penugasan->laporan) {
                        $status = $penugasan->laporan->status; // diajukan, revisi, disetujui
                    }
                @endphp

                @if($status === 'disetujui')
                    <span class="px-4 py-2 text-xs font-black text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-xl uppercase tracking-wider shadow-sm">VALID / SELESAI</span>
                @elseif($status === 'revisi')
                    <span class="px-4 py-2 text-xs font-black text-amber-700 bg-amber-50 border border-amber-100 rounded-xl uppercase tracking-wider shadow-sm animate-pulse">PERLU REVISI</span>
                @elseif($status === 'diajukan')
                    <span class="px-4 py-2 text-xs font-black text-blue-700 bg-blue-50 border border-blue-100 rounded-xl uppercase tracking-wider shadow-sm">SEDANG DITINJAU</span>
                @else
                    <span class="px-4 py-2 text-xs font-black text-slate-600 bg-slate-50 border border-slate-200 rounded-xl uppercase tracking-wider shadow-sm">SEDANG BERJALAN</span>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <div class="lg:col-span-2 space-y-6 animate-slide-up" style="animation-delay: 100ms;">
            
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-2 border-b border-slate-100 pb-3">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                    </svg>
                    Deskripsi Detail & Instruksi Proyek
                </h3>
                <div class="text-xs font-medium text-slate-600 leading-relaxed whitespace-pre-line bg-slate-50/50 p-4 rounded-xl border border-slate-100 font-sans shadow-inner">
                    {!! nl2br(e($penugasan->tugas->deskripsi ?? 'Tidak ada instruksi deskripsi tambahan dari pengawas teknis.')) !!}
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-2 border-b border-slate-100 pb-3">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Jadwal Batas Waktu Pelaporan
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex items-center p-4 bg-slate-50/80 border border-slate-200/60 rounded-xl shadow-inner">
                        <div class="p-2.5 bg-slate-900 text-white rounded-lg mr-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tanggal Terbit Tugas</p>
                            <p class="text-xs font-black text-slate-800 mt-0.5">{{ \Carbon\Carbon::parse($penugasan->created_at)->locale('id')->translatedFormat('d F Y') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center p-4 bg-red-50/50 border border-red-100 rounded-xl shadow-sm">
                        <div class="p-2.5 bg-red-600 text-white rounded-lg mr-3 shadow-md shadow-red-500/10">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-red-500 uppercase tracking-wider">Tenggat Pengisian Dokumen</p>
                            <p class="text-xs font-black text-red-700 mt-0.5">{{ \Carbon\Carbon::parse($penugasan->batas_waktu_lapor)->locale('id')->translatedFormat('d F Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="space-y-6 animate-slide-up" style="animation-delay: 150ms;">
            
            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-4">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Status Tindakan Laporan
                </h3>

                @if($status === 'belum_lapor')
                    <div class="bg-blue-50/50 border border-blue-100 p-4 rounded-xl text-center space-y-3">
                        <p class="text-xs font-semibold text-blue-800 leading-normal">Pekerjaan fisik lapangan sudah rampung? Segera ajukan ringkasan bukti berkas pelaporan Anda.</p>
                        <a href="{{ route('laporan.create', $penugasan->id) }}" 
                           class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-md shadow-blue-500/10">
                            Buat Laporan Sekarang
                        </a>
                    </div>
                @elseif($status === 'revisi')
                    <div class="bg-amber-50/50 border border-amber-100 p-4 rounded-xl text-center space-y-3 animate-pulse">
                        <p class="text-xs font-semibold text-amber-800 leading-normal">Laporan ditolak oleh pengawas. Silakan buka catatan revisi admin lalu perbaiki dokumen di bawah ini.</p>
                        <a href="{{ route('laporan.create', $penugasan->id) }}" 
                           class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-md shadow-amber-500/10">
                            Perbaiki Laporan Anda
                        </a>
                    </div>
                @elseif($status === 'diajukan')
                    <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl text-center space-y-2 shadow-inner">
                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mx-auto mb-1 animate-spin-slow">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18"/></svg>
                        </div>
                        <p class="text-xs font-bold text-slate-800">Berkas Sedang Diperiksa</p>
                        <p class="text-[11px] font-medium text-slate-400 leading-normal">Dokumen Anda sedang dalam antrean verifikasi tim administrasi DPU.</p>
                    </div>
                @elseif($status === 'disetujui')
                    <div class="bg-emerald-50/40 border border-emerald-100 p-4 rounded-xl text-center space-y-2">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <p class="text-xs font-black text-emerald-800 uppercase tracking-wider">Tugas Selesai Sempurna</p>
                        <p class="text-[11px] font-medium text-emerald-600/80 leading-normal">Dokumen terverifikasi 100% tepat waktu. Poin kinerja sudah diakumulasikan ke akun Anda.</p>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-4">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-2 border-b border-slate-100 pb-3">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Rekan Tim Pelaksana
                </h3>
                
                <div class="space-y-3">
                    @forelse($penugasan->anggota as $anggota)
                        <div class="flex items-center gap-3 p-2 hover:bg-slate-50 rounded-xl transition-colors duration-150 group">
                            <div class="w-9 h-9 rounded-xl bg-slate-100 group-hover:bg-white group-hover:border-blue-200 border border-slate-200/60 flex items-center justify-center text-slate-700 font-extrabold text-[11px] transition-colors shadow-sm shrink-0">
                                {{ strtoupper(substr($anggota->user->name ?? '?', 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-800 truncate group-hover:text-blue-600 transition-colors">{{ $anggota->user->name ?? 'Pegawai Tidak Dikenal' }}</p>
                                <p class="text-[10px] font-mono text-slate-400 mt-0.5 uppercase tracking-wider">NIP. {{ $anggota->user->nip ?? '-' }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic text-center py-4">Belum ada anggota tim terdaftar.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    
    .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
    .animate-slide-up { animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .animate-spin-slow { animation: spin 10s linear infinite; }
</style>
@endsection