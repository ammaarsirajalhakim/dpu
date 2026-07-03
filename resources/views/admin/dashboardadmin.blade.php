@extends('layout.layoutadmin')

@section('title', 'Dashboard Admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@php
    $statusItems = collect($statusPenugasan ?? [
        'Selesai' => 0,
        'Proses Review' => 0,
        'Terlambat' => 0,
        'Ditugaskan' => 0,
        'Belum Ditugaskan' => 0,
    ]);
    $statusTotal = $statusPenugasanTotal ?? $statusItems->sum();
@endphp

<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    .cursor-grab { cursor: grab; }
    .active\:cursor-grabbing:active { cursor: grabbing !important; }

    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    .hud-panel { opacity: 0; animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .delay-100 { animation-delay: 100ms; } .delay-200 { animation-delay: 200ms; } .delay-300 { animation-delay: 300ms; }
    .delay-400 { animation-delay: 400ms; } .delay-500 { animation-delay: 500ms; } .delay-600 { animation-delay: 600ms; }

    .date-btn { transition: all 0.3s ease; }
    .date-btn.active { background-color: #2563EB !important; border-color: #1D4ED8 !important; transform: scale(1.05) !important; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important; }
    .date-btn.active .day-text { color: #BFDBFE !important; }
    .date-btn.active .date-text { color: #ffffff !important; }
</style>

<div class="container mx-auto px-4 py-6 font-sans">
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-end border-b border-gray-300 pb-4 hud-panel">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                <svg class="w-7 h-7 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                Dashboard Admin
            </h2>
            <p class="text-sm text-gray-500 mt-1">Sistem diinisialisasi. Menampilkan data real-time.</p>
        </div>
        <div class="text-right mt-4 md:mt-0 bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-100 flex items-center">
            <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse mr-2"></div>
            <p id="liveTime" class="font-semibold text-gray-800 text-sm"></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div class="flex flex-col space-y-6 lg:col-span-1">
            <div class="hud-panel delay-100 bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col h-full hover:shadow-md transition-shadow">
                <div class="bg-black px-4 py-3 flex items-center rounded-t-xl"><svg class="w-4 h-4 text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg><h3 class="text-white text-sm font-semibold">Statistik Utama</h3></div>
                <div class="p-4 grid grid-cols-2 gap-4 flex-grow">
                    <div class="bg-gray-50 rounded-lg p-3 text-center border border-gray-100 hover:bg-gray-100 transition"><span class="block text-2xl font-bold text-gray-800">{{ $totalUser ?? 0 }}</span><span class="text-[10px] text-gray-500 font-semibold uppercase">Total User</span></div>
                    <div class="bg-blue-50 rounded-lg p-3 text-center border border-blue-100 hover:bg-blue-100 transition"><span class="block text-2xl font-bold text-blue-700">{{ $tugasAktif ?? 0 }}</span><span class="text-[10px] text-blue-600 font-semibold uppercase">Tugas Aktif</span></div>
                    <div class="bg-blue-600 rounded-lg p-3 text-center shadow-md relative overflow-hidden"><span class="block text-2xl font-bold text-white relative z-10">{{ $menungguReview ?? 0 }}</span><span class="text-[10px] text-blue-100 font-semibold uppercase relative z-10">Cek Laporan</span></div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center border border-gray-100 hover:bg-green-50 transition"><span class="block text-2xl font-bold text-gray-800">{{ $selesaiBulanIni ?? 0 }}</span><span class="text-[10px] text-gray-500 font-semibold uppercase">Selesai (Bln)</span></div>
                </div>
                <div class="bg-gray-50 px-4 py-2 border-t border-gray-100 rounded-b-xl"><button onclick="openModal('modalStats')" class="flex items-center text-xs font-semibold text-gray-500 hover:text-blue-600 transition"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l5-5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg> Expand</button></div>
            </div>

            <div class="hud-panel delay-200 bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col h-full hover:shadow-md transition-shadow">
                <div class="bg-black px-4 py-3 flex items-center rounded-t-xl"><svg class="w-4 h-4 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path></svg><h3 class="text-white text-sm font-semibold">Status Penugasan</h3></div>
                <div class="p-4 flex flex-col items-center justify-center flex-grow">
                    <div class="relative w-full max-w-[180px] aspect-square"><canvas id="statusChart"></canvas></div>
                    <div class="grid grid-cols-1 gap-1 mt-3 text-[10px] font-bold text-gray-600 w-full">
                        <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-gray-800 mr-2"></span>Selesai: {{ $totalSelesai ?? 0 }}</div>
                        <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-yellow-400 mr-2"></span>Proses Review: {{ $totalProses ?? 0 }}</div>
                        <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span>Terlambat: {{ $totalTerlambat ?? 0 }}</div>
                        <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-blue-600 mr-2"></span>Ditugaskan: {{ $totalDitugaskan ?? 0 }}</div>
                        <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-gray-400 mr-2"></span>Belum Ditugaskan: {{ $belumDitugaskan ?? 0 }}</div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-2 border-t border-gray-100 rounded-b-xl"><button onclick="openModal('modalStatus')" class="flex items-center text-xs font-semibold text-gray-500 hover:text-blue-600 transition"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l5-5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg> Expand</button></div>
            </div>
        </div>

        <div class="flex flex-col lg:col-span-2 hud-panel delay-300">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 h-full flex flex-col hover:shadow-md transition-shadow">
                <div class="bg-black px-4 py-3 flex justify-between items-center rounded-t-xl">
                    <div class="flex items-center"><svg class="w-4 h-4 text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg><h3 class="text-white text-sm font-semibold">Jadwal & Aktivitas</h3></div>
                    <div class="flex items-center space-x-2">
                        <button onclick="changeMonth(-1)" class="p-1 text-gray-400 hover:text-white transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>
                        <span id="currentMonthYearLabel" class="text-white text-xs font-bold font-mono min-w-[100px] text-center">Bulan</span>
                        <button onclick="changeMonth(1)" class="p-1 text-gray-400 hover:text-white transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>
                        <button onclick="goToToday()" class="ml-2 px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-bold rounded shadow transition">HARI INI</button>
                    </div>
                </div>
                <div class="p-5 flex-grow flex flex-col">
                    <div class="flex items-center mb-4 relative">
                        <button onclick="scrollDates('dateScrollContainer', -200)" class="p-2 rounded bg-gray-50 hover:bg-gray-200 text-gray-600 transition absolute left-0 z-10 shadow-sm border border-gray-100"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>
                        <div id="dateScrollContainer" class="drag-scroll flex overflow-x-auto scrollbar-hide space-x-2 px-12 flex-grow cursor-grab select-none py-2"></div>
                        <button onclick="scrollDates('dateScrollContainer', 200)" class="p-2 rounded bg-gray-50 hover:bg-gray-200 text-gray-600 transition absolute right-0 z-10 shadow-sm border border-gray-100"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>
                    </div>
                    <div id="taskListContainer" class="overflow-y-auto flex-grow pr-2 space-y-3 relative min-h-[250px]"></div>
                </div>
                <div class="bg-gray-50 px-4 py-3 border-t border-gray-100 flex justify-between items-center rounded-b-xl">
                    <button onclick="openModal('modalTimeline')" class="flex items-center text-xs font-semibold text-gray-500 hover:text-blue-600 transition"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l5-5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg> Expand (Bulan Penuh)</button>
                    <div class="flex flex-wrap space-x-4 text-[10px] font-medium text-gray-600">
                        <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-blue-600 mr-1"></span> Ditugaskan</div>
                        <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-yellow-400 mr-1"></span> Dekat Batas</div>
                        <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-red-500 mr-1"></span> Terlambat</div>
                        <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-gray-800 mr-1"></span> Selesai</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col space-y-6 lg:col-span-1">
            <div class="hud-panel delay-400 bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col hover:shadow-md transition-shadow">
                <div class="bg-black px-4 py-3 flex items-center rounded-t-xl"><svg class="w-4 h-4 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg><h3 class="text-white text-sm font-semibold">Tren Kinerja</h3></div>
                <div class="p-3 h-36 flex items-center justify-center"><canvas id="trendChart"></canvas></div>
                <div class="bg-gray-50 px-4 py-2 border-t border-gray-100 rounded-b-xl"><button onclick="openModal('modalTrend')" class="flex items-center text-xs font-semibold text-gray-500 hover:text-blue-600 transition"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l5-5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg> Expand</button></div>
            </div>

            <div class="hud-panel delay-500 bg-white rounded-xl shadow-sm border border-red-200 flex flex-col flex-grow hover:shadow-md transition-shadow">
                <div class="bg-red-50 px-4 py-3 flex items-center border-b border-red-100 rounded-t-xl"><svg class="w-4 h-4 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg><h3 class="text-red-700 text-sm font-semibold">Perlu Perhatian</h3></div>
                <div class="p-0 divide-y divide-gray-100 flex-grow">
                    @forelse($urgentTugas ?? [] as $penugasan)
                        @php
                            $deadline = \Carbon\Carbon::parse($penugasan->batas_waktu_lapor);
                            $isLate = now()->greaterThan($deadline);
                        @endphp
                        <a href="{{ route('admin.penugasan.show', $penugasan->id) }}" class="block p-3 {{ $isLate ? 'hover:bg-red-50' : 'hover:bg-yellow-50' }} transition cursor-pointer">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $penugasan->tugas->nama_tugas ?? 'Data Dihapus' }}</p>
                            <div class="flex justify-between items-center mt-1"><p class="text-[10px] font-bold {{ $isLate ? 'text-red-600' : 'text-yellow-600' }}">{{ $isLate ? 'TERLAMBAT' : 'MENDEKATI BATAS' }} • {{ $deadline->locale('id')->translatedFormat('d M Y') }}</p></div>
                        </a>
                    @empty
                        <div class="p-4 text-center text-xs text-gray-500 italic">Semua tugas aman.</div>
                    @endforelse
                </div>
                <div class="bg-gray-50 px-4 py-2 border-t border-gray-100 rounded-b-xl"><button onclick="openModal('modalUrgent')" class="flex items-center text-xs font-semibold text-gray-500 hover:text-blue-600 transition"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l5-5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg> Expand</button></div>
            </div>

            <div class="hud-panel delay-600 bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col hover:shadow-md transition-shadow">
                <div class="bg-black px-4 py-3 flex items-center rounded-t-xl"><svg class="w-4 h-4 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293h3.172a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293H20"></path></svg><h3 class="text-white text-sm font-semibold">Laporan Baru</h3></div>
                <div class="p-0 divide-y divide-gray-100">
                    @forelse($laporanBaru ?? [] as $laporan)
                        <a href="{{ route('admin.laporan.show', $laporan->id) }}" class="block p-3 hover:bg-gray-50 transition cursor-pointer group">
                            <p class="text-sm font-semibold text-blue-600 group-hover:text-blue-800 truncate">{{ $laporan->penugasan->tugas->nama_tugas ?? 'Laporan Masuk' }}</p>
                            <p class="text-[10px] text-gray-500 mt-1">#LPR-{{ $laporan->id }} • {{ $laporan->created_at->format('H:i') }} WIB</p>
                        </a>
                    @empty
                        <div class="p-4 text-center text-xs text-gray-500 italic">Belum ada laporan terbaru.</div>
                    @endforelse
                </div>
                <div class="bg-gray-50 px-4 py-2 border-t border-gray-100 rounded-b-xl"><button onclick="openModal('modalInbox')" class="flex items-center text-xs font-semibold text-gray-500 hover:text-blue-600 transition"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l5-5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg> Expand</button></div>
            </div>
        </div>
    </div>
</div>

<div id="modalStats" class="fixed inset-0 bg-black/80 z-[60] hidden flex items-center justify-center transition-opacity p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col">
        <div class="bg-black px-6 py-4 flex justify-between items-center"><h3 class="text-white font-bold">Detail Statistik</h3><button onclick="closeModal('modalStats')" class="text-gray-400 hover:text-white text-2xl">&times;</button></div>
        <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
            <div class="bg-gray-50 border border-gray-200 p-4 rounded-xl"><p class="text-4xl font-black text-gray-800">{{ $totalUser ?? 0 }}</p><p class="text-xs font-bold text-gray-500 mt-2">TOTAL USER</p></div>
            <div class="bg-blue-50 border border-blue-200 p-4 rounded-xl"><p class="text-4xl font-black text-blue-700">{{ $tugasAktif ?? 0 }}</p><p class="text-xs font-bold text-blue-600 mt-2">TUGAS AKTIF</p></div>
            <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-xl"><p class="text-4xl font-black text-yellow-700">{{ $menungguReview ?? 0 }}</p><p class="text-xs font-bold text-yellow-600 mt-2">CEK LAPORAN</p></div>
            <div class="bg-green-50 border border-green-200 p-4 rounded-xl"><p class="text-4xl font-black text-green-700">{{ $selesaiBulanIni ?? 0 }}</p><p class="text-xs font-bold text-green-600 mt-2">SELESAI BULAN INI</p></div>
        </div>
    </div>
</div>

<div id="modalStatus" class="fixed inset-0 bg-black/80 z-[60] hidden flex items-center justify-center transition-opacity p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col">
        <div class="bg-black px-6 py-4 flex justify-between items-center"><h3 class="text-white font-bold">Status Penugasan</h3><button onclick="closeModal('modalStatus')" class="text-gray-400 hover:text-white text-2xl">&times;</button></div>
        <div class="p-6 h-96 flex items-center justify-center"><canvas id="statusChartModal"></canvas></div>
        <div class="px-6 pb-6 text-xs font-bold text-gray-500">Total status: {{ $statusTotal }} / Total tugas: {{ $totalTugas ?? 0 }}</div>
    </div>
</div>

<div id="modalTimeline" class="fixed inset-0 bg-black/80 z-[60] hidden flex items-center justify-center transition-opacity p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl overflow-hidden flex flex-col h-[85vh]">
        <div class="bg-black px-6 py-4 flex justify-between items-center">
            <h3 class="text-white font-bold">Kalender Lengkap</h3>
            <div class="flex items-center space-x-2"><button onclick="changeMonthModal(-1)" class="p-1 text-gray-400 hover:text-white">‹</button><span id="modalMonthYearLabel" class="text-white text-sm font-bold font-mono min-w-[120px] text-center">Bulan</span><button onclick="changeMonthModal(1)" class="p-1 text-gray-400 hover:text-white">›</button><button onclick="goToTodayModal()" class="ml-2 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded shadow">HARI INI</button><button onclick="closeModal('modalTimeline')" class="text-gray-400 hover:text-white ml-4 text-2xl">&times;</button></div>
        </div>
        <div class="p-6 flex flex-col flex-grow overflow-hidden bg-gray-50/30">
            <div class="flex items-center mb-6 relative"><button onclick="scrollDates('modalDateScrollContainer', -300)" class="p-3 rounded-lg bg-gray-100 hover:bg-gray-200 absolute left-0 z-10 shadow-sm border border-gray-200">‹</button><div id="modalDateScrollContainer" class="drag-scroll flex overflow-x-auto scrollbar-hide space-x-3 px-16 flex-grow cursor-grab select-none py-2"></div><button onclick="scrollDates('modalDateScrollContainer', 300)" class="p-3 rounded-lg bg-gray-100 hover:bg-gray-200 absolute right-0 z-10 shadow-sm border border-gray-200">›</button></div>
            <div id="modalTaskListContainer" class="overflow-y-auto flex-grow pr-2 space-y-4 relative min-h-[300px]"></div>
        </div>
    </div>
</div>

<div id="modalTrend" class="fixed inset-0 bg-black/80 z-[60] hidden flex items-center justify-center transition-opacity p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden flex flex-col">
        <div class="bg-black px-6 py-4 flex justify-between items-center"><h3 class="text-white font-bold">Analitik Tren Kinerja</h3><button onclick="closeModal('modalTrend')" class="text-gray-400 hover:text-white text-2xl">&times;</button></div>
        <div class="p-6 h-[400px] flex items-center justify-center"><canvas id="trendChartModal"></canvas></div>
    </div>
</div>

<div id="modalUrgent" class="fixed inset-0 bg-black/80 z-[60] hidden flex items-center justify-center transition-opacity p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[85vh]">
        <div class="bg-red-600 px-6 py-4 flex justify-between items-center"><h3 class="text-white font-bold">Daftar Perlu Perhatian</h3><button onclick="closeModal('modalUrgent')" class="text-red-100 hover:text-white text-2xl">&times;</button></div>
        <div class="divide-y divide-gray-100 overflow-y-auto">
            @forelse($urgentTugas ?? [] as $penugasan)
                @php $deadline = \Carbon\Carbon::parse($penugasan->batas_waktu_lapor); $isLate = now()->greaterThan($deadline); @endphp
                <a href="{{ route('admin.penugasan.show', $penugasan->id) }}" class="block p-5 hover:bg-gray-50"><p class="font-bold text-gray-900">{{ $penugasan->tugas->nama_tugas ?? '-' }}</p><p class="text-xs {{ $isLate ? 'text-red-600' : 'text-yellow-600' }} font-bold mt-1">{{ $isLate ? 'TERLAMBAT' : 'MENDEKATI BATAS' }} • {{ $deadline->locale('id')->translatedFormat('d F Y, H:i') }}</p></a>
            @empty
                <div class="p-10 text-center text-sm text-gray-400 italic">Tidak ada tugas mendesak.</div>
            @endforelse
        </div>
    </div>
</div>

<div id="modalInbox" class="fixed inset-0 bg-black/80 z-[60] hidden flex items-center justify-center transition-opacity p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[85vh]">
        <div class="bg-black px-6 py-4 flex justify-between items-center"><h3 class="text-white font-bold">Laporan Baru</h3><button onclick="closeModal('modalInbox')" class="text-gray-400 hover:text-white text-2xl">&times;</button></div>
        <div class="divide-y divide-gray-100 overflow-y-auto">
            @forelse($laporanBaru ?? [] as $laporan)
                <a href="{{ route('admin.laporan.show', $laporan->id) }}" class="block p-5 hover:bg-gray-50"><p class="font-bold text-blue-700">{{ $laporan->penugasan->tugas->nama_tugas ?? 'Laporan Masuk' }}</p><p class="text-xs text-gray-500 mt-1">#LPR-{{ $laporan->id }} • {{ $laporan->created_at->locale('id')->translatedFormat('d F Y, H:i') }}</p></a>
            @empty
                <div class="p-10 text-center text-sm text-gray-400 italic">Belum ada laporan baru.</div>
            @endforelse
        </div>
    </div>
</div>

<script>
    const tasksData = @json(($timelineTugas ?? collect())->values());
    const dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    let currentDate = new Date();
    let currentModalDate = new Date();
    let selectedFilterDate = 'all';
    let selectedModalFilterDate = 'all';

    function updateLiveTime() {
        const now = new Date();
        const el = document.getElementById('liveTime');
        if (el) el.innerText = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) + ' • ' + now.toLocaleTimeString('id-ID');
    }
    setInterval(updateLiveTime, 1000); updateLiveTime();

    function parseDateOnly(value) {
        const date = new Date(value);
        date.setHours(0,0,0,0);
        return date;
    }

    function getTaskStatus(task, targetDateStr) {
        const start = parseDateOnly(task.tugas?.tanggal_mulai || task.created_at || task.batas_waktu_lapor);
        const deadline = parseDateOnly(task.batas_waktu_lapor);
        const today = new Date(); today.setHours(0,0,0,0);
        const target = targetDateStr === 'all' ? today : parseDateOnly(targetDateStr);
        const laporanStatus = task.laporan?.status || 'belum_lapor';
        const isCompleted = laporanStatus === 'disetujui';
        const isProcess = laporanStatus === 'diajukan' || laporanStatus === 'revisi';

        if (targetDateStr !== 'all' && (target < start || target > deadline)) return null;
        if (isCompleted) return { bgLine: 'bg-gray-800', bgCard: 'bg-gray-50', text: 'SELESAI', labelBg: 'bg-gray-200', labelText: 'text-gray-800' };
        if (isProcess) return { bgLine: 'bg-yellow-400', bgCard: 'bg-yellow-50', text: 'PROSES REVIEW', labelBg: 'bg-yellow-200', labelText: 'text-yellow-800' };
        if (deadline < today) return { bgLine: 'bg-red-500', bgCard: 'bg-red-50', text: 'TERLAMBAT', labelBg: 'bg-red-200', labelText: 'text-red-800' };

        const diffDays = Math.ceil((deadline - target) / (1000 * 60 * 60 * 24));
        if (diffDays >= 0 && diffDays <= 2) return { bgLine: 'bg-yellow-400', bgCard: 'bg-yellow-50', text: 'MENDEKATI BATAS', labelBg: 'bg-yellow-200', labelText: 'text-yellow-800' };
        if (target >= start && target <= deadline) return { bgLine: 'bg-blue-600', bgCard: 'bg-blue-50', text: 'DITUGASKAN', labelBg: 'bg-blue-200', labelText: 'text-blue-800' };
        return null;
    }

    function buildCalendarHtml(dateObj, filterDate, isModal) {
        let html = '';
        const daysInMonth = new Date(dateObj.getFullYear(), dateObj.getMonth() + 1, 0).getDate();
        const todayStr = new Date().toISOString().split('T')[0];
        for (let i = 1; i <= daysInMonth; i++) {
            let iterDate = new Date(dateObj.getFullYear(), dateObj.getMonth(), i, 12);
            let iterStr = iterDate.toISOString().split('T')[0];
            let isWeekend = (iterDate.getDay() === 0 || iterDate.getDay() === 6);
            let isToday = (iterStr === todayStr);
            let isActive = (iterStr === filterDate);
            let baseClass = isModal ? 'min-w-[80px] p-3 rounded-xl' : 'min-w-[70px] p-2 rounded-lg';
            let btnClass = `date-btn ${baseClass} text-center cursor-pointer transition shadow-sm border flex-shrink-0 `;
            if (isActive) btnClass += 'active border-transparent';
            else if (isToday) btnClass += 'bg-blue-100 border-blue-300 hover:bg-blue-200';
            else btnClass += 'bg-white border-transparent hover:bg-gray-100';
            let dColor = isActive ? '' : (isWeekend ? 'text-red-400' : 'text-gray-500');
            let tColor = isActive ? '' : (isWeekend ? 'text-red-600' : (isModal ? 'text-gray-800' : 'text-gray-700'));
            let numSize = isModal ? 'text-lg mt-1' : 'text-sm';
            html += `<div onclick="${isModal ? 'selectDateModal' : 'selectDate'}('${iterStr}')" class="${btnClass}"><span class="block ${isModal?'text-xs':'text-[10px]'} font-semibold day-text ${dColor}">${dayNames[iterDate.getDay()]}</span><span class="block ${numSize} font-bold date-text ${tColor}">${i}</span></div>`;
        }
        return html;
    }

    function renderTasks(containerId, isModal, filterDate) {
        const container = document.getElementById(containerId);
        if (!container) return;
        container.innerHTML = ''; let count = 0;
        tasksData.forEach(task => {
            const statusCfg = getTaskStatus(task, filterDate);
            if (!statusCfg) return;
            count++;
            const tName = task.tugas ? task.tugas.nama_tugas : 'Tugas Tidak Diketahui';
            const dStr = new Date(task.batas_waktu_lapor).toLocaleDateString('id-ID', {day: 'numeric', month:'short', year:'numeric'});
            const url = `{{ url('admin/penugasan') }}/${task.id}`;
            if (isModal) {
                container.innerHTML += `<a href="${url}" class="relative ${statusCfg.bgCard} border border-gray-200 rounded-xl flex items-center hover:shadow-md transition p-4 z-10 block"><div class="w-2 h-12 ${statusCfg.bgLine} rounded-full mr-4"></div><div class="flex-grow"><p class="text-base font-bold text-gray-900">${tName}</p><p class="text-xs text-gray-500 mt-1">Batas: ${dStr}</p></div><span class="${statusCfg.labelBg} ${statusCfg.labelText} text-xs font-bold px-4 py-2 rounded-lg ml-4">${statusCfg.text}</span></a>`;
            } else {
                container.innerHTML += `<a href="${url}" class="relative h-12 ${statusCfg.bgCard} border border-gray-100 rounded-lg flex items-center transition p-2 z-10 block hover:shadow-sm"><div class="w-1.5 h-full ${statusCfg.bgLine} rounded-full mr-3"></div><div class="flex-grow"><p class="text-sm font-bold text-gray-800 truncate">${tName}</p><p class="text-[10px] text-gray-500">Tenggat: ${dStr}</p></div><span class="${statusCfg.labelBg} ${statusCfg.labelText} text-[10px] font-bold px-2 py-1 rounded">${statusCfg.text}</span></a>`;
            }
        });
        if(count === 0) container.innerHTML = `<div class="flex flex-col items-center justify-center h-full text-gray-400 py-10"><span class="italic text-sm">Tidak ada jadwal tugas di tanggal yang dipilih.</span></div>`;
    }

    function renderCalendar() {
        document.getElementById('currentMonthYearLabel').innerText = monthNames[currentDate.getMonth()] + ' ' + currentDate.getFullYear();
        document.getElementById('dateScrollContainer').innerHTML = buildCalendarHtml(currentDate, selectedFilterDate, false);
        renderTasks('taskListContainer', false, selectedFilterDate);
    }
    function renderModalCalendar() {
        document.getElementById('modalMonthYearLabel').innerText = monthNames[currentModalDate.getMonth()] + ' ' + currentModalDate.getFullYear();
        document.getElementById('modalDateScrollContainer').innerHTML = buildCalendarHtml(currentModalDate, selectedModalFilterDate, true);
        renderTasks('modalTaskListContainer', true, selectedModalFilterDate);
    }
    function selectDate(dStr) { selectedFilterDate = (selectedFilterDate === dStr) ? 'all' : dStr; renderCalendar(); }
    function selectDateModal(dStr) { selectedModalFilterDate = (selectedModalFilterDate === dStr) ? 'all' : dStr; renderModalCalendar(); }
    function changeMonth(offset) { currentDate.setMonth(currentDate.getMonth() + offset); renderCalendar(); }
    function changeMonthModal(offset) { currentModalDate.setMonth(currentModalDate.getMonth() + offset); renderModalCalendar(); }
    function goToToday() { currentDate = new Date(); selectedFilterDate = new Date().toISOString().split('T')[0]; renderCalendar(); setTimeout(() => document.getElementById('dateScrollContainer').scrollLeft = (currentDate.getDate() - 3) * 78, 50); }
    function goToTodayModal() { currentModalDate = new Date(); selectedModalFilterDate = new Date().toISOString().split('T')[0]; renderModalCalendar(); setTimeout(() => document.getElementById('modalDateScrollContainer').scrollLeft = (currentModalDate.getDate() - 3) * 88, 50); }
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
    window.addEventListener('click', e => { ['modalStats', 'modalStatus', 'modalTimeline', 'modalTrend', 'modalUrgent', 'modalInbox'].forEach(id => { if (e.target === document.getElementById(id)) closeModal(id); }); });
    function scrollDates(id, offset) { document.getElementById(id).scrollBy({ left: offset, behavior: 'smooth' }); }

    document.querySelectorAll('.drag-scroll').forEach(slider => {
        let isDown = false, startX, scrollLeft;
        slider.addEventListener('mousedown', e => { isDown = true; slider.classList.add('active:cursor-grabbing'); startX = e.pageX - slider.offsetLeft; scrollLeft = slider.scrollLeft; });
        slider.addEventListener('mouseleave', () => { isDown = false; slider.classList.remove('active:cursor-grabbing'); });
        slider.addEventListener('mouseup', () => { isDown = false; slider.classList.remove('active:cursor-grabbing'); });
        slider.addEventListener('mousemove', e => { if (!isDown) return; e.preventDefault(); slider.scrollLeft = scrollLeft - (e.pageX - slider.offsetLeft - startX) * 2; });
    });

    const cDark = '#111827'; const cBlue = '#2563EB'; const cYellow = '#FACC15'; const cRed = '#EF4444'; const cGray = '#9CA3AF';
    const donutData = {
        labels: ['Selesai', 'Proses Review', 'Terlambat', 'Ditugaskan', 'Belum Ditugaskan'],
        datasets: [{
            data: [{{ $totalSelesai ?? 0 }}, {{ $totalProses ?? 0 }}, {{ $totalTerlambat ?? 0 }}, {{ $totalDitugaskan ?? 0 }}, {{ $belumDitugaskan ?? 0 }}],
            backgroundColor: [cDark, cYellow, cRed, cBlue, cGray],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    };
    new Chart(document.getElementById('statusChart').getContext('2d'), { type: 'doughnut', data: donutData, options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { display: false } } } });
    new Chart(document.getElementById('statusChartModal').getContext('2d'), { type: 'doughnut', data: donutData, options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { display: true, position: 'bottom' } } } });

    const trendData = {
        labels: {!! json_encode($trendLabels ?? []) !!},
        datasets: [
            { label: 'Tugas Masuk', data: {!! json_encode($trendMasuk ?? []) !!}, backgroundColor: cDark, borderRadius: 4, barPercentage: 0.6 },
            { label: 'Tugas Selesai', data: {!! json_encode($trendSelesai ?? []) !!}, backgroundColor: cBlue, borderRadius: 4, barPercentage: 0.6 }
        ]
    };
    const trendOpt = { responsive: true, maintainAspectRatio: false, scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { precision: 0 }, border: { display: false }, grid: { color: '#F3F4F6' } } }, plugins: { legend: { display: false } } };
    new Chart(document.getElementById('trendChart').getContext('2d'), { type: 'bar', data: trendData, options: trendOpt });
    new Chart(document.getElementById('trendChartModal').getContext('2d'), { type: 'bar', data: trendData, options: { ...trendOpt, plugins: { legend: { display: true, position: 'top' } } } });

    goToToday();
    goToTodayModal();
</script>
@endsection
