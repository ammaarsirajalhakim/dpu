@extends('layout.layoutadmin')

@section('title', 'Tambah Tugas Baru')

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
                <svg class="w-7 h-7 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Tugas Baru
            </h2>
            <p class="text-sm text-gray-500 mt-1 font-mono">>_ SYSTEM_PROMPT: Formulir pendelegasian tugas master.</p>
        </div>
        <div class="text-right mt-4 md:mt-0 bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-100 flex items-center">
            <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse mr-2"></div>
            <p id="liveTime" class="font-semibold text-gray-800 text-sm"></p>
        </div>
    </div>

    <div class="hud-panel delay-100 bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col overflow-hidden max-w-5xl mx-auto">
        
        <div class="bg-black px-6 py-4 flex items-center">
            <svg class="w-5 h-5 text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <h3 class="text-white text-sm font-bold tracking-wide uppercase">Form Data Master Tugas</h3>
        </div>

        <form action="{{ route('admin.tugas.store') }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8 bg-gray-50/30">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                
                <div class="space-y-6">
                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-200 pb-2 mb-4">A. Informasi Utama</h4>
                    
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="text-xs font-bold text-gray-700 uppercase tracking-wide">Kode Tugas <span class="text-red-500">*</span></label>
                            
                            <label class="inline-flex items-center cursor-pointer group">
                                <input type="checkbox" id="autoGenerateToggle" class="sr-only peer">
                                <div class="relative w-9 h-5 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600 shadow-inner"></div>
                                <span class="ms-2 text-[10px] font-bold text-gray-500 group-hover:text-blue-600 uppercase transition-colors">Isi Otomatis</span>
                            </label>
                        </div>
                        <div class="relative">
                            <input type="text" name="kodetugas" id="kodetugas" required maxlength="10" placeholder="Contoh: TGS-001" 
                                class="w-full bg-white border border-gray-300 rounded-lg pl-10 pr-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                            </div>
                        </div>
                        <p class="text-[10px] text-gray-500 mt-1 font-mono tracking-tighter">Maks. 10 Karakter Alfanumerik</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">Nama Tugas <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_tugas" required placeholder="Contoh: Audit Laporan Keuangan" 
                            class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-sm font-semibold text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">Deskripsi Detail <span class="text-red-500">*</span></label>
                        <textarea name="deskripsi" rows="5" required placeholder="Jelaskan instruksi, tujuan, dan detail operasional tugas..." 
                            class="w-full bg-white border border-gray-300 rounded-lg px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm leading-relaxed"></textarea>
                    </div>
                </div>

                <div class="space-y-6">
                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-200 pb-2 mb-4">B. Penjadwalan & Berkas</h4>
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">Tgl. & Waktu Mulai <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="datetime-local" name="tanggal_mulai" required 
                                class="w-full bg-white border border-gray-300 rounded-lg pl-10 pr-4 py-2.5 text-sm font-mono text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">Tgl. & Waktu Selesai <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="datetime-local" name="tanggal_selesai" required 
                                class="w-full bg-white border border-gray-300 rounded-lg pl-10 pr-4 py-2.5 text-sm font-mono text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">Upload Lampiran <span class="text-gray-400 font-normal">(Opsional)</span></label>
                        
                        <div class="flex items-center justify-center w-full mt-1">
                            <label for="lampiran" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-white hover:bg-blue-50 hover:border-blue-400 transition-colors group relative overflow-hidden">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-3 text-gray-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    <p class="mb-1 text-sm text-gray-500 group-hover:text-blue-600"><span class="font-bold">Klik untuk upload</span> atau drag & drop</p>
                                    <p class="text-[10px] text-gray-400 font-mono">JPG, PNG, PDF (Maks. 2MB)</p>
                                </div>
                                <input id="lampiran" name="lampiran" type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="updateFileName(this)"/>
                            </label>
                        </div>
                        
                        <p id="file-name-display" class="text-xs text-blue-600 font-bold mt-2 hidden flex items-center bg-blue-50 p-2 rounded border border-blue-100">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                            <span></span>
                        </p>
                    </div>

                </div>
            </div>

            <div class="border-t border-gray-200 pt-6 flex justify-end items-center space-x-3 mt-4">
                <a href="{{ route('admin.tugas.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition text-sm font-bold shadow-sm">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-transform transform hover:scale-105 text-sm font-black tracking-widest uppercase shadow-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan Tugas
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

    // Tampilkan Nama File yang diupload
    function updateFileName(input) {
        const display = document.getElementById('file-name-display');
        const span = display.querySelector('span');
        if (input.files && input.files.length > 0) {
            span.textContent = input.files[0].name;
            display.classList.remove('hidden');
        } else {
            display.classList.add('hidden');
        }
    }

    // Auto-generate code
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('autoGenerateToggle');
        const inputKodetugas = document.getElementById('kodetugas');

        function generateRandomCode() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let randomPart = '';
            for (let i = 0; i < 6; i++) {
                randomPart += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            return 'TGS-' + randomPart;
        }

        toggle.addEventListener('change', function() {
            if (this.checked) {
                inputKodetugas.value = generateRandomCode();
                inputKodetugas.setAttribute('readonly', true);
                // Style saat readonly aktif
                inputKodetugas.classList.add('bg-gray-100', 'text-gray-500', 'cursor-not-allowed', 'shadow-inner');
                inputKodetugas.classList.remove('bg-white');
            } else {
                inputKodetugas.value = '';
                inputKodetugas.removeAttribute('readonly');
                // Kembalikan style normal
                inputKodetugas.classList.remove('bg-gray-100', 'text-gray-500', 'cursor-not-allowed', 'shadow-inner');
                inputKodetugas.classList.add('bg-white');
            }
        });
    });
</script>
@endsection