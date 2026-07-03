@extends('layout.layoutadmin')

@section('title', 'Detail Tugas')

@section('content')
<style>
    /* CSS Animasi dan Scrollbar Textarea */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .hud-panel {
        opacity: 0;
        animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    .delay-100 { animation-delay: 100ms; }
    .delay-200 { animation-delay: 200ms; }
    
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<div class="container mx-auto px-4 py-6 font-sans">
    
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-end border-b border-gray-300 pb-4 hud-panel">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center uppercase tracking-wide">
                <svg class="w-7 h-7 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0 5l4-8 4 8m-4-8v8"></path></svg>
                Detail Master Tugas
            </h2>
            <p class="text-sm text-gray-500 mt-1 font-mono">>_ SYSTEM_PROMPT: Menampilkan record tugas [ <span class="text-blue-600 font-bold">{{ $tugas->kodetugas }}</span> ]</p>
        </div>
        <div class="text-right mt-4 md:mt-0 flex items-center space-x-3">
            <a href="{{ route('admin.tugas.index') }}" class="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition font-bold text-xs flex items-center shadow-sm uppercase tracking-wider">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
            <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-100 flex items-center hidden sm:flex">
                <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse mr-2"></div>
                <p id="liveTime" class="font-semibold text-gray-800 text-sm"></p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 space-y-6">
            
            <div class="hud-panel delay-100 bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col overflow-hidden">
                <div class="bg-black px-6 py-4 flex justify-between items-center">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <h3 class="text-white text-sm font-bold tracking-wide uppercase">Spesifikasi Detail</h3>
                    </div>
                    
                    @php
                        $today = \Carbon\Carbon::today();
                        $start = \Carbon\Carbon::parse($tugas->tanggal_mulai);
                        $end = \Carbon\Carbon::parse($tugas->tanggal_selesai);
                    @endphp
                    
                    @if ($today->lt($start))
                        <span class="px-3 py-1 text-[10px] font-bold rounded bg-gray-200 text-gray-800 tracking-wider">MENDATANG</span>
                    @elseif($today->gt($end))
                        <span class="px-3 py-1 text-[10px] font-bold rounded bg-red-600 text-white tracking-wider shadow">SELESAI / TERLEWAT</span>
                    @else
                        <span class="px-3 py-1 text-[10px] font-bold rounded bg-blue-600 text-white tracking-wider shadow">AKTIF BERJALAN</span>
                    @endif
                </div>

                <div class="p-6">
                    <h3 class="text-2xl font-black text-gray-900 mb-1">{{ $tugas->nama_tugas }}</h3>
                    <div class="mb-6 flex items-center">
                        <span class="text-xs font-mono text-gray-500 bg-gray-100 px-2 py-1 rounded border border-gray-200">KODE: {{ $tugas->kodetugas }}</span>
                    </div>

                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-200 pb-2 mb-4">Instruksi & Deskripsi</h4>
                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-100 text-sm text-gray-700 leading-relaxed whitespace-pre-wrap min-h-[120px] font-medium">{{ $tugas->deskripsi }}</div>
                </div>
            </div>

            <div class="hud-panel delay-200 bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col overflow-hidden">
                <div class="bg-black px-6 py-4 flex items-center">
                    <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                    <h3 class="text-white text-sm font-bold tracking-wide uppercase">Pratinjau Berkas Lampiran</h3>
                </div>

                <div class="p-6 bg-gray-50/50">
                    @if($tugas->lampiran)
                        @php
                            $extension = strtolower(pathinfo($tugas->lampiran, PATHINFO_EXTENSION));
                            $fileUrl = Storage::url($tugas->lampiran);
                            $fullUrl = url($fileUrl);
                        @endphp

                        <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-3 rounded-lg border border-gray-200 shadow-sm gap-3">
                            <div class="flex items-center">
                                <span class="text-[10px] font-black uppercase bg-blue-100 text-blue-700 px-3 py-1.5 rounded">{{ $extension }}</span>
                                <span class="ml-3 text-sm text-gray-700 font-medium truncate max-w-[200px] md:max-w-xs">{{ basename($tugas->lampiran) }}</span>
                            </div>
                            <a href="{{ $fileUrl }}" target="_blank" download class="inline-flex items-center px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-[10px] font-black uppercase tracking-widest shadow-sm w-full sm:w-auto justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Unduh File
                            </a>
                        </div>

                        <div class="bg-gray-900 rounded-xl border-4 border-gray-800 p-1 overflow-hidden flex justify-center items-center min-h-[300px] relative">
                            @if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']))
                                <img src="{{ $fileUrl }}" alt="Lampiran Tugas" class="max-w-full max-h-[800px] object-contain rounded">
                            
                            @elseif($extension === 'pdf')
                                <iframe src="{{ $fileUrl }}" class="w-full h-[600px] rounded border-0 bg-white" title="Pratinjau PDF"></iframe>
                                
                            @elseif(in_array($extension, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']))
                                <div class="w-full h-full flex flex-col items-center justify-center p-8 bg-gray-800 rounded">
                                    <iframe src="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode($fullUrl) }}" class="w-full h-[600px] rounded border-0 bg-white" title="Pratinjau Dokumen Office"></iframe>
                                    <p class="text-xs text-yellow-400 mt-4 text-center font-mono">
                                        >_ INFO: Format Ms.Office di-render via Microsoft Public API. Jika error di localhost, silakan Unduh file.
                                    </p>
                                </div>
                                
                            @elseif($extension === 'txt')
                                <iframe src="{{ $fileUrl }}" class="w-full h-[400px] rounded border-0 bg-white p-4 font-mono text-sm" title="Pratinjau Text"></iframe>

                            @else
                                <div class="py-16 text-center">
                                    <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <h5 class="text-gray-400 font-bold mb-1 uppercase tracking-wider">Format Tidak Didukung</h5>
                                    <p class="text-gray-500 text-xs font-mono">>_ Browser gagal membaca format: .{{ $extension }}</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="bg-white p-10 rounded-xl border border-dashed border-gray-300 text-center flex flex-col items-center">
                            <svg class="w-14 h-14 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <p class="text-sm text-gray-500 font-bold">Tidak ada file lampiran yang disematkan.</p>
                            <p class="text-xs text-gray-400 font-mono mt-2">>_ STATUS: NULL</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-6">
            
            <div class="hud-panel delay-100 bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col overflow-hidden">
                <div class="bg-black px-5 py-3 flex items-center">
                    <svg class="w-4 h-4 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <h3 class="text-white text-xs font-bold tracking-wide uppercase">Waktu Operasional</h3>
                </div>
                <div class="p-5 space-y-5">
                    <div class="flex items-start">
                        <div class="p-2.5 bg-blue-50 border border-blue-100 rounded-lg mr-4 shadow-sm">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-0.5">Mulai Pengerjaan</p>
                            <p class="text-sm font-bold text-gray-900">{{ \Carbon\Carbon::parse($tugas->tanggal_mulai)->translatedFormat('d M Y') }}</p>
                            <p class="text-xs text-blue-600 font-mono mt-0.5">{{ \Carbon\Carbon::parse($tugas->tanggal_mulai)->format('H:i') }} WIB</p>
                        </div>
                    </div>
                    
                    <div class="ml-5 w-0.5 h-6 bg-gray-200"></div>

                    <div class="flex items-start">
                        <div class="p-2.5 bg-red-50 border border-red-100 rounded-lg mr-4 shadow-sm">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-0.5">Tenggat Waktu Selesai</p>
                            <p class="text-sm font-bold text-gray-900">{{ \Carbon\Carbon::parse($tugas->tanggal_selesai)->translatedFormat('d M Y') }}</p>
                            <p class="text-xs text-red-600 font-mono mt-0.5">{{ \Carbon\Carbon::parse($tugas->tanggal_selesai)->format('H:i') }} WIB</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="hud-panel delay-200 bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col overflow-hidden">
                <div class="bg-black px-5 py-3 flex items-center">
                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <h3 class="text-white text-xs font-bold tracking-wide uppercase">Otoritas Pembuat</h3>
                </div>
                <div class="p-5 bg-gray-50/50">
                    <div class="mb-4">
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-1">Nama Pendelegasi</p>
                        <p class="text-sm font-bold text-gray-800">{{ $tugas->admin->name ?? 'Admin (User Tidak Ditemukan)' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-1">Log Dibuat</p>
                        <p class="text-xs font-mono text-gray-600 bg-gray-100 px-2 py-1 inline-block rounded border border-gray-200">{{ $tugas->created_at->translatedFormat('d M Y, H:i:s') }}</p>
                    </div>
                </div>
            </div>

            <div class="hud-panel delay-300">
                <a href="{{ route('admin.tugas.edit', $tugas->kodetugas) }}" class="w-full py-4 bg-yellow-400 text-gray-900 rounded-xl hover:bg-yellow-500 transition-transform transform hover:-translate-y-1 shadow-lg flex items-center justify-center font-black tracking-widest uppercase text-sm border-2 border-yellow-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit Parameter Tugas
                </a>
            </div>

        </div>
    </div>
</div>

<script>
    // Live Time Update
    function updateClock() {
        const opt = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
        document.getElementById('liveTime').innerText = new Date().toLocaleDateString('id-ID', opt) + ' WIB';
    }
    setInterval(updateClock, 1000); updateClock();
</script>
@endsection