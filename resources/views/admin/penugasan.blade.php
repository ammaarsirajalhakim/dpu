@extends('layout.layoutadmin')

@section('title', 'Daftar Penugasan')

@section('content')
<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .hud-panel {
        opacity: 0;
        animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    .delay-100 { animation-delay: 100ms; }
</style>

@if (session('success'))
    <div id="toast-success"
        class="fixed top-20 right-5 flex items-center w-full max-w-xs p-4 mb-4 text-white bg-gray-900 rounded-xl shadow-2xl border-l-4 border-green-500 z-50 transform transition-all duration-500 translate-x-0 opacity-100"
        role="alert">
        <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg">
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
            </svg>
            <span class="sr-only">Check icon</span>
        </div>
        <div class="ms-3 text-sm font-bold text-gray-100">{{ session('success') }}</div>
        <button type="button"
            class="ms-auto -mx-1.5 -my-1.5 bg-gray-900 text-gray-400 hover:text-white rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-800 inline-flex items-center justify-center h-8 w-8 transition-colors"
            aria-label="Close" onclick="closeToast()">
            <span class="sr-only">Close</span>
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
            </svg>
        </button>
    </div>

    <script>
        function closeToast() {
            const toast = document.getElementById('toast-success');
            if (toast) {
                toast.classList.remove('translate-x-0', 'opacity-100');
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 500);
            }
        }
        setTimeout(closeToast, 3500);
    </script>
@endif

<div class="container mx-auto px-4 py-6 font-sans">
    
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-end border-b border-gray-300 pb-4 hud-panel">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                <svg class="w-7 h-7 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-8v4h8v-4zm-4-8h4v4H8V8zM5 8H3m2 6H3"></path></svg>
                Direktori Penugasan User
            </h2>
            <p class="text-sm text-gray-500 mt-1">Kelola distribusi tugas dan pantau tenggat waktu pegawai.</p>
        </div>
        <div class="text-right mt-4 md:mt-0 bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-100 flex items-center">
            <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse mr-2"></div>
            <p id="liveTime" class="font-semibold text-gray-800 text-sm"></p>
        </div>
    </div>

    <div class="hud-panel delay-100 bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col overflow-hidden">
        
        <div class="bg-black px-5 py-4 flex flex-col xl:flex-row justify-between items-start xl:items-center space-y-4 xl:space-y-0 rounded-t-xl">
            
            <form action="{{ route('admin.penugasan.index') }}" method="GET" class="flex w-full xl:w-auto">
                <div class="relative w-full sm:w-72">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Kode atau Nama Tugas..." 
                        class="w-full bg-gray-900 border border-gray-700 text-gray-300 text-sm rounded-lg pl-10 pr-4 py-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors placeholder-gray-600 shadow-inner">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
            </form>

            <div class="flex flex-wrap w-full xl:w-auto items-center justify-start xl:justify-end gap-2">
                <a href="{{ route('admin.penugasan.template') }}" class="bg-gray-800 border border-emerald-500/50 text-emerald-400 px-3 py-2 rounded-lg hover:bg-gray-700 hover:text-emerald-300 text-xs font-bold flex items-center transition shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> Template
                </a>
                <a href="{{ route('admin.penugasan.export') }}" class="bg-gray-800 border border-amber-500/50 text-amber-400 px-3 py-2 rounded-lg hover:bg-gray-700 hover:text-amber-300 text-xs font-bold flex items-center transition shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> Export
                </a>
                <button type="button" onclick="openImportModal()" class="bg-gray-800 border border-yellow-500/50 text-yellow-400 px-3 py-2 rounded-lg hover:bg-gray-700 hover:text-yellow-300 text-xs font-bold flex items-center transition shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg> Import
                </button>
                <a href="{{ route('admin.penugasan.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-xs font-bold flex items-center transition shadow-sm transform hover:scale-105 ml-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" /></svg> Tugaskan
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="bg-gray-50 text-gray-600 text-[11px] uppercase font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Kode Tugas</th>
                        <th class="px-6 py-4">Nama Tugas</th>
                        <th class="px-6 py-4">Penerima</th>
                        <th class="px-6 py-4">Pemberi (Admin)</th>
                        <th class="px-6 py-4">Batas Lapor</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse ($penugasan as $p)
                        <tr class="hover:bg-blue-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-gray-100 border border-gray-200 text-blue-600 font-mono text-xs font-bold rounded shadow-sm">
                                    {{ $p->kodetugas }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium truncate max-w-xs text-sm text-gray-900">
                                {{ $p->tugas->nama_tugas ?? 'Tugas Tidak Ada' }}
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-gray-800">
                                @if ($p->anggota->count() > 0)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-gray-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        {{ $p->anggota->first()->user->name ?? 'Unknown' }}
                                        @if ($p->anggota->count() > 1)
                                            <span class="ml-2 px-1.5 py-0.5 bg-gray-200 text-gray-600 rounded-md font-mono text-[10px] shadow-inner">
                                                +{{ $p->anggota->count() - 1 }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">Belum Ada</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500 font-medium">
                                {{ $p->admin->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-800 font-semibold font-mono">
                                {{ \Carbon\Carbon::parse($p->batas_waktu_lapor)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $today = \Carbon\Carbon::today();
                                    $batas = \Carbon\Carbon::parse($p->batas_waktu_lapor);
                                @endphp
                                @if ($today->gt($batas))
                                    <span class="px-2 py-1 text-[10px] font-bold rounded bg-red-100 text-red-700 tracking-wider border border-red-200 shadow-sm">TERLEWAT</span>
                                @else
                                    <span class="px-2 py-1 text-[10px] font-bold rounded bg-blue-100 text-blue-700 tracking-wider border border-blue-200 shadow-sm">AKTIF</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center space-x-3 opacity-80 group-hover:opacity-100 transition-opacity">
                                    {{-- Detail --}}
                                    <a href="{{ route('admin.penugasan.show', $p->id) }}" class="p-1.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-md transition-colors" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('admin.penugasan.create', ['kodetugas' => $p->kodetugas]) }}" class="p-1.5 bg-yellow-50 text-yellow-600 hover:bg-yellow-400 hover:text-white rounded-md transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>

                                    {{-- Delete --}}
                                    <form action="{{ route('admin.penugasan.destroy', $p->id) }}" method="POST" class="inline-block" onsubmit="confirmDelete(event, this)">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-md transition-colors" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    <span class="text-sm font-semibold">Belum ada data penugasan yang ditugaskan kepada user.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(method_exists($penugasan, 'hasPages'))
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 rounded-b-xl">
                {{ $penugasan->links() }}
            </div>
        @else
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100 rounded-b-xl flex justify-between items-center text-[10px] font-bold text-gray-500 uppercase tracking-widest">
                <span>Total Distribusi Penugasan: {{ count($penugasan) }}</span>
                <span>>> Modul Penugasan Aktif</span>
            </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Live Waktu
    function updateClock() {
        const opt = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
        document.getElementById('liveTime').innerText = new Date().toLocaleDateString('id-ID', opt) + ' WIB';
    }
    setInterval(updateClock, 1000); updateClock();

    // Validasi Penghapusan SweetAlert2 (Desain Sci-fi/Modern)
    function confirmDelete(event, form) {
        event.preventDefault();
        Swal.fire({
            title: 'HAPUS PENUGASAN?',
            text: "Akses user terhadap tugas ini akan diputus dan laporan tidak dapat diterima.",
            icon: 'warning',
            background: '#ffffff',
            color: '#111827',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#4b5563',
            confirmButtonText: 'Ya, Cabut Tugas!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-xl border border-gray-200 shadow-2xl',
                title: 'font-black tracking-wide uppercase',
                confirmButton: 'rounded-lg px-5 py-2.5 text-sm font-bold shadow-md',
                cancelButton: 'rounded-lg px-5 py-2.5 text-sm font-bold'
            }
        }).then((result) => {
            if (result.isConfirmed) { form.submit(); }
        });
    }

    // Modal Import (Membuka view modal jika disertakan)
    function openImportModal() {
        if(document.getElementById('importModalPenugasan')) {
            document.getElementById('importModalPenugasan').classList.remove('hidden');
            document.getElementById('importModalPenugasan').classList.add('flex'); // Asumsi HUD butuh flex layout
        } else {
            Swal.fire({
                icon: 'error',
                title: 'MODAL TIDAK DITEMUKAN',
                text: 'Pastikan file importpenugasan.blade.php sudah di-include.',
                customClass: { popup: 'rounded-xl' }
            });
        }
    }
</script>

@include('admin.importpenugasan')

@endsection