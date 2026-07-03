@extends('layout.layoutadmin')

@section('title', 'Detail Penugasan')

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
            <span class="text-[10px] font-black text-blue-600 bg-blue-50 border border-blue-100 px-2.5 py-0.5 rounded-md uppercase tracking-wider font-mono">
                PENUGASAN #{{ $penugasan->id }}
            </span>
            <h1 class="text-xl md:text-2xl font-black text-slate-900 tracking-tight mt-2 leading-tight">
                {{ $penugasan->tugas->nama_tugas ?? 'Detail Penugasan' }}
            </h1>
            <p class="text-xs font-medium text-slate-400 mt-1">Kode tugas: {{ $penugasan->kodetugas }}</p>
        </div>
        <a href="{{ route('admin.penugasan.index') }}" class="px-4 py-2.5 bg-slate-900 text-white rounded-xl text-xs font-black uppercase tracking-wider">Kembali</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-3">Deskripsi Tugas</h3>
                <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-wrap bg-slate-50 p-5 rounded-xl border border-slate-100">
                    {{ $penugasan->tugas->deskripsi ?? 'Tidak ada deskripsi tersedia.' }}
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-3">Anggota Penugasan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @forelse($penugasan->anggota ?? [] as $anggota)
                        <div class="p-4 rounded-2xl border {{ $anggota->status_keterlambatan === 'mengajukan' ? 'border-red-200 bg-red-50' : 'border-slate-200 bg-slate-50' }}">
                            <p class="text-sm font-black text-slate-900">{{ $anggota->user->name ?? 'User Tidak Diketahui' }}</p>
                            <p class="text-[10px] font-mono text-slate-400 uppercase mt-0.5">NIP: {{ $anggota->user->nip ?? $anggota->id_user }}</p>
                            @if($anggota->status_keterlambatan === 'mengajukan')
                                <div class="mt-3 p-3 rounded-xl bg-white border border-red-100">
                                    <p class="text-[10px] font-black text-red-700 uppercase tracking-widest">Permohonan Perpanjangan</p>
                                    <p class="text-xs font-semibold text-slate-700 mt-1 leading-relaxed">{{ $anggota->alasan_keterlambatan }}</p>
                                </div>
                            @elseif($anggota->status_keterlambatan === 'disetujui')
                                <p class="mt-2 text-[10px] font-black text-emerald-700 uppercase tracking-wider">Perpanjangan disetujui</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic">Belum ada anggota penugasan.</p>
                    @endforelse
                </div>
            </div>

            @if(($extensionRequests ?? collect())->count() > 0)
                <div class="bg-red-50 border border-red-100 rounded-2xl p-6 shadow-sm space-y-4">
                    <h3 class="text-xs font-black text-red-700 uppercase tracking-widest border-b border-red-100 pb-3">Chat Permohonan Perpanjangan Waktu</h3>
                    @foreach($extensionRequests as $requestItem)
                        <div class="bg-white border border-red-100 rounded-2xl p-4">
                            <p class="text-xs font-black text-slate-900">{{ $requestItem->user->name ?? $requestItem->id_user }}</p>
                            <p class="text-xs font-semibold text-slate-700 mt-2 leading-relaxed">{{ $requestItem->alasan_keterlambatan }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-4">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">Status & Tenggat Waktu</h3>
                @php
                    $batasLapor = \Carbon\Carbon::parse($penugasan->batas_waktu_lapor);
                    $terlewat = now()->greaterThan($batasLapor);
                @endphp
                <div class="p-4 rounded-xl {{ $terlewat ? 'bg-red-50 border border-red-100 text-red-700' : 'bg-blue-50 border border-blue-100 text-blue-700' }}">
                    <p class="text-[10px] font-black uppercase tracking-widest">Batas Laporan Akhir</p>
                    <p class="text-sm font-black mt-1">{{ $batasLapor->locale('id')->translatedFormat('d F Y, H:i') }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-4">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">Set Ulang Batas Waktu Lapor</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Gunakan untuk menyetujui permohonan perpanjangan dan membuka kembali akses laporan akhir.</p>
                <form action="{{ route('admin.penugasan.updateDeadline', $penugasan->id) }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PUT')
                    <input type="datetime-local" name="batas_waktu_lapor" required class="w-full px-4 py-3 text-xs font-bold text-slate-700 bg-slate-50 border border-slate-200 rounded-xl focus:border-blue-500 outline-none">
                    <textarea name="catatan_admin" rows="3" class="w-full px-4 py-3 text-xs font-medium text-slate-700 bg-slate-50 border border-slate-200 rounded-xl focus:border-blue-500 outline-none resize-none" placeholder="Catatan admin opsional..."></textarea>
                    <button type="submit" class="w-full px-4 py-3 text-xs font-black text-white bg-red-600 hover:bg-red-700 rounded-xl uppercase tracking-wider">Simpan Batas Waktu Baru</button>
                </form>
            </div>

            <div class="space-y-3">
                <a href="{{ route('admin.penugasan.edit', $penugasan->id) }}" class="w-full inline-flex justify-center py-3.5 bg-yellow-400 text-gray-900 rounded-xl hover:bg-yellow-500 font-black tracking-widest uppercase text-xs">Revisi Parameter Tugas</a>
                <form action="{{ route('admin.penugasan.destroy', $penugasan->id) }}" method="POST" onsubmit="return confirm('Cabut penugasan ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full py-3.5 bg-white text-red-600 border-2 border-red-200 rounded-xl hover:bg-red-50 font-black tracking-widest uppercase text-xs">Cabut Penugasan Ini</button>
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
