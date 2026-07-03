@extends('layout.layoutadmin')

@section('title', 'Manajemen Laporan')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 animate-fade-in">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <h1 class="text-xl md:text-2xl font-black text-slate-900 tracking-tight">Manajemen Laporan</h1>
            <p class="text-xs font-medium text-slate-400 mt-1">Pantau laporan akhir, laporan harian, dan permohonan perpanjangan waktu dari anggota penugasan.</p>
        </div>
        <div class="grid grid-cols-3 gap-2 w-full md:w-auto">
            <button type="button" onclick="filterLaporanAdmin('akhir')" class="filter-laporan-card bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-2xl px-4 py-3 text-center transition-all" data-filter-button="akhir">
                <p class="text-lg font-black text-slate-900">{{ method_exists($laporans, 'total') ? $laporans->total() : $laporans->count() }}</p>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Akhir</p>
            </button>
            <button type="button" onclick="filterLaporanAdmin('harian')" class="filter-laporan-card bg-blue-50 hover:bg-blue-100 border border-blue-100 rounded-2xl px-4 py-3 text-center transition-all" data-filter-button="harian">
                <p class="text-lg font-black text-blue-700">{{ ($dailyReports ?? collect())->count() }}</p>
                <p class="text-[9px] font-black text-blue-500 uppercase tracking-widest">Harian</p>
            </button>
            <button type="button" onclick="filterLaporanAdmin('perpanjangan')" class="filter-laporan-card bg-red-50 hover:bg-red-100 border border-red-100 rounded-2xl px-4 py-3 text-center transition-all" data-filter-button="perpanjangan">
                <p class="text-lg font-black text-red-700">{{ ($extensionRequests ?? collect())->count() }}</p>
                <p class="text-[9px] font-black text-red-500 uppercase tracking-widest">Perpanjangan</p>
            </button>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 space-y-4">
        <form action="{{ route('admin.laporan.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama tugas, kode tugas, atau NIP user..." class="flex-1 px-4 py-3 text-xs font-medium bg-slate-50 border border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all">
            <button type="submit" class="px-5 py-3 bg-slate-900 hover:bg-black text-white text-xs font-black rounded-xl uppercase tracking-wider">Cari Data</button>
        </form>

        <div class="flex flex-wrap gap-2">
            <button type="button" onclick="filterLaporanAdmin('all')" class="filter-laporan-btn px-4 py-2 rounded-xl bg-slate-900 text-white text-[10px] font-black uppercase tracking-wider" data-filter-button="all">Semua</button>
            <button type="button" onclick="filterLaporanAdmin('akhir')" class="filter-laporan-btn px-4 py-2 rounded-xl bg-slate-100 text-slate-600 text-[10px] font-black uppercase tracking-wider" data-filter-button="akhir">Laporan Akhir</button>
            <button type="button" onclick="filterLaporanAdmin('harian')" class="filter-laporan-btn px-4 py-2 rounded-xl bg-slate-100 text-slate-600 text-[10px] font-black uppercase tracking-wider" data-filter-button="harian">Laporan Harian</button>
            <button type="button" onclick="filterLaporanAdmin('perpanjangan')" class="filter-laporan-btn px-4 py-2 rounded-xl bg-slate-100 text-slate-600 text-[10px] font-black uppercase tracking-wider" data-filter-button="perpanjangan">Perpanjangan</button>
        </div>
    </div>

    <section data-report-section="perpanjangan" class="laporan-section {{ ($extensionRequests ?? collect())->count() > 0 ? '' : 'hidden' }} bg-red-50 border border-red-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-red-100 flex items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-black text-red-700 uppercase tracking-widest">Permohonan Perpanjangan Waktu</h2>
                <p class="text-xs font-semibold text-red-500 mt-1">Permohonan ini membutuhkan keputusan admin.</p>
            </div>
            <span class="px-3 py-1 rounded-xl bg-white border border-red-100 text-[10px] font-black text-red-700 uppercase tracking-wider">{{ ($extensionRequests ?? collect())->count() }} Menunggu</span>
        </div>
        <div class="divide-y divide-red-100">
            @forelse(($extensionRequests ?? collect()) as $requestItem)
                <div class="p-5 bg-white/60 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <p class="text-sm font-black text-slate-900">{{ $requestItem->penugasan->tugas->nama_tugas ?? 'Penugasan' }}</p>
                        <p class="text-[10px] font-mono text-slate-400 uppercase">KODE: {{ $requestItem->penugasan->kodetugas ?? '-' }} • USER: {{ $requestItem->user->name ?? $requestItem->id_user }} / {{ $requestItem->id_user }}</p>
                        <p class="text-xs font-semibold text-slate-700 leading-relaxed max-w-3xl mt-2">{{ $requestItem->alasan_keterlambatan ?: 'Tidak ada alasan tertulis.' }}</p>
                    </div>
                    <a href="{{ route('admin.penugasan.show', $requestItem->id_penugasan) }}" class="shrink-0 inline-flex justify-center px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-[10px] font-black uppercase tracking-wider">Tinjau & Set Deadline</a>
                </div>
            @empty
                <div class="p-10 text-center text-red-400 text-xs font-semibold italic bg-white/60">Tidak ada permohonan perpanjangan waktu.</div>
            @endforelse
        </div>
    </section>

    <section data-report-section="harian" class="laporan-section bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-black text-slate-800 uppercase tracking-widest">Daftar Laporan Harian</h2>
                <p class="text-xs font-medium text-slate-400 mt-1">Menampilkan laporan harian terbaru dari seluruh penugasan.</p>
            </div>
            <span class="px-3 py-1 rounded-xl bg-blue-50 border border-blue-100 text-[10px] font-black text-blue-700 uppercase tracking-wider">{{ ($dailyReports ?? collect())->count() }} Data</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="py-3 px-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-wider">Tanggal</th>
                        <th class="py-3 px-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-wider">Tugas</th>
                        <th class="py-3 px-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-wider">User</th>
                        <th class="py-3 px-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-wider">Progres</th>
                        <th class="py-3 px-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-wider">Lampiran</th>
                        <th class="py-3 px-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    @forelse(($dailyReports ?? collect()) as $daily)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-4 px-4 text-xs font-black text-slate-700 whitespace-nowrap">{{ \Carbon\Carbon::parse($daily->tanggal_laporan)->locale('id')->translatedFormat('d M Y') }}</td>
                            <td class="py-4 px-4"><span class="text-xs font-bold text-slate-800 block">{{ $daily->penugasan->tugas->nama_tugas ?? '-' }}</span><span class="text-[10px] font-mono text-slate-400 block mt-0.5">KODE: {{ $daily->penugasan->kodetugas ?? '-' }}</span></td>
                            <td class="py-4 px-4 text-xs font-semibold text-slate-600">{{ $daily->user->name ?? $daily->id_user }}<span class="block text-[10px] font-mono text-slate-400">{{ $daily->id_user }}</span></td>
                            <td class="py-4 px-4 text-xs text-slate-600 max-w-sm">{{ \Illuminate\Support\Str::limit($daily->progres, 90) }}</td>
                            <td class="py-4 px-4 text-center">@if($daily->file_path)<a href="{{ asset('storage/' . $daily->file_path) }}" target="_blank" class="inline-flex px-3 py-1.5 bg-blue-50 border border-blue-100 text-blue-700 rounded-lg text-[10px] font-black uppercase tracking-wider">Buka</a>@else<span class="text-[10px] font-bold text-slate-300 uppercase">Tidak Ada</span>@endif</td>
                            <td class="py-4 px-4 text-center"><a href="{{ route('admin.penugasan.show', $daily->id_penugasan) }}" class="inline-flex px-3 py-1.5 bg-slate-900 hover:bg-black text-white rounded-lg text-[10px] font-black uppercase tracking-wider">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-10 text-center text-slate-400 text-xs font-medium italic">Belum ada laporan harian yang masuk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section data-report-section="akhir" class="laporan-section bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-sm font-black text-slate-800 uppercase tracking-widest">Daftar Laporan Akhir</h2>
            <p class="text-xs font-medium text-slate-400 mt-1">Laporan akhir yang sudah dikirim user untuk review admin.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="py-3 px-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-wider">ID</th>
                        <th class="py-3 px-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-wider">Nama Tugas</th>
                        <th class="py-3 px-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-wider">NIP Pengirim</th>
                        <th class="py-3 px-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-wider">Ringkasan</th>
                        <th class="py-3 px-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-wider">Perpanjangan</th>
                        <th class="py-3 px-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    @forelse($laporans as $l)
                        @php $hasExtension = ($l->penugasan->anggota ?? collect())->where('status_keterlambatan', 'mengajukan')->count() > 0; @endphp
                        <tr class="hover:bg-slate-50 transition-colors {{ $hasExtension ? 'bg-red-50/40' : '' }}">
                            <td class="py-4 px-4 text-xs font-semibold text-slate-500">#LPR-{{ $l->id }}</td>
                            <td class="py-4 px-4"><span class="text-xs font-bold text-slate-800 block">{{ $l->penugasan->tugas->nama_tugas ?? '-' }}</span><span class="text-[10px] font-mono text-slate-400 block mt-0.5">KODE: {{ $l->penugasan->kodetugas ?? '-' }}</span></td>
                            <td class="py-4 px-4 text-xs font-medium text-slate-600">{{ $l->user_id }}</td>
                            <td class="py-4 px-4 text-xs text-slate-500 max-w-sm">{{ \Illuminate\Support\Str::limit($l->teks_laporan ?? '-', 80) }}</td>
                            <td class="py-4 px-4 text-center">@if($l->status === 'disetujui')<span class="px-2.5 py-1 text-[10px] font-black text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-md uppercase tracking-wider">DISETUJUI</span>@elseif($l->status === 'revisi')<span class="px-2.5 py-1 text-[10px] font-black text-amber-700 bg-amber-50 border border-amber-100 rounded-md uppercase tracking-wider">REVISI</span>@else<span class="px-2.5 py-1 text-[10px] font-black text-blue-700 bg-blue-50 border border-blue-100 rounded-md uppercase tracking-wider">{{ strtoupper($l->status) }}</span>@endif</td>
                            <td class="py-4 px-4 text-center">@if($hasExtension)<span class="px-2.5 py-1 text-[10px] font-black text-red-700 bg-red-50 border border-red-100 rounded-md uppercase tracking-wider">Ada</span>@else<span class="text-[10px] font-bold text-slate-300 uppercase">-</span>@endif</td>
                            <td class="py-4 px-4 text-center"><a href="{{ route('admin.laporan.show', $l->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black rounded-lg shadow-sm transition uppercase tracking-wider">Review</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-12 text-center text-slate-400 font-medium italic text-xs">Belum ada laporan akhir yang masuk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($laporans, 'links'))
            <div class="bg-slate-50 px-4 py-3 border-t border-slate-100">{{ $laporans->links() }}</div>
        @endif
    </section>
</div>

<script>
    function filterLaporanAdmin(type) {
        document.querySelectorAll('.laporan-section').forEach(function (section) {
            section.classList.toggle('hidden', type !== 'all' && section.dataset.reportSection !== type);
        });

        document.querySelectorAll('.filter-laporan-btn').forEach(function (button) {
            const active = button.dataset.filterButton === type;
            button.classList.toggle('bg-slate-900', active);
            button.classList.toggle('text-white', active);
            button.classList.toggle('bg-slate-100', !active);
            button.classList.toggle('text-slate-600', !active);
        });
    }
</script>

<style>
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
</style>
@endsection
