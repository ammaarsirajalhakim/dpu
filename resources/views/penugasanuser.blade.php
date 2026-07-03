@extends('layout.layout')

@section('content')
<div class="space-y-6 animate-fade-in">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm animate-slide-up">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Daftar Penugasan Saya
            </h1>
            <p class="text-xs font-medium text-slate-400 mt-1">Kelola laporan harian dan laporan akhir berdasarkan periode tugas yang diberikan.</p>
        </div>

        <div class="text-xs font-bold text-slate-700 bg-slate-100 border border-slate-200 px-4 py-2 rounded-xl shadow-inner self-start md:self-auto">
            Beban Kerja: {{ $penugasans->count() }} Penugasan
        </div>
    </div>

    <div class="bg-white p-2 rounded-xl border border-slate-200/80 shadow-sm flex flex-wrap gap-1 animate-slide-up" style="animation-delay: 50ms;">
        <button onclick="filterTugas('all')" id="btn-all" class="filter-btn px-4 py-2 text-xs font-bold rounded-lg bg-slate-900 text-white transition-all duration-200 shadow-sm">SEMUA TUGAS</button>
        <button onclick="filterTugas('belum_lapor')" id="btn-belum_lapor" class="filter-btn px-4 py-2 text-xs font-bold rounded-lg text-slate-600 hover:bg-slate-100 transition-all duration-200">BELUM LAPOR AKHIR</button>
        <button onclick="filterTugas('diajukan')" id="btn-diajukan" class="filter-btn px-4 py-2 text-xs font-bold rounded-lg text-slate-600 hover:bg-slate-100 transition-all duration-200">DIAJUKAN</button>
        <button onclick="filterTugas('revisi')" id="btn-revisi" class="filter-btn px-4 py-2 text-xs font-bold rounded-lg text-slate-600 hover:bg-slate-100 transition-all duration-200">PERLU REVISI</button>
        <button onclick="filterTugas('disetujui')" id="btn-disetujui" class="filter-btn px-4 py-2 text-xs font-bold rounded-lg text-slate-600 hover:bg-slate-100 transition-all duration-200">DISETUJUI</button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-slide-up" style="animation-delay: 100ms;" id="container-tugas">
        @forelse($penugasans as $p)
            @php
                $status = $p->laporan ? $p->laporan->status : 'belum_lapor';
                $startDate = \Carbon\Carbon::parse($p->tugas->tanggal_mulai ?? $p->created_at)->startOfDay();
                $endDate = \Carbon\Carbon::parse($p->tugas->tanggal_selesai ?? $p->batas_waktu_lapor)->startOfDay();
                $reportedDates = ($p->dailyProgressReports ?? collect())->pluck('tanggal_laporan')->map(fn($date) => \Carbon\Carbon::parse($date)->toDateString())->toArray();
                $missingDaily = 0;
                foreach (\Carbon\CarbonPeriod::create($startDate, $endDate) as $date) {
                    if ($date->lte(now()) && !in_array($date->toDateString(), $reportedDates, true)) {
                        $missingDaily++;
                    }
                }
                $isDailyComplete = $missingDaily === 0;
                $isDeadlinePassed = now()->greaterThan(\Carbon\Carbon::parse($p->batas_waktu_lapor));
                $isMendesak = \Carbon\Carbon::parse($p->batas_waktu_lapor)->lte(\Carbon\Carbon::now()->addDay()) && $status !== 'disetujui';
            @endphp

            <div class="tugas-card bg-white border {{ $isMendesak ? 'border-red-200 shadow-red-50/50 shadow-md animate-pop-in' : 'border-slate-200/80 shadow-sm' }} rounded-2xl p-6 transition-all duration-300 hover:-translate-y-1 hover:shadow-md flex flex-col justify-between group relative overflow-hidden" data-status="{{ $status }}">
                @if($isMendesak)
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-red-500 to-amber-500"></div>
                @endif

                <div class="space-y-4">
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-mono font-bold text-blue-600 text-[11px] bg-blue-50 border border-blue-100 px-2 py-0.5 rounded-md shadow-inner">{{ $p->kodetugas }}</span>
                        <div>
                            @if($status === 'disetujui')
                                <span class="px-2.5 py-1 text-[10px] font-black text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-md uppercase tracking-wider">Laporan Akhir Disetujui</span>
                            @elseif($status === 'revisi')
                                <span class="px-2.5 py-1 text-[10px] font-black text-amber-700 bg-amber-50 border border-amber-100 rounded-md uppercase tracking-wider animate-pulse">Laporan Akhir Revisi</span>
                            @elseif($status === 'diajukan')
                                <span class="px-2.5 py-1 text-[10px] font-black text-blue-700 bg-blue-50 border border-blue-100 rounded-md uppercase tracking-wider">Laporan Akhir Diajukan</span>
                            @else
                                <span class="px-2.5 py-1 text-[10px] font-black text-slate-500 bg-slate-100 border border-slate-200 rounded-md uppercase tracking-wider">Belum Laporan Akhir</span>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-1">
                        <h3 class="text-sm font-black text-slate-900 group-hover:text-blue-600 transition-colors leading-snug">{{ $p->tugas->nama_tugas ?? '-' }}</h3>
                        <p class="text-xs text-slate-400 font-medium leading-relaxed line-clamp-3">{{ $p->tugas->deskripsi ?? 'Tidak ada deskripsi berkas teknis untuk tugas ini.' }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-[10px] font-bold">
                        <div class="p-3 rounded-xl {{ $missingDaily > 0 ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-emerald-50 text-emerald-700 border border-emerald-100' }}">
                            {{ $missingDaily }} laporan harian belum dibuat
                        </div>
                        <div class="p-3 rounded-xl {{ $isDeadlinePassed ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-slate-50 text-slate-600 border border-slate-100' }}">
                            Batas: {{ \Carbon\Carbon::parse($p->batas_waktu_lapor)->locale('id')->diffForHumans() }}
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100 flex flex-col gap-2">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <a href="{{ route('penugasan.show', $p->id) }}" class="px-3.5 py-2 text-center text-[10px] font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all uppercase tracking-wider shadow-sm">Detail</a>
                        <a href="{{ route('penugasan.show', $p->id) }}#form-laporan-harian" class="px-3.5 py-2 text-center text-[10px] font-black text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-md shadow-blue-500/10 transition-all uppercase tracking-wider">Laporan Harian</a>
                        @if($isDailyComplete && !$isDeadlinePassed && $status === 'belum_lapor')
                            <a href="{{ route('laporan.create', $p->id) }}" class="px-3.5 py-2 text-center text-[10px] font-black text-white bg-slate-900 hover:bg-black rounded-xl shadow-md shadow-slate-900/10 transition-all uppercase tracking-wider">Laporan Akhir</a>
                        @elseif($status === 'revisi')
                            <a href="{{ route('laporan.create', $p->id) }}" class="px-3.5 py-2 text-center text-[10px] font-black text-white bg-amber-500 hover:bg-amber-600 rounded-xl shadow-md shadow-amber-500/10 transition-all uppercase tracking-wider">Revisi Akhir</a>
                        @else
                            <span class="px-3.5 py-2 text-center text-[10px] font-black text-slate-400 bg-slate-50 border border-slate-100 rounded-xl uppercase tracking-wider cursor-not-allowed">Akhir Terkunci</span>
                        @endif
                    </div>

                    @if($isDeadlinePassed && $status === 'belum_lapor')
                        <a href="{{ route('penugasan.show', $p->id) }}" class="text-center px-3.5 py-2 text-[10px] font-black text-red-700 bg-red-50 border border-red-100 hover:bg-red-100 rounded-xl uppercase tracking-wider">
                            Ajukan Perpanjangan Waktu
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-1 md:col-span-2 bg-white border border-slate-200/80 rounded-2xl p-16 text-center text-slate-400 font-medium animate-pop-in">
                Belum ada berkas data penugasan DPU Kabupaten Semarang yang terdaftar untuk Anda saat ini.
            </div>
        @endforelse
    </div>
</div>

<script>
    function filterTugas(status) {
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('bg-slate-900', 'text-white', 'shadow-sm');
            btn.classList.add('text-slate-600', 'hover:bg-slate-100');
        });

        const activeBtn = document.getElementById('btn-' + status);
        if (activeBtn) {
            activeBtn.classList.remove('text-slate-600', 'hover:bg-slate-100');
            activeBtn.classList.add('bg-slate-900', 'text-white', 'shadow-sm');
        }

        document.querySelectorAll('.tugas-card').forEach(card => {
            if (status === 'all' || card.getAttribute('data-status') === status) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>

<style>
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes popIn { from { opacity: 0; transform: scale(0.97); } to { opacity: 1; transform: scale(1); } }
    .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
    .animate-slide-up { animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .animate-pop-in { animation: popIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
</style>
@endsection
