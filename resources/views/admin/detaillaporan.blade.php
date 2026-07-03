@extends('layout.layoutadmin')

@section('title', 'Detail Laporan Akhir')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 animate-fade-in">
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl text-sm font-semibold text-emerald-700">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="p-4 bg-red-50 border border-red-100 rounded-2xl text-sm font-semibold text-red-700">{{ $errors->first() }}</div>
    @endif

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 border border-indigo-100 px-2.5 py-0.5 rounded-md uppercase tracking-wider font-mono">LAPORAN AKHIR #{{ $laporan->id }}</span>
            <h1 class="text-xl md:text-2xl font-black text-slate-900 tracking-tight mt-2 leading-tight">{{ $laporan->penugasan->tugas->nama_tugas ?? 'Laporan Akhir Penugasan' }}</h1>
            <p class="text-xs font-medium text-slate-400 mt-1">Dikirim pada {{ \Carbon\Carbon::parse($laporan->created_at)->locale('id')->translatedFormat('d F Y, H:i') }} WIB</p>
        </div>

        <div class="shrink-0 flex flex-col gap-2 items-start md:items-end">
            <span class="px-4 py-2 text-xs font-black rounded-xl uppercase tracking-wider border {{ $laporan->status === 'disetujui' ? 'text-emerald-700 bg-emerald-50 border-emerald-100' : ($laporan->status === 'revisi' ? 'text-amber-700 bg-amber-50 border-amber-100' : 'text-indigo-700 bg-indigo-50 border-indigo-100') }}">{{ strtoupper($laporan->status) }}</span>
            @if(($extensionRequests ?? collect())->count() > 0)
                <span class="px-3 py-1 text-[10px] font-black text-red-700 bg-red-50 border border-red-100 rounded-lg uppercase tracking-wider">Ada Permohonan Perpanjangan</span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-3">Uraian Laporan Akhir</h3>
                <div class="text-sm font-medium text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50/50 p-6 rounded-xl border border-slate-100 shadow-inner">
                    {!! nl2br(e($laporan->teks_laporan ?? '-')) !!}
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-3">Lampiran Laporan Akhir</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @forelse($laporan->files ?? [] as $file)
                        <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="flex items-center justify-between p-3.5 bg-white border border-slate-200/80 rounded-xl hover:border-indigo-400 hover:shadow-sm transition-all group">
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-800 truncate group-hover:text-indigo-600 transition-colors">{{ $file->file_name ?? basename($file->file_path) }}</p>
                                <p class="text-[9px] font-mono text-slate-400 uppercase mt-0.5">Klik untuk membuka lampiran</p>
                            </div>
                            <span class="text-[10px] font-black text-indigo-600 uppercase">Buka</span>
                        </a>
                    @empty
                        <div class="col-span-1 sm:col-span-2 text-center py-6 text-slate-400 italic text-xs">Pengguna tidak melampirkan berkas akhir.</div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-3">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">Daftar Laporan Harian Terkait</h3>
                    <span class="px-3 py-1 rounded-xl bg-blue-50 border border-blue-100 text-[10px] font-black text-blue-700 uppercase tracking-wider">
                        {{ ($dailyReports ?? collect())->count() }} Data
                    </span>
                </div>

                <div class="space-y-3 max-h-[32rem] overflow-y-auto pr-1">
                    @forelse(($dailyReports ?? collect()) as $daily)
                        <div class="p-4 rounded-2xl border border-blue-100 bg-blue-50/40">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-2">
                                <div>
                                    <p class="text-xs font-black text-blue-800 uppercase tracking-wider">
                                        {{ \Carbon\Carbon::parse($daily->tanggal_laporan)->locale('id')->translatedFormat('d F Y') }}
                                    </p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">
                                        {{ $daily->user->name ?? $daily->id_user }} / {{ $daily->id_user }}
                                    </p>
                                </div>
                                @if($daily->file_path)
                                    <a href="{{ asset('storage/' . $daily->file_path) }}" target="_blank" class="inline-flex px-3 py-1.5 bg-white border border-blue-100 text-blue-700 rounded-lg text-[10px] font-black uppercase tracking-wider">Buka Lampiran</a>
                                @endif
                            </div>
                            <p class="text-xs font-medium text-slate-700 leading-relaxed whitespace-pre-line">{{ $daily->progres }}</p>
                            @if($daily->kendala)
                                <p class="text-[11px] font-semibold text-amber-700 mt-2">Kendala: {{ $daily->kendala }}</p>
                            @endif
                            @if($daily->rencana_lanjut)
                                <p class="text-[11px] font-semibold text-indigo-700 mt-1">Rencana lanjut: {{ $daily->rencana_lanjut }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-center py-6 text-slate-400 text-xs font-medium italic">Belum ada laporan harian untuk penugasan ini.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-3">Chat Evaluasi & Permohonan Perpanjangan</h3>

                @if(($extensionRequests ?? collect())->count() > 0)
                    <div class="space-y-3">
                        @foreach($extensionRequests as $requestItem)
                            <div class="bg-red-50 border border-red-100 rounded-2xl p-4">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-2">
                                    <p class="text-xs font-black text-red-700 uppercase tracking-wider">Permohonan Perpanjangan</p>
                                    <a href="{{ route('admin.penugasan.show', $laporan->penugasan->id) }}" class="text-[10px] font-black text-red-700 bg-white border border-red-100 px-2 py-1 rounded-lg uppercase">Tinjau Deadline</a>
                                </div>
                                <p class="text-xs font-semibold text-slate-700 leading-relaxed">{{ $requestItem->alasan_keterlambatan }}</p>
                                <p class="text-[10px] font-bold text-slate-400 mt-2 uppercase">Diajukan oleh: {{ $requestItem->user->name ?? $requestItem->id_user }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="space-y-4 max-h-96 overflow-y-auto pr-2 pb-2">
                    @forelse($laporan->chats ?? [] as $chat)
                        <div class="flex flex-col {{ $chat->is_from_admin_panel ? 'items-end' : 'items-start' }} gap-1 w-full">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $chat->is_from_admin_panel ? 'Admin' : 'Anggota' }} • {{ \Carbon\Carbon::parse($chat->created_at)->format('d M, H:i') }}</span>
                            <div class="{{ $chat->is_from_admin_panel ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 border border-slate-200/60' }} px-4 py-3 rounded-2xl text-xs font-medium max-w-[85%] leading-relaxed">{{ $chat->pesan }}</div>
                        </div>
                    @empty
                        <p class="text-center py-6 text-slate-400 text-xs font-medium">Belum ada chat evaluasi.</p>
                    @endforelse
                </div>

                <form action="{{ route('admin.laporan.chat.store') }}" method="POST" class="mt-4 flex gap-2 border-t border-slate-100 pt-4">
                    @csrf
                    <input type="hidden" name="id_laporan" value="{{ $laporan->id }}">
                    <input type="text" name="pesan" placeholder="Ketik balasan atau catatan evaluasi..." required class="w-full px-4 py-2.5 text-xs font-medium text-slate-800 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                    <button type="submit" class="shrink-0 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-black uppercase tracking-wider">Kirim</button>
                </form>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-4">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">Keputusan Admin</h3>
                <form action="{{ route('admin.laporan.updateStatus', $laporan->id) }}" method="POST" class="space-y-4" id="formUpdateStatus">
                    @csrf
                    @method('PUT')
                    <select name="status" id="statusSelect" required class="w-full px-4 py-3 text-sm font-bold text-slate-700 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none">
                        <option value="">-- Pilih Keputusan --</option>
                        <option value="disetujui" {{ $laporan->status === 'disetujui' ? 'selected' : '' }}>Setujui Laporan Akhir</option>
                        <option value="revisi" {{ $laporan->status === 'revisi' ? 'selected' : '' }}>Minta Revisi</option>
                    </select>
                    <textarea name="pesan_revisi" id="pesan_revisi" rows="3" class="w-full px-4 py-3 text-xs font-medium text-slate-700 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none resize-none" placeholder="Pesan revisi jika diperlukan..."></textarea>
                    <button type="submit" class="w-full px-4 py-3 text-xs font-black text-white bg-slate-900 hover:bg-black rounded-xl uppercase tracking-widest">Simpan Keputusan</button>
                </form>
            </div>

            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-4">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">Atur Ulang Batas Waktu</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Gunakan jika ada permohonan perpanjangan waktu dari anggota.</p>
                <form action="{{ route('admin.penugasan.updateDeadline', $laporan->penugasan->id) }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PUT')
                    <input type="datetime-local" name="batas_waktu_lapor" required class="w-full px-4 py-3 text-xs font-bold text-slate-700 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 outline-none">
                    <textarea name="catatan_admin" rows="3" class="w-full px-4 py-3 text-xs font-medium text-slate-700 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 outline-none resize-none" placeholder="Catatan admin opsional..."></textarea>
                    <button type="submit" class="w-full px-4 py-3 text-xs font-black text-white bg-red-600 hover:bg-red-700 rounded-xl uppercase tracking-wider">Set Batas Waktu Baru</button>
                </form>
            </div>

            <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-5 shadow-inner space-y-4">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest border-b border-slate-200 pb-3">Informasi Pengirim</h3>
                <p class="text-sm font-bold text-slate-800">{{ $laporan->user_id }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Anggota Penugasan</p>
            </div>
        </div>
    </div>
</div>

<script>
    const statusSelect = document.getElementById('statusSelect');
    const pesanRevisi = document.getElementById('pesan_revisi');
    if (statusSelect && pesanRevisi) {
        statusSelect.addEventListener('change', function () {
            if (this.value === 'revisi') {
                pesanRevisi.setAttribute('required', 'required');
            } else {
                pesanRevisi.removeAttribute('required');
            }
        });
    }
</script>

<style>
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
</style>
@endsection
