@extends('layout.layoutadmin')

@section('content')
    <div class="mb-6">
        <h2 id="pageTitle" class="text-2xl font-bold text-gray-800">Tambah Penugasan Baru</h2>
        <p id="pageSubtitle" class="text-gray-600 text-sm mt-1">Ikuti langkah-langkah di bawah ini untuk mendelegasikan tugas.</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 max-w-4xl">
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-md">
                <ul class="list-disc pl-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex items-center mb-8 border-b pb-4">
            <div class="flex items-center text-blue-600" id="indicator-step-1">
                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 font-bold">1</div>
                <span class="ml-2 font-medium">Informasi Tugas</span>
            </div>
            <div class="w-12 h-0.5 bg-gray-200 mx-4"></div>
            <div class="flex items-center text-gray-400" id="indicator-step-2">
                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 font-bold">2</div>
                <span class="ml-2 font-medium">Pengaturan & Anggota</span>
            </div>
        </div>

        <form action="{{ route('admin.penugasan.store') }}" method="POST" id="penugasanForm">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            
            <div id="step-1" class="block">
                <div class="mb-6 max-w-xl">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Tugas <span class="text-red-500">*</span></label>
                    <select name="kodetugas" id="kodetugas" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white">
                        <option value="" disabled selected>-- Pilih Tugas yang Akan Didelegasikan --</option>
                        @foreach($tugas as $t)
                            <option value="{{ $t->kodetugas }}">{{ $t->kodetugas }} - {{ $t->nama_tugas }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="border-t border-gray-200 pt-6 flex justify-end space-x-3">
                    <a href="{{ route('admin.penugasan.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">Batal</a>
                    <button type="button" onclick="goToStep(2)" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm flex items-center shadow-sm">
                        Selanjutnya 
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
            </div>

            <div id="step-2" class="hidden">
                <div class="mb-6 bg-blue-50/50 p-4 rounded-lg border border-blue-100">
                    <label class="block text-sm font-medium text-gray-800 mb-2">Batas Waktu Lapor <span class="text-red-500">*</span></label>
                    <input type="date" name="batas_waktu_lapor" id="batas_waktu_lapor" required class="w-full md:w-1/2 px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
                    <div class="border-r md:pr-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cari & Tambah Anggota</label>
                        <input type="text" id="searchInput" onkeyup="filterUsers()" placeholder="Ketik nama user..." class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-4">
                        <div class="bg-gray-50 border border-gray-200 rounded-lg h-64 overflow-y-auto">
                            <ul id="userList" class="divide-y divide-gray-200"></ul>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Daftar Anggota Terpilih <span class="text-red-500">*</span></label>
                        <div id="selectedMembersContainer" class="space-y-3">
                            <div id="emptyState" class="text-center py-8 bg-gray-50 border border-dashed border-gray-300 rounded-lg text-gray-400 text-sm">
                                Belum ada anggota yang dipilih.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-6 flex justify-between items-center">
                    <button type="button" onclick="handleKembali()" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        Kembali
                    </button>
                    <button type="button" onclick="submitForm()" class="px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium text-sm shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
    const users = @json($users);
    let selectedUsers = [];

    document.addEventListener("DOMContentLoaded", () => {
        renderUserList(users);
        
        const urlParams = new URLSearchParams(window.location.search);
        const kodetugasParam = urlParams.get('kodetugas');
        if (kodetugasParam) {
            const selectTugas = document.getElementById('kodetugas');
            selectTugas.value = kodetugasParam;
            if (selectTugas.value) {
                goToStep(2);
            }
        }
    });

    function handleKembali() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('kodetugas')) {
            window.location.href = "{{ route('admin.penugasan.index') }}";
        } else {
            goToStep(1);
        }
    }

    async function goToStep(step) {
        const kodetugas = document.getElementById('kodetugas').value;
        if (step === 2) {
            if (!kodetugas) { alert("Pilih tugas terlebih dahulu!"); return; }

            const btnNext = document.querySelector('button[onclick="goToStep(2)"]');
            const originalText = btnNext.innerHTML;
            btnNext.innerHTML = 'Memuat...';
            btnNext.disabled = true;

            try {
                const response = await fetch(`/admin/penugasan/check-existing/${kodetugas}`);
                const result = await response.json();

                const form = document.getElementById('penugasanForm');
                const methodInput = document.getElementById('formMethod');
                const batasWaktuInput = document.getElementById('batas_waktu_lapor');
                
                const pageTitle = document.getElementById('pageTitle');
                const pageSubtitle = document.getElementById('pageSubtitle');

                if (result.exists) {
                    form.action = `/admin/penugasan/${result.data.id}`;
                    methodInput.value = 'PUT';
                    batasWaktuInput.value = result.data.batas_waktu_lapor;
                    // UBAH: Gunakan nip, mapping id_user (yang sekarang berisi NIP) ke nip
                    selectedUsers = result.data.anggota.map(a => ({
                        nip: a.id_user, 
                        name: a.user ? a.user.name : 'Unknown'
                    }));
                    
                    pageTitle.innerText = 'Edit Penugasan';
                    pageSubtitle.innerText = 'Perbarui data batas waktu lapor dan anggota untuk tugas ini.';
                } else {
                    form.action = `{{ route('admin.penugasan.store') }}`;
                    methodInput.value = 'POST';
                    batasWaktuInput.value = '';
                    selectedUsers = [];
                    
                    pageTitle.innerText = 'Tambah Penugasan Baru';
                    pageSubtitle.innerText = 'Ikuti langkah-langkah di bawah ini untuk mendelegasikan tugas.';
                }
                renderSelectedMembers();
                filterUsers();
            } catch (error) {
                console.error(error);
                alert("Terjadi kesalahan saat memuat data penugasan.");
            }
            
            btnNext.innerHTML = originalText;
            btnNext.disabled = false;

            document.getElementById('step-1').classList.add('hidden');
            document.getElementById('step-2').classList.remove('hidden');
            document.getElementById('indicator-step-1').classList.replace('text-blue-600', 'text-gray-400');
            document.getElementById('indicator-step-1').querySelector('div').classList.replace('bg-blue-100', 'bg-gray-100');
            document.getElementById('indicator-step-2').classList.replace('text-gray-400', 'text-blue-600');
            document.getElementById('indicator-step-2').querySelector('div').classList.replace('bg-gray-100', 'bg-blue-100');
        } else {
            document.getElementById('step-1').classList.remove('hidden');
            document.getElementById('step-2').classList.add('hidden');
            document.getElementById('indicator-step-1').classList.replace('text-gray-400', 'text-blue-600');
            document.getElementById('indicator-step-1').querySelector('div').classList.replace('bg-gray-100', 'bg-blue-100');
            document.getElementById('indicator-step-2').classList.replace('text-blue-600', 'text-gray-400');
            document.getElementById('indicator-step-2').querySelector('div').classList.replace('bg-blue-100', 'bg-gray-100');
        }
    }

    function filterUsers() {
        const query = document.getElementById('searchInput').value.toLowerCase();
        const filtered = users.filter(u => u.name.toLowerCase().includes(query));
        renderUserList(filtered);
    }

    function renderUserList(arr) {
        const list = document.getElementById('userList');
        list.innerHTML = '';
        
        if (arr.length === 0) {
            list.innerHTML = '<li class="p-3 text-sm text-gray-500 text-center">Data user tidak ditemukan.</li>';
            return;
        }
        
        arr.forEach(u => {
            // UBAH: Cek menggunakan nip
            const isSelected = selectedUsers.some(su => su.nip === u.nip);
            const li = document.createElement('li');
            li.className = "flex items-center justify-between p-3 hover:bg-gray-100";
            
            // UBAH: Kirim nip ke addMember dan tambahkan tanda kutip ('') agar nip tidak ter-convert menjadi format int limit js
            li.innerHTML = `
                <div><p class="text-sm font-medium">${u.name}</p><p class="text-xs text-gray-500">${u.nip}</p></div>
                <button type="button" onclick="addMember('${u.nip}', '${u.name.replace(/'/g, "\\'")}')" 
                    class="p-1.5 rounded-full ${isSelected ? 'bg-gray-200 text-gray-400' : 'bg-blue-100 text-blue-600'}" ${isSelected ? 'disabled' : ''}>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </button>`;
            list.appendChild(li);
        });
    }

    // UBAH: Parameter menggunakan nip
    function addMember(nip, name) {
        if (selectedUsers.some(user => user.nip === nip)) return;
        selectedUsers.push({ nip, name });
        renderSelectedMembers();
        filterUsers();
    }

    // UBAH: Parameter menggunakan nip
    function removeMember(nip) {
        selectedUsers = selectedUsers.filter(u => u.nip !== nip);
        renderSelectedMembers();
        filterUsers();
    }

    function renderSelectedMembers() {
        const container = document.getElementById('selectedMembersContainer');
        const empty = document.getElementById('emptyState');
        
        Array.from(container.children).forEach(c => { if(c.id !== 'emptyState') c.remove(); });
        
        if (selectedUsers.length === 0) { 
            empty.style.display = 'block'; 
            return; 
        }
        
        empty.style.display = 'none';

        selectedUsers.forEach((u, i) => {
            const div = document.createElement('div');
            div.className = "flex items-center justify-between p-3 border rounded-lg bg-white mb-2 shadow-sm";
            
            // UBAH: input value menjadi u.nip, parameter hapus menjadi u.nip (pakai kutip string)
            div.innerHTML = `
                <div class="flex-1 mr-4">
                    <p class="text-sm font-medium mb-1">${u.name}</p>
                    <input type="hidden" name="anggota[${i}][id_user]" value="${u.nip}">
                </div>
                <button type="button" onclick="removeMember('${u.nip}')" class="text-red-500 hover:text-red-700 bg-red-50 p-2 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>`;
            container.appendChild(div);
        });
    }

    function submitForm() {
        const batasWaktu = document.getElementById('batas_waktu_lapor').value;
        if (!batasWaktu) {
            alert('Mohon tentukan Batas Waktu Lapor terlebih dahulu!');
            document.getElementById('batas_waktu_lapor').focus();
            return;
        }
        if (selectedUsers.length === 0) {
            alert('Anda belum menambahkan anggota satupun!');
            return;
        }
        const form = document.getElementById('penugasanForm');
        if (form.reportValidity()) form.submit();
    }
</script>
@endsection