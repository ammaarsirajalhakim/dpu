@extends('layout.layout')

@section('content')
<div class="space-y-6 animate-fade-in">
    
    <div class="space-y-3 animate-slide-up">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
                <li>
                    <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <svg class="w-3 h-3 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                </li>
                <li class="text-slate-800 font-bold">Riwayat Laporan</li>
            </ol>
        </nav>

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2z"/>
                    </svg>
                    Riwayat Laporan Saya
                </h1>
                <p class="text-xs font-medium text-slate-400 mt-1">Daftar arsip rekapitulasi capaian fisik proyek infrastruktur resmi Dinas Pekerjaan Umum (DPU) Kabupaten Semarang.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3 self-start md:self-auto shrink-0">
                <div class="text-xs font-bold text-slate-700 bg-slate-100 border border-slate-200 px-4 py-2.5 rounded-xl shadow-inner">
                    Total Arsip: {{ $laporans->count() }} Dokumen
                </div>
                <a href="{{ route('penugasan.index') }}"
                   class="flex items-center px-4 py-2.5 text-xs font-black text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 rounded-xl shadow-md shadow-blue-500/10 transition-all uppercase tracking-wider hover:-translate-y-0.5 duration-200">
                    <svg class="w-4 h-4 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Laporan Baru
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden animate-slide-up" style="animation-delay: 100ms;">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center">
                        <th class="py-4 px-6 text-left w-16">No</th>
                        <th class="py-4 px-6 text-left">Tugas / Proyek Infrastruktur</th>
                        <th class="py-4 px-6 text-left">Tanggal Pengiriman</th>
                        <th class="py-4 px-6">Status Verifikasi</th>
                        <th class="py-4 px-6 w-40">Aksi Berkas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 text-xs font-medium">
                    @forelse($laporans as $index => $l)
                        <tr class="hover:bg-slate-50/50 transition-colors duration-150 group">
                            <td class="py-4 px-6 text-slate-400 font-bold text-center">
                                {{ $index + 1 }}
                            </td>
                            
                            <td class="py-4 px-6">
                                <div class="space-y-1">
                                    <span class="font-mono font-bold text-blue-600 text-[10px] bg-blue-50 border border-blue-100 px-1.5 py-0.5 rounded shadow-inner">
                                        {{ $l->penugasan->kodetugas ?? '-' }}
                                    </span>
                                    <h3 class="text-slate-800 font-bold text-xs mt-1 truncate max-w-md group-hover:text-blue-600 transition-colors">
                                        {{ $l->penugasan->tugas->nama_tugas ?? 'Nama Tugas Tidak Ditemukan' }}
                                    </h3>
                                </div>
                            </td>
                            
                            <td class="py-4 px-6 text-slate-500 font-mono text-[11px]">
                                {{ \Carbon\Carbon::parse($l->created_at)->locale('id')->translatedFormat('d F Y, H:i') }} WIB
                            </td>
                            
                            <td class="py-4 px-6 text-center">
                                @if($l->status === 'disetujui')
                                    <span class="inline-flex items-center px-2.5 py-1 text-[10px] font-black text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-md uppercase tracking-wider">
                                        Disetujui
                                    </span>
                                @elseif($l->status === 'revisi')
                                    <span class="inline-flex items-center px-2.5 py-1 text-[10px] font-black text-amber-700 bg-amber-50 border border-amber-100 rounded-md uppercase tracking-wider animate-pulse">
                                        Perlu Revisi
                                    </span>
                                @elseif($l->status === 'diajukan' || $l->status === 'menunggu')
                                    <span class="inline-flex items-center px-2.5 py-1 text-[10px] font-black text-blue-700 bg-blue-50 border border-blue-100 rounded-md uppercase tracking-wider">
                                        Sedang Ditinjau
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 text-[10px] font-black text-slate-500 bg-slate-100 border border-slate-200 rounded-md uppercase tracking-wider">
                                        Belum Lapor
                                    </span>
                                @endif
                            </td>
                            
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center">
                                    <a href="{{ route('laporan.show', $l->id) }}"
                                       class="inline-flex items-center px-3.5 py-2 text-[10px] font-bold text-slate-700 bg-slate-100 hover:bg-slate-900 hover:text-white rounded-xl transition-all uppercase tracking-wider shadow-sm"
                                       title="Buka Rincian Lembar Berkas Pengajuan">
                                        LIHAT RINCIAN
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center text-slate-400 font-medium">
                                <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                Anda belum mengirimkan berkas laporan kemajuan proyek apapun ke dalam sistem database DPU.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<style>
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    
    .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
    .animate-slide-up { animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
@endsection