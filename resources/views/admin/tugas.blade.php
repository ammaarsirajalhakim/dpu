@extends('layout.layoutadmin')

@section('title', 'Daftar Tugas')

@section('content')
    <!-- CSS Animasi & Scrollbar -->
    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hud-panel {
            opacity: 0;
            animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .delay-100 {
            animation-delay: 100ms;
        }

        .delay-200 {
            animation-delay: 200ms;
        }
    </style>

    <!-- TOAST ALERT -->
    @if (session('success'))
        <div id="toast-success"
            class="fixed top-20 right-5 flex items-center w-full max-w-xs p-4 mb-4 text-white bg-gray-900 rounded-xl shadow-2xl border-l-4 border-green-500 z-50 transform transition-all duration-500 translate-x-0 opacity-100"
            role="alert">
            <div
                class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg">
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                    viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                </svg>
                <span class="sr-only">Check icon</span>
            </div>
            <div class="ms-3 text-sm font-bold text-gray-100">{{ session('success') }}</div>
            <button type="button"
                class="ms-auto -mx-1.5 -my-1.5 bg-gray-900 text-gray-400 hover:text-white rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-800 inline-flex items-center justify-center h-8 w-8 transition-colors"
                aria-label="Close" onclick="closeToast()">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
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

        <!-- HEADER -->
        <div
            class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-end border-b border-gray-300 pb-4 hud-panel">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                    <svg class="w-7 h-7 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                        </path>
                    </svg>
                    Direktori Tugas
                </h2>
                <p class="text-sm text-gray-500 mt-1">Kelola data tugas dan jadwal operasional secara terpusat.</p>
            </div>
            <div
                class="text-right mt-4 md:mt-0 bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-100 flex items-center">
                <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse mr-2"></div>
                <p id="liveTime" class="font-semibold text-gray-800 text-sm"></p>
            </div>
        </div>

        <!-- PANEL UTAMA: KOTAK TABEL -->
        <div class="hud-panel delay-100 bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col overflow-hidden">

            <!-- HEADER PANEL HITAM (BARIS PENCARIAN & AKSI) -->
            <div
                class="bg-black px-5 py-4 flex flex-col xl:flex-row justify-between items-start xl:items-center space-y-4 xl:space-y-0 rounded-t-xl">

                <!-- FORM PENCARIAN & FILTER -->
                <form action="{{ route('admin.tugas.index') }}" method="GET"
                    class="flex w-full xl:w-auto flex-col sm:flex-row items-center space-y-3 sm:space-y-0 sm:space-x-3">
                    <div class="relative w-full sm:w-64">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari Kode / Nama..."
                            class="w-full bg-gray-900 border border-gray-700 text-gray-300 text-sm rounded-lg pl-10 pr-4 py-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors placeholder-gray-600">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <button type="button" onclick="openFilterModal()"
                        class="w-full sm:w-auto bg-gray-800 border border-gray-700 text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-700 hover:text-white flex items-center justify-center text-sm font-semibold transition">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                            </path>
                        </svg> Filter
                    </button>
                </form>

                <!-- KUMPULAN TOMBOL AKSI -->
                <div class="flex flex-wrap w-full xl:w-auto items-center justify-start xl:justify-end gap-2">
                    <a href="{{ route('admin.tugas.template') }}"
                        class="bg-gray-800 border border-gray-700 text-gray-300 px-3 py-2 rounded-lg hover:bg-gray-700 hover:text-white text-xs font-bold flex items-center transition shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg> Template
                    </a>
                    <a href="{{ route('admin.tugas.export') }}"
                        class="bg-gray-800 border border-gray-700 text-gray-300 px-3 py-2 rounded-lg hover:bg-gray-700 hover:text-white text-xs font-bold flex items-center transition shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg> Export
                    </a>
                    <button type="button" onclick="openImportModal()"
                        class="bg-gray-800 border border-yellow-500/50 text-yellow-400 px-3 py-2 rounded-lg hover:bg-gray-700 hover:text-yellow-300 text-xs font-bold flex items-center transition shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                            </path>
                        </svg> Import
                    </button>
                    <a href="{{ route('admin.tugas.create') }}"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-xs font-bold flex items-center transition shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                clip-rule="evenodd" />
                        </svg> Tugas Baru
                    </a>
                </div>
            </div>

            <!-- AREA TABEL -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead class="bg-gray-50 text-gray-600 text-[11px] uppercase font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Kode</th>
                            <th class="px-6 py-4">Nama Tugas</th>
                            <th class="px-6 py-4">Pembuat (Admin)</th>
                            <th class="px-6 py-4">Tanggal Mulai</th>
                            <th class="px-6 py-4">Batas Lapor</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse ($tugas as $t)
                            <tr class="hover:bg-blue-50/50 transition-colors group">
                                <!-- Kolom Kode -->
                                <td class="px-6 py-4 font-mono text-sm text-blue-600 font-bold">
                                    {{ $t->kodetugas }}
                                </td>
                                <!-- Kolom Nama Tugas -->
                                <td class="px-6 py-4 font-medium truncate max-w-xs text-sm text-gray-900">
                                    {{ $t->nama_tugas }}
                                </td>
                                <!-- Kolom Admin -->
                                <td class="px-6 py-4 text-xs text-gray-500 flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    {{ $t->admin->name ?? 'Admin' }}
                                </td>
                                <!-- Kolom Tanggal Mulai -->
                                <td class="px-6 py-4 text-xs text-gray-700 font-semibold">
                                    {{ \Carbon\Carbon::parse($t->tanggal_mulai)->format('d/m/Y') }}
                                </td>
                                <!-- Kolom Tanggal Selesai -->
                                <td class="px-6 py-4 text-xs text-gray-700 font-semibold">
                                    {{ \Carbon\Carbon::parse($t->tanggal_selesai)->format('d/m/Y') }}
                                </td>
                                <!-- Kolom Status Berdasarkan Tanggal -->
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 text-[10px] font-bold rounded tracking-wider shadow-sm {{ $t->status_penugasan_class }}">
                                        {{ strtoupper($t->status_penugasan_label) }}
                                    </span>
                                </td>
                                <!-- Kolom Aksi -->
                                <td class="px-6 py-4 text-center">
                                    <div
                                        class="flex justify-center space-x-3 opacity-80 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ route('admin.tugas.show', $t->kodetugas) }}"
                                            class="p-1.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-md transition-colors"
                                            title="Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.tugas.edit', $t->kodetugas) }}"
                                            class="p-1.5 bg-yellow-50 text-yellow-600 hover:bg-yellow-400 hover:text-white rounded-md transition-colors"
                                            title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.tugas.destroy', $t->kodetugas) }}" method="POST"
                                            class="inline-block" onsubmit="confirmDelete(event, this)">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-md transition-colors"
                                                title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                            </path>
                                        </svg>
                                        <span class="text-sm font-semibold">Tidak ada data tugas yang ditemukan.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- FOOTER PANEL Paginasi -->
            @if (method_exists($tugas, 'hasPages'))
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 rounded-b-xl">
                    {{ $tugas->links() }}
                </div>
            @else
                <div
                    class="bg-gray-50 px-6 py-3 border-t border-gray-100 rounded-b-xl flex justify-between items-center text-[10px] font-bold text-gray-500 uppercase tracking-widest">
                    <span>Total Record: {{ count($tugas) }}</span>
                    <span>>> Direktori Master Berjalan</span>
                </div>
            @endif
        </div>
    </div>

    <!-- ========================================================================================= -->
    <!-- MODAL FILTER (Sesuai Gaya HUD) -->
    <!-- ========================================================================================= -->
    <div id="filterModal"
        class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black/80 transition-opacity p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-black px-6 py-4 flex justify-between items-center">
                <h3 class="text-white font-bold flex items-center">
                    <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                        </path>
                    </svg>
                    Filter Data Tugas
                </h3>
                <button type="button" onclick="closeFilterModal()"
                    class="text-gray-400 hover:text-white transition focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.tugas.index') }}" method="GET" class="p-6">
                @if (request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wide">Bulan
                            Mulai</label>
                        <select name="bulan"
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                            <option value="">Semua Bulan</option>
                            @foreach (['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'] as $num => $name)
                                <option value="{{ $num }}" {{ request('bulan') == $num ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wide">Tahun
                            Mulai</label>
                        <select name="tahun"
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                            <option value="">Semua Tahun</option>
                            @for ($i = date('Y') + 1; $i >= date('Y') - 3; $i--)
                                <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>
                                    {{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wide">Status
                            Target</label>
                        <select name="status"
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif (Belum Lewat
                                Deadline)</option>
                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai /
                                Terlewat</option>
                        </select>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3 pt-5 border-t border-gray-100">
                    <a href="{{ route('admin.tugas.index') }}"
                        class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-bold">Reset</a>
                    <button type="submit"
                        class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-md transition text-sm font-bold">Terapkan
                        Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================================= -->
    <!-- JAVASCRIPT BANTUAN -->
    <!-- ========================================================================================= -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Live Waktu
        function updateClock() {
            const opt = {
                weekday: 'long',
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            document.getElementById('liveTime').innerText = new Date().toLocaleDateString('id-ID', opt) + ' WIB';
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Logika Modal Filter
        function openFilterModal() {
            document.getElementById('filterModal').classList.remove('hidden');
            document.getElementById('filterModal').classList.add('flex');
        }

        function closeFilterModal() {
            document.getElementById('filterModal').classList.add('hidden');
            document.getElementById('filterModal').classList.remove('flex');
        }
        // Menutup Modal dengan mengklik layar luar
        window.addEventListener('click', function(e) {
            let mod = document.getElementById('filterModal');
            if (e.target === mod) {
                closeFilterModal();
            }
        });

        // Validasi Penghapusan SweetAlert2 (Desain disesuaikan Sci-fi/Modern)
        function confirmDelete(event, form) {
            event.preventDefault();
            Swal.fire({
                title: 'HAPUS DATA TUGAS?',
                text: "Data tugas beserta seluruh penugasan dan laporan terkait akan dihapus secara permanen dari sistem.",
                icon: 'warning',
                background: '#ffffff',
                color: '#111827',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#4b5563',
                confirmButtonText: 'Ya, Eksekusi!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-xl border border-gray-200',
                    title: 'font-black tracking-wide',
                    confirmButton: 'rounded-lg px-5 py-2.5 text-sm font-bold shadow-md',
                    cancelButton: 'rounded-lg px-5 py-2.5 text-sm font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        // Modal Import (Membuka view modal jika disertakan)
        function openImportModal() {
            if (document.getElementById('importModalTugas')) {
                document.getElementById('importModalTugas').classList.remove('hidden');
            } else {
                console.warn('Komponen modal import tidak ditemukan.');
            }
        }
    </script>

    <!-- Mengambil tampilan pop-up import dari file blade terpisah -->
    @include('admin.importtugas')

@endsection
