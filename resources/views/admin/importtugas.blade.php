<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<div id="importModalTugas" class="fixed inset-0 z-[70] hidden flex items-center justify-center bg-black/80 transition-opacity p-4 backdrop-blur-sm">
    
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-7xl max-h-[90vh] flex flex-col overflow-hidden hud-panel">
        
        <div class="bg-black px-6 py-4 flex justify-between items-center border-b-4 border-blue-600">
            <h3 class="text-white font-bold flex items-center tracking-wide uppercase text-sm">
                <svg class="w-5 h-5 text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Terminal Import Data Tugas
            </h3>
            <button type="button" onclick="closeImportModalTugas()" class="text-gray-400 hover:text-white transition focus:outline-none">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-6 overflow-y-auto flex-grow relative bg-gray-50/30">
            
            <div id="dragDropAreaTugas" class="border-2 border-dashed border-blue-400 bg-blue-50/50 rounded-xl p-12 text-center hover:bg-blue-100 hover:border-blue-500 transition cursor-pointer relative group">
                <input type="file" id="excelFileTugas" accept=".xlsx, .xls" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                <svg class="mx-auto h-16 w-16 text-blue-500 mb-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                <p class="text-gray-900 font-black text-lg uppercase tracking-wide">Klik Atau Tarik File Excel Ke Sini</p>
                <p class="text-sm text-gray-500 mt-2 font-mono">>_ FORMAT_DIIZINKAN : .XLSX, .XLS</p>
            </div>

            <form id="importFormTugas" action="{{ route('admin.tugas.importProcess') ?? '#' }}" method="POST" enctype="multipart/form-data" class="hidden flex-col h-full">
                @csrf
                
                <div class="mb-4 flex flex-col md:flex-row justify-between items-start md:items-center bg-gray-900 p-4 rounded-xl border-l-4 border-yellow-400 shadow-sm gap-4">
                    <p class="text-sm text-gray-300 font-mono flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        >_ STATUS : PREVIEW_DATA. Verifikasi baris tugas dan upload lampiran sebelum eksekusi.
                    </p>
                    <button type="button" onclick="resetImportTugas()" class="text-xs bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-2 rounded-lg shadow transition uppercase tracking-widest flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Batal / Ganti File
                    </button>
                </div>

                <div class="overflow-x-auto border border-gray-200 rounded-xl shadow-sm bg-white flex-grow">
                    <table class="min-w-full divide-y divide-gray-200 text-left">
                        <thead class="bg-gray-100 text-gray-600 text-[10px] uppercase font-bold tracking-wider">
                            <tr>
                                <th class="px-4 py-4 w-40">Kode Tugas</th>
                                <th class="px-4 py-4 w-48">Nama Tugas</th>
                                <th class="px-4 py-4 w-56">Deskripsi</th>
                                <th class="px-4 py-4 w-40">Tanggal Mulai</th>
                                <th class="px-4 py-4 w-40">Tanggal Selesai</th>
                                <th class="px-4 py-4 w-48">Lampiran (Opsional)</th>
                            </tr>
                        </thead>
                        <tbody id="previewTableBodyTugas" class="divide-y divide-gray-100 text-gray-800">
                            </tbody>
                    </table>
                </div>

                <div class="mt-6 flex justify-end pt-4 border-t border-gray-200">
                    <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-lg transition-transform transform hover:scale-105 text-sm font-black uppercase tracking-widest flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> 
                        Eksekusi Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const modalTugas = document.getElementById('importModalTugas');
    const dragDropAreaTugas = document.getElementById('dragDropAreaTugas');
    const importFormTugas = document.getElementById('importFormTugas');
    const fileInputTugas = document.getElementById('excelFileTugas');
    const tableBodyTugas = document.getElementById('previewTableBodyTugas');

    // Membuka Modal
    window.openImportModal = function() {
        modalTugas.classList.remove('hidden');
        modalTugas.classList.add('flex');
        resetImportTugas();
    }

    function closeImportModalTugas() {
        modalTugas.classList.add('hidden');
        modalTugas.classList.remove('flex');
    }

    function resetImportTugas() {
        fileInputTugas.value = '';
        dragDropAreaTugas.classList.remove('hidden');
        importFormTugas.classList.add('hidden');
        importFormTugas.classList.remove('flex');
        tableBodyTugas.innerHTML = '';
    }

    // Generator Kode Otomatis bergaya HUD (TGS + Random Alfanumerik)
    function generateKode(length) {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let result = '';
        for (let i = 0; i < length; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return result;
    }

    window.generateKodeRow = function(index) {
        const inputKode = document.getElementById(`kode_tugas_${index}`);
        if(inputKode) {
            inputKode.value = 'TGS' + generateKode(5);
            // Tambah efek nyala agar interaktif
            inputKode.classList.add('ring-2', 'ring-yellow-400', 'bg-yellow-50');
            setTimeout(() => inputKode.classList.remove('ring-2', 'ring-yellow-400', 'bg-yellow-50'), 500);
        }
    }

    // Drag & Drop Handlers
    dragDropAreaTugas.addEventListener('dragover', (e) => { e.preventDefault(); dragDropAreaTugas.classList.add('bg-blue-100', 'border-blue-600'); });
    dragDropAreaTugas.addEventListener('dragleave', (e) => { e.preventDefault(); dragDropAreaTugas.classList.remove('bg-blue-100', 'border-blue-600'); });
    dragDropAreaTugas.addEventListener('drop', (e) => {
        e.preventDefault();
        dragDropAreaTugas.classList.remove('bg-blue-100', 'border-blue-600');
        if(e.dataTransfer.files.length) {
            fileInputTugas.files = e.dataTransfer.files;
            processExcelTugas(e.dataTransfer.files[0]);
        }
    });
    fileInputTugas.addEventListener('change', (e) => {
        if(e.target.files.length) processExcelTugas(e.target.files[0]);
    });

    // Proses Membaca Excel
    function processExcelTugas(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, {type: 'array', cellDates: true});
            const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
            const json = XLSX.utils.sheet_to_json(firstSheet, {raw: false, dateNF: 'yyyy-mm-dd'});
            renderPreviewTugas(json);
        };
        reader.readAsArrayBuffer(file);
    }

    // Format waktu untuk input datetime-local
    function formatToDateTimeLocal(dateStr) {
        if (!dateStr) return '';
        if (dateStr.length === 10) return dateStr + 'T00:00';
        return dateStr;
    }

    // Render Preview ke dalam Tabel
    function renderPreviewTugas(data) {
        dragDropAreaTugas.classList.add('hidden');
        importFormTugas.classList.remove('hidden');
        importFormTugas.classList.add('flex');
        tableBodyTugas.innerHTML = '';

        data.forEach((row, index) => {
            const namaTugas = row['Nama Tugas'] || '';
            const deskripsi = row['Deskripsi'] || '';
            let tglMulai = row['Tanggal Mulai (YYYY-MM-DD)'] || row['Tanggal Mulai'] || '';
            let tglSelesai = row['Tanggal Selesai (YYYY-MM-DD)'] || row['Tanggal Selesai'] || '';

            tglMulai = formatToDateTimeLocal(tglMulai);
            tglSelesai = formatToDateTimeLocal(tglSelesai);

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-blue-50/50 transition-colors group';
            tr.innerHTML = `
                <td class="px-3 py-3 align-top">
                    <div class="flex flex-col space-y-1">
                        <input type="text" id="kode_tugas_${index}" name="tugas[${index}][kodetugas]" value="" required placeholder="TGS..." 
                            class="w-full text-xs bg-gray-50 border border-gray-200 rounded px-2 py-1.5 font-mono text-blue-700 font-bold focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none uppercase transition-all shadow-inner">
                        <button type="button" onclick="generateKodeRow(${index})" class="w-full py-1 text-[10px] bg-gray-900 text-yellow-400 hover:bg-gray-700 rounded transition shadow font-bold tracking-wider" title="Generate Otomatis">
                            AUTO-GEN
                        </button>
                    </div>
                </td>
                
                <td class="px-3 py-3 align-top">
                    <textarea name="tugas[${index}][nama_tugas]" required rows="2" placeholder="Nama Tugas..."
                        class="w-full text-xs bg-white border border-gray-200 rounded px-2 py-1.5 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none font-semibold text-gray-900 shadow-sm leading-tight resize-none">${namaTugas}</textarea>
                </td>
                
                <td class="px-3 py-3 align-top">
                    <textarea name="tugas[${index}][deskripsi]" required rows="2" placeholder="Deskripsi..."
                        class="w-full text-[11px] bg-white border border-gray-200 rounded px-2 py-1.5 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none text-gray-700 shadow-sm leading-relaxed">${deskripsi}</textarea>
                </td>
                
                <td class="px-3 py-3 align-top">
                    <input type="datetime-local" name="tugas[${index}][tanggal_mulai]" value="${tglMulai}" required 
                        class="w-full text-[11px] bg-gray-50 border border-gray-200 rounded px-2 py-1.5 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none text-gray-700 font-mono shadow-inner">
                </td>
                
                <td class="px-3 py-3 align-top">
                    <input type="datetime-local" name="tugas[${index}][tanggal_selesai]" value="${tglSelesai}" required 
                        class="w-full text-[11px] bg-gray-50 border border-gray-200 rounded px-2 py-1.5 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none text-gray-700 font-mono shadow-inner">
                </td>

                <td class="px-3 py-3 align-top">
                    <div class="relative overflow-hidden w-full bg-white border border-gray-200 rounded hover:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 transition shadow-sm">
                        <input type="file" name="tugas[${index}][lampiran]" accept=".jpg,.png,.pdf"
                            class="w-full text-[10px] text-gray-500 file:mr-2 file:py-1.5 file:px-2 file:border-0 file:text-[10px] file:font-bold file:bg-gray-100 file:text-blue-700 hover:file:bg-gray-200 cursor-pointer outline-none">
                    </div>
                    <p class="text-[9px] text-gray-400 mt-1 font-mono">Max 2MB. Kosongkan jk tdk ada.</p>
                </td>
            `;
            tableBodyTugas.appendChild(tr);

            // Trigger isi otomatis untuk Kode Tugas di setiap baris saat awal loading
            generateKodeRow(index);
        });
    }
</script>