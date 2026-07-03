@php
    $tugasDict = \App\Models\Tugas::all()->keyBy('kodetugas');
    // Hapus kondisi where('role', '!=', 'admin')
    $userDict = \App\Models\User::all()->keyBy('nip');
@endphp

<div id="importModalPenugasan" class="fixed inset-0 z-[70] hidden flex items-center justify-center bg-black/80 transition-opacity p-4 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-7xl max-h-[90vh] flex flex-col overflow-hidden hud-panel">

        <div class="bg-black px-6 py-4 flex justify-between items-center border-b-4 border-blue-600">
            <h3 class="text-white font-bold flex items-center tracking-wide uppercase text-sm">
                <svg class="w-5 h-5 text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Terminal Import Data Penugasan
            </h3>
            <button type="button" onclick="closeImportModal()" class="text-gray-400 hover:text-white transition focus:outline-none">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-6 overflow-y-auto flex-grow relative bg-gray-50/30">

            <div id="dragDropAreaPenugasan" class="border-2 border-dashed border-blue-400 bg-blue-50/50 rounded-xl p-12 text-center hover:bg-blue-100 hover:border-blue-500 transition cursor-pointer relative group">
                <input type="file" id="excelFilePenugasan" accept=".xlsx, .xls" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                <svg class="mx-auto h-16 w-16 text-blue-500 mb-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                <p class="text-gray-900 font-black text-lg uppercase tracking-wide">Klik Atau Tarik File Excel Ke Sini</p>
                <p class="text-sm text-gray-500 mt-2 font-mono">>_ FORMAT_DIIZINKAN : .XLSX, .XLS</p>
            </div>

            <form id="importFormPenugasan" action="{{ route('admin.penugasan.importProcess') }}" method="POST" class="hidden flex-col h-full">
                @csrf
                <div class="mb-4 flex flex-col md:flex-row justify-between items-start md:items-center bg-gray-900 p-4 rounded-xl border-l-4 border-yellow-400 shadow-sm gap-4">
                    <p class="text-sm text-gray-300 font-mono flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        >_ STATUS : PREVIEW_DATA. Pastikan data tugas dan penerima sudah sesuai sebelum di-import.
                    </p>
                    <button type="button" onclick="resetImportPenugasan()" class="text-xs bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-2 rounded-lg shadow transition uppercase tracking-widest flex items-center">
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
                                <th class="px-4 py-4 w-56">Penerima (NIP & Nama)</th>
                                <th class="px-4 py-4 w-40">Batas Waktu Lapor</th>
                                <th class="px-4 py-4 w-32 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="previewTableBodyPenugasan" class="divide-y divide-gray-100 text-gray-800">
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

<div id="detailPreviewModal" class="fixed inset-0 z-[80] hidden flex items-center justify-center bg-black/80 backdrop-blur-sm transition-opacity p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden hud-panel border border-gray-200">
        
        <div class="bg-black px-6 py-4 flex justify-between items-center border-b-4 border-blue-600">
            <h3 class="text-white font-bold flex items-center tracking-wide uppercase text-sm">
                <svg class="w-5 h-5 text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Detail Rencana Penugasan
            </h3>
            <button type="button" onclick="closeDetailModal()" class="text-gray-400 hover:text-white transition focus:outline-none">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <div class="p-6 bg-gray-50/30">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                    <h4 class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-3 flex items-center">
                       <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                       Informasi Tugas
                    </h4>
                    <div class="space-y-4 text-sm">
                        <div><span class="text-gray-400 block text-[10px] font-mono uppercase tracking-wider mb-1">>_ Kode Tugas</span><span id="dtlKode" class="font-mono font-bold text-gray-800 bg-gray-100 px-2 py-1 rounded"></span></div>
                        <div><span class="text-gray-400 block text-[10px] font-mono uppercase tracking-wider mb-1">>_ Nama Tugas</span><span id="dtlNamaTugas" class="font-bold text-gray-800"></span></div>
                        <div><span class="text-gray-400 block text-[10px] font-mono uppercase tracking-wider mb-1">>_ Keterangan / Deskripsi</span>
                            <p id="dtlDeskripsi" class="text-gray-600 mt-1 bg-gray-50 p-2 rounded border border-gray-100 text-[11px] leading-relaxed"></p>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div><span class="text-gray-400 block text-[10px] font-mono uppercase tracking-wider mb-1">>_ Tgl Mulai</span><span id="dtlTglMulai" class="text-gray-700 font-mono text-xs"></span></div>
                            <div><span class="text-gray-400 block text-[10px] font-mono uppercase tracking-wider mb-1">>_ Tgl Selesai</span><span id="dtlTglSelesai" class="text-gray-700 font-mono text-xs"></span></div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                    <h4 class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-3 flex items-center">
                       <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                       Informasi Penerima & Batas
                    </h4>
                    <div class="space-y-4 text-sm">
                        <div><span class="text-gray-400 block text-[10px] font-mono uppercase tracking-wider mb-1">>_ NIP Penerima</span><span id="dtlUserId" class="font-mono text-gray-800 whitespace-pre-wrap bg-gray-100 px-2 py-1 rounded"></span></div>
                        <div><span class="text-gray-400 block text-[10px] font-mono uppercase tracking-wider mb-1">>_ Nama Lengkap</span><span id="dtlUserName" class="font-bold text-gray-800 whitespace-pre-wrap"></span></div>
                        <div><span class="text-gray-400 block text-[10px] font-mono uppercase tracking-wider mb-1">>_ Role</span><span id="dtlUserRole" class="inline-block bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider"></span></div>
                        
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <span class="text-red-400 block text-[10px] font-mono uppercase tracking-wider mb-2">>_ Batas Waktu Lapor</span>
                            <span id="dtlBatasWaktu" class="text-red-700 font-bold text-sm bg-red-50 px-3 py-1.5 rounded inline-block border border-red-100 font-mono"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="px-6 py-4 bg-gray-100 border-t border-gray-200 text-right">
            <button type="button" onclick="closeDetailModal()" class="px-6 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 shadow transition font-bold uppercase tracking-widest text-xs">
                Tutup Preview
            </button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    const dictTugas = @json($tugasDict);
    const dictUser = @json($userDict);
    let previewRowData = [];

    const modalPenugasan = document.getElementById('importModalPenugasan');
    const dragDropArea = document.getElementById('dragDropAreaPenugasan');
    const importForm = document.getElementById('importFormPenugasan');
    const fileInput = document.getElementById('excelFilePenugasan');
    const tableBody = document.getElementById('previewTableBodyPenugasan');
    const detailModal = document.getElementById('detailPreviewModal');

    function openImportModal() {
        modalPenugasan.classList.remove('hidden');
        modalPenugasan.classList.add('flex');
        resetImportPenugasan();
    }

    function closeImportModal() {
        modalPenugasan.classList.add('hidden');
        modalPenugasan.classList.remove('flex');
    }

    function resetImportPenugasan() {
        fileInput.value = '';
        dragDropArea.classList.remove('hidden');
        importForm.classList.add('hidden');
        importForm.classList.remove('flex');
        tableBody.innerHTML = '';
        previewRowData = [];
    }

    // Drag & Drop Handlers
    dragDropArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        dragDropArea.classList.add('bg-blue-100', 'border-blue-600');
    });
    dragDropArea.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dragDropArea.classList.remove('bg-blue-100', 'border-blue-600');
    });
    dragDropArea.addEventListener('drop', (e) => {
        e.preventDefault();
        dragDropArea.classList.remove('bg-blue-100', 'border-blue-600');
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            processExcel(e.dataTransfer.files[0]);
        }
    });
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length) {
            processExcel(e.target.files[0]);
        }
    });

    // Proses File Excel
    function processExcel(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, {
                type: 'array',
                cellDates: true
            });
            const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
            const json = XLSX.utils.sheet_to_json(firstSheet, {
                raw: false,
                dateNF: 'yyyy-mm-dd'
            });
            renderPreview(json);
        };
        reader.readAsArrayBuffer(file);
    }

    // Render Tabel Preview
    function renderPreview(data) {
        dragDropArea.classList.add('hidden');
        importForm.classList.remove('hidden');
        importForm.classList.add('flex');
        tableBody.innerHTML = '';
        previewRowData = [];

        data.forEach((row, index) => {
            const kodeTugas = row['Kode Tugas'] || '';
            const nipAnggota = row['NIP Anggota (Pisahkan dengan koma)'] || row['NIP Anggota'] || '';
            const batasWaktu = row['Batas Waktu Lapor (YYYY-MM-DD)'] || row['Batas Waktu Lapor'] || row['Batas Waktu'] || '';

            const dtTugas = dictTugas[kodeTugas];
            const namaTugas = dtTugas ? dtTugas.nama_tugas : '<span class="text-red-500 font-bold">Kode Tugas Tidak Valid</span>';

            let arrayNip = nipAnggota ? String(nipAnggota).split(',').map(n => n.trim()) : [];
            let namaUserList = [];

            arrayNip.forEach(nip => {
                if (dictUser[nip]) {
                    namaUserList.push(dictUser[nip].name);
                } else {
                    namaUserList.push(`<span class="text-red-500 font-bold">NIP ${nip} Invalid</span>`);
                }
            });
            const namaUser = namaUserList.length > 0 ? namaUserList.join('<br>') : '<span class="text-red-500 font-bold">NIP Kosong</span>';

            previewRowData[index] = {
                kode: kodeTugas,
                id_user: arrayNip.join(',\n'),
                batas: batasWaktu,
                tugas: dtTugas || null,
                user: arrayNip.length === 1 && dictUser[arrayNip[0]] ? dictUser[arrayNip[0]] : {
                    name: namaUserList.join(',\n').replace(/<br>/g, ',\n'),
                    role: 'Multiple User'
                }
            };

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-blue-50/50 transition-colors group';
            tr.innerHTML = `
                <td class="px-4 py-3 align-top">
                    <input type="text" name="penugasan[${index}][kodetugas]" value="${kodeTugas}" readonly class="w-full text-xs bg-gray-50 border border-gray-200 rounded px-2 py-1.5 font-mono text-blue-700 font-bold focus:outline-none uppercase shadow-inner">
                </td>
                <td class="px-4 py-3 text-[11px] font-semibold text-gray-900 align-top">${namaTugas}</td>
                <td class="px-4 py-3 align-top">
                    <div class="flex flex-col space-y-1">
                        <input type="text" name="penugasan[${index}][nip_anggota]" value="${nipAnggota}" readonly class="w-full text-xs bg-gray-50 border border-gray-200 rounded px-2 py-1.5 focus:outline-none shadow-inner text-gray-700">
                        <span class="text-[11px] text-gray-700 font-medium whitespace-normal mt-1 leading-relaxed">${namaUser}</span>
                    </div>
                </td>
                <td class="px-4 py-3 align-top">
                    <input type="date" name="penugasan[${index}][batas_waktu_lapor]" value="${batasWaktu}" class="w-36 text-[11px] bg-gray-50 border border-gray-200 rounded px-2 py-1.5 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none text-gray-700 font-mono shadow-inner">
                </td>
                <td class="px-4 py-3 text-center align-top">
                    <button type="button" onclick="openDetailModal(${index})" class="text-[10px] bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1.5 rounded font-bold transition inline-flex items-center tracking-wide uppercase">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Detail
                    </button>
                </td>
            `;
            tableBody.appendChild(tr);
        });
    }

    // Modal Popup Detail Functions
    function openDetailModal(index) {
        const data = previewRowData[index];

        document.getElementById('dtlKode').textContent = data.kode;
        document.getElementById('dtlNamaTugas').innerHTML = data.tugas ? data.tugas.nama_tugas : '<span class="text-red-500">Tidak Valid</span>';
        document.getElementById('dtlDeskripsi').textContent = data.tugas ? data.tugas.deskripsi : '-';
        document.getElementById('dtlTglMulai').textContent = data.tugas ? (data.tugas.tanggal_mulai || '-') : '-';
        document.getElementById('dtlTglSelesai').textContent = data.tugas ? (data.tugas.tanggal_selesai || '-') : '-';

        document.getElementById('dtlUserId').textContent = data.id_user;
        document.getElementById('dtlUserName').innerHTML = data.user ? data.user.name : '<span class="text-red-500">Tidak Ditemukan</span>';
        document.getElementById('dtlUserRole').textContent = data.user ? data.user.role : '-';

        document.getElementById('dtlBatasWaktu').textContent = data.batas || '-';

        detailModal.classList.remove('hidden');
        detailModal.classList.add('flex');
    }

    function closeDetailModal() {
        detailModal.classList.add('hidden');
        detailModal.classList.remove('flex');
    }
</script>