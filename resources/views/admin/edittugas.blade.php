@extends('layout.layoutadmin')

@section('title', 'Edit Data Tugas')

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
    
    textarea::-webkit-scrollbar { width: 6px; }
    textarea::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
    textarea::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    textarea::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<div class="container mx-auto px-4 py-6 font-sans">
    
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-end border-b border-gray-300 pb-4 hud-panel">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center uppercase tracking-wide">
                <svg class="w-7 h-7 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit Data Tugas
            </h2>
            <p class="text-sm text-gray-500 mt-1 font-mono">>_ SYSTEM_PROMPT: Perbarui parameter operasional tugas.</p>
        </div>
        <div class="text-right mt-4 md:mt-0 bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-100 flex items-center">
            <div class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse mr-2"></div>
            <p id="liveTime" class="font-semibold text-gray-800 text-sm"></p>
        </div>
    </div>

    <div class="hud-panel delay-100 bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col overflow-hidden max-w-5xl mx-auto">
        
        <div class="bg-black px-6 py-4 flex items-center">
            <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <h3 class="text-white text-sm font-bold tracking-wide uppercase">Revisi Data Master</h3>
        </div>

        <form action="{{ route('admin.tugas.update', $tugas->kodetugas) }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8 bg-gray-50/30">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                
                <div class="space-y-6">
                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-200 pb-2 mb-4">A. Informasi Utama</h4>
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">Kode Tugas</label>
                        <div class="relative">
                            <input type="text" name="kodetugas" value="{{ $tugas->kodetugas }}" readonly 
                                class="w-full bg-gray-100 border border-gray-200 rounded-lg pl-10 pr-4 py-2.5 text-sm font-mono text-gray-500 cursor-not-allowed shadow-inner focus:outline-none">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                        </div>
                        <p class="text-[10px] text-red-400 mt-1 font-mono tracking-tighter">Kode ID Sistem bersifat permanen.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">Nama Tugas <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_tugas" value="{{ old('nama_tugas', $tugas->nama_tugas) }}" required 
                            class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-sm font-semibold text-gray-900 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition-all shadow-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">Deskripsi Detail <span class="text-red-500">*</span></label>
                        <textarea name="deskripsi" rows="5" required 
                            class="w-full bg-white border border-gray-300 rounded-lg px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition-all shadow-sm leading-relaxed">{{ old('deskripsi', $tugas->deskripsi) }}</textarea>
                    </div>
                </div>

                <div class="space-y-6">
                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-200 pb-2 mb-4">B. Penjadwalan & Berkas</h4>
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">Tgl. & Waktu Mulai <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="datetime-local" name="tanggal_mulai" value="{{ \Carbon\Carbon::parse($tugas->tanggal_mulai)->format('Y-m-d\TH:i') }}" required 
                                class="w-full bg-white border border-gray-300 rounded-lg pl-10 pr-4 py-2.5 text-sm font-mono text-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition-all shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">Tgl. & Waktu Selesai <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="datetime-local" name="tanggal_selesai" value="{{ \Carbon\Carbon::parse($tugas->tanggal_selesai)->format('Y-m-d\TH:i') }}" required 
                                class="w-full bg-white border border-gray-300 rounded-lg pl-10 pr-4 py-2.5 text-sm font-mono text-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition-all shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">Update Lampiran Baru <span class="text-gray-400 font-normal">(Opsional)</span></label>
                        
                        <div class="flex items-center justify-center w-full mt-1">
                            <label for="lampiran" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-white hover:bg-yellow-50 hover:border-yellow-400 transition-colors group relative overflow-hidden">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-3 text-gray-400 group-hover:text-yellow-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    <p class="mb-1 text-sm text-gray-500 group-hover:text-yellow-600"><span class="font-bold">Klik untuk ganti file</span> atau drag & drop</p>
                                    <p class="text-[10px] text-gray-400 font-mono">Biarkan kosong jika tidak ingin mengubah.</p>
                                </div>
                                <input id="lampiran" name="lampiran" type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="updateFileName(this)"/>
                            </label>
                        </div>

                        <p id="file-name-display" class="text-xs text-yellow-600 font-bold mt-2 hidden flex items-center bg-yellow-50 p-2 rounded border border-yellow-100">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                            <span></span>
                        </p>

                        @if($tugas->lampiran)
                            <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg flex justify-between items-center shadow-sm">
                                <div class="flex items-center">
                                    <div class="bg-blue-100 p-2 rounded mr-3">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">File Saat Ini</p>
                                        <p class="text-xs text-blue-700 font-semibold truncate max-w-[150px]">{{ basename($tugas->lampiran) }}</p>
                                    </div>
                                </div>
                                <a href="{{ asset('storage/' . $tugas->lampiran) }}" target="_blank" class="text-[10px] bg-white border border-blue-200 text-blue-600 hover:bg-blue-600 hover:text-white px-2 py-1 rounded transition font-bold">
                                    LIHAT
                                </a>
                            </div>
                        @endif
                    </div>

                </div>
            </div>

            <div class="border-t border-gray-200 pt-6 flex justify-end items-center space-x-3 mt-4">
                <a href="{{ route('admin.tugas.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition text-sm font-bold shadow-sm">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-yellow-500 text-gray-900 rounded-lg hover:bg-yellow-400 transition-transform transform hover:scale-105 text-sm font-black tracking-widest uppercase shadow-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Live Time Update
    function updateClock() {
        const opt = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
        document.getElementById('liveTime').innerText = new Date().toLocaleDateString('id-ID', opt) + ' WIB';
    }
    setInterval(updateClock, 1000); updateClock();

    // Tampilkan Nama File yang Baru Diupload
    function updateFileName(input) {
        const display = document.getElementById('file-name-display');
        const span = display.querySelector('span');
        if (input.files && input.files.length > 0) {
            span.textContent = "Terpilih: " + input.files[0].name;
            display.classList.remove('hidden');
        } else {
            display.classList.add('hidden');
        }
    }
</script>
@endsection