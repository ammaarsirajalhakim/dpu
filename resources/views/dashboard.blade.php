@extends('layout.layout')

@section('content')
<div class="space-y-8 animate-fade-in">
    
    <div class="relative bg-white border border-slate-200/80 rounded-3xl p-6 md:p-8 shadow-sm overflow-hidden group animate-slide-up">
        <div class="absolute top-0 right-0 w-80 h-80 bg-gradient-to-tr from-blue-500/5 to-indigo-500/5 rounded-full blur-3xl transition-transform duration-700 group-hover:scale-110"></div>
        
        <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6 z-10">
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-600"></span>
                    </span>
                    <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest animate-pulse">Konsol Utama Agen</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">
                    Selamat Datang di <span class="text-slate-900">POINT</span><span class="text-blue-600">IFY</span>! 👋
                </h1>
                <p class="text-xs font-medium text-slate-500 max-w-3xl leading-relaxed">
                    Sistem Informasi Penugasan Resmi **Dinas Pekerjaan Umum (DPU) Kabupaten Semarang**. 
                    Platform ini berfungsi sebagai instrumen pendukung keputusan (*Decision Support System*) untuk memantau distribusi, perkembangan, dan efisiensi pelaporan proyek infrastruktur daerah secara transparan dan akuntabel.
                </p>
            </div>
            
            <div class="flex items-center gap-3 bg-slate-50 border border-slate-200/60 px-4 py-3 rounded-2xl self-start md:self-auto shadow-inner hover:scale-105 transition-transform">
                <div class="p-2 bg-slate-900 text-yellow-400 rounded-xl shadow-sm">
                    <svg class="w-5 h-5 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707-.707m12.728 0l-.707.707M6.343 6.343l-.707-.707m12.828 5.757a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <div>
                    <div class="text-xs font-black text-slate-800 uppercase tracking-wider">{{ \Carbon\Carbon::now()->translatedFormat('l, d M Y') }}</div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">Waktu Operasional</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <div class="bg-white border-2 border-red-100 hover:border-red-400 rounded-2xl p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md relative overflow-hidden group animate-pop-in" style="animation-delay: 100ms;">
            <div class="absolute top-0 right-0 p-3 bg-red-50 text-red-600 rounded-bl-2xl">
                <svg class="w-5 h-5 {{ $totalLaporSegera > 0 ? 'animate-bounce' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <p class="text-[10px] font-black text-red-500 uppercase tracking-widest">⚠️ Kritis / Mendesak</p>
            <h3 class="text-2xl font-black text-slate-900 mt-2">{{ $totalLaporSegera }} <span class="text-xs font-bold text-slate-400">Tugas</span></h3>
            <p class="text-xs font-bold text-slate-700 mt-1">Lapor Segera (&lt; 24 Jam)</p>
        </div>

        <div class="bg-white border border-slate-200/80 hover:border-blue-500 rounded-2xl p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md relative overflow-hidden group animate-pop-in" style="animation-delay: 200ms;">
            <div class="absolute top-0 right-0 p-3 bg-blue-50 text-blue-600 rounded-bl-2xl">
                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest">💼 Beban Kerja</p>
            <h3 class="text-2xl font-black text-slate-900 mt-2">{{ $totalTugasDikerjakan }} <span class="text-xs font-bold text-slate-400">Tugas</span></h3>
            <p class="text-xs font-bold text-slate-700 mt-1">Total Tugas Aktif</p>
        </div>

        <div class="bg-white border border-slate-200/80 hover:border-amber-500 rounded-2xl p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md relative overflow-hidden group animate-pop-in" style="animation-delay: 300ms;">
            <div class="absolute top-0 right-0 p-3 bg-amber-50 text-amber-600 rounded-bl-2xl">
                <svg class="w-5 h-5 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest">📝 Tindakan Diperlukan</p>
            <h3 class="text-2xl font-black text-slate-900 mt-2">{{ $totalPerluUnggah }} <span class="text-xs font-bold text-slate-400">Laporan</span></h3>
            <p class="text-xs font-bold text-slate-700 mt-1">Belum Lapor / Revisi</p>
        </div>

        <div class="bg-white border border-slate-200/80 hover:border-emerald-500 rounded-2xl p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md relative overflow-hidden group animate-pop-in" style="animation-delay: 400ms;">
            <div class="absolute top-0 right-0 p-3 bg-emerald-50 text-emerald-600 rounded-bl-2xl">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">✅ Validated</p>
            <h3 class="text-2xl font-black text-slate-900 mt-2">{{ $totalSelesai }} <span class="text-xs font-bold text-slate-400">Tugas</span></h3>
            <p class="text-xs font-bold text-slate-700 mt-1">Laporan Disetujui</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden flex flex-col animate-slide-up">
            <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Urutan Prioritas Eksekusi Tugas Lapangan (DSS)
                    </h2>
                    <p class="text-[11px] font-medium text-slate-400 mt-0.5">Sistem DPU menyarankan pengerjaan proyek dari nomor urut teratas untuk efisiensi tenggat.</p>
                </div>
                <a href="{{ route('penugasan.index') }}" class="text-[10px] font-bold text-blue-600 hover:underline shrink-0">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="divide-y divide-slate-100 flex-1">
                @forelse($rekomendasiPrioritas as $index => $p)
                    <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/50 transition-all duration-150 group">
                        <div class="flex gap-4 items-start">
                            <div class="w-8 h-8 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 flex items-center justify-center font-extrabold text-xs flex-shrink-0 relative">
                                {{ $index + 1 }}
                                @if(Carbon\Carbon::parse($p->batas_waktu_lapor)->lte(Carbon\Carbon::now()->addDay()))
                                    <span class="absolute -top-0.5 -right-0.5 flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                    </span>
                                @endif
                            </div>
                            <div class="space-y-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-mono font-bold text-blue-600 text-[11px] bg-blue-50 px-1.5 py-0.5 rounded shadow-sm">{{ $p->kodetugas }}</span>
                                    <h4 class="text-xs font-black text-slate-800 truncate">{{ $p->tugas->nama_tugas ?? '-' }}</h4>
                                </div>
                                <p class="text-[11px] text-slate-400 font-medium leading-relaxed truncate max-w-md">{{ $p->tugas->deskripsi ?? '-' }}</p>
                            </div>
                        </div>
                        
                        <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-2 border-t sm:border-t-0 pt-2 sm:pt-0 border-slate-100 shrink-0">
                            <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-extrabold rounded-md uppercase tracking-wider {{ $p->laporan && $p->laporan->status === 'revisi' ? 'bg-yellow-50 text-yellow-700 border border-yellow-100' : 'bg-red-50 text-red-700 border border-red-100' }}">
                                {{ $p->laporan && $p->laporan->status === 'revisi' ? 'Revisi Dokumen' : 'Batas: ' . Carbon\Carbon::parse($p->batas_waktu_lapor)->locale('id')->diffForHumans() }}
                            </span>
                            
                            <a href="{{ route('penugasan.show', $p->id) }}" class="text-[10px] font-black text-slate-800 group-hover:text-blue-600 flex items-center gap-1 group-hover:underline">
                                BUKA DETAIL
                                <svg class="w-3 h-3 transition-transform duration-200 group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-slate-400 italic font-medium">
                         Hebat! Seluruh laporan tugas lapangan Anda telah selesai diproses.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm flex flex-col overflow-hidden animate-slide-up" style="animation-delay: 150ms;">
            <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Siklus Laporan Lapangan
                    </h2>
                    <p class="text-[11px] font-medium text-slate-400 mt-0.5">Daftar verifikasi fisik tim pengawas DPU.</p>
                </div>
                <a href="{{ route('laporan.index') }}" class="text-[10px] font-bold text-blue-600 hover:underline shrink-0">
                    Riwayat &rarr;
                </a>
            </div>

            <div class="p-4 overflow-y-auto max-h-[360px] divide-y divide-slate-100 flex-1 custom-scrollbar">
                @forelse($statusPelaporan as $sp)
                    <div class="py-3 flex items-center justify-between gap-3 text-xs font-semibold hover:bg-slate-50/30 px-1 rounded-lg transition-colors">
                        <div class="space-y-0.5 min-w-0">
                            <h5 class="text-slate-800 truncate font-bold text-[11px]">{{ $sp['nama_tugas'] }}</h5>
                            <p class="text-[10px] font-mono text-slate-400 uppercase tracking-wider">Kode: {{ $sp['kodetugas'] }}</p>
                        </div>
                        
                        <div>
                            @if($sp['status'] === 'disetujui')
                                <span class="px-2 py-0.5 text-[9px] font-black text-emerald-700 bg-emerald-50 border border-emerald-100 rounded uppercase">Disetujui</span>
                            @elseif($sp['status'] === 'revisi')
                                <span class="px-2 py-0.5 text-[9px] font-black text-amber-700 bg-amber-50 border border-amber-100 rounded uppercase animate-flash-slow">Revisi</span>
                            @elseif($sp['status'] === 'diajukan')
                                <span class="px-2 py-0.5 text-[9px] font-black text-blue-700 bg-blue-50 border border-blue-100 rounded uppercase">Diajukan</span>
                            @else
                                <span class="px-2 py-0.5 text-[9px] font-black text-slate-500 bg-slate-100 border border-slate-200 rounded uppercase">Belum Lapor</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-400 text-xs italic">
                        Belum ada riwayat penugasan terdaftar.
                    </div>
                @endforelse
            </div>
            
            <div class="p-4 bg-slate-50 border-t border-slate-100 mt-auto">
                <div class="flex justify-between items-center text-[11px] font-bold text-slate-600 mb-1.5">
                    <span>Indeks Ketepatan Waktu Lapor</span>
                    <span class="text-blue-600 font-extrabold">{{ $rasioAkurasi }}%</span>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden shadow-inner">
                    <div class="bg-blue-600 h-1.5 rounded-full transition-all duration-1000 ease-out" style="width: {{ $rasioAkurasi }}%"></div>
                </div>
            </div>
        </div>

    </div>

</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes popIn {
        from { opacity: 0; transform: scale(0.96); }
        to { opacity: 1; transform: scale(1); }
    }
    @keyframes flash {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; }
    }
    
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
    .animate-slide-up { animation: slideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .animate-pop-in { opacity: 0; animation: popIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    .animate-flash-slow { animation: flash 2s infinite; }
    .animate-spin-slow { animation: spin 12s linear infinite; }
</style>
@endsection