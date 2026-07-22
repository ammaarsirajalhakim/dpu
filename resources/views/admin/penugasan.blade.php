@extends('layouts.admin')

@section('content')
    <div class="min-h-screen bg-slate-50 px-4 py-6 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl space-y-6">

            {{-- Header --}}
            <div class="flex flex-col gap-4 rounded-2xl bg-white p-6 shadow-sm border border-slate-100 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Data Penugasan</h1>
                    <p class="mt-1 text-sm text-slate-500">
                        Kelola pembagian tugas kepada pegawai, batas waktu laporan, anggota, dan status penyelesaian.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    @if (Route::has('admin.penugasan.template'))
                        <a href="{{ route('admin.penugasan.template') }}"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                            Template Import
                        </a>
                    @endif

                    @if (Route::has('admin.penugasan.export'))
                        <a href="{{ route('admin.penugasan.export') }}"
                            class="inline-flex items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 shadow-sm transition hover:bg-emerald-100">
                            Export Excel
                        </a>
                    @endif

                    <a href="{{ route('admin.penugasan.create') }}"
                        class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                        Tambah Penugasan
                    </a>
                </div>
            </div>

            {{-- Alert --}}
            @if (session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <p class="font-semibold">Terdapat kesalahan input:</p>
                    <ul class="mt-2 list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Statistik Ringkas --}}
            @php
                $totalPenugasan = $penugasan->count();
                $totalSelesai = $penugasan->filter(fn ($item) => $item->status_penugasan_label === 'Selesai')->count();
                $totalAktif = $penugasan->filter(fn ($item) => $item->status_penugasan_label === 'Aktif')->count();
                $totalTerlewat = $penugasan->filter(fn ($item) => str_starts_with($item->status_penugasan_label, 'Terlewat'))->count();
            @endphp

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl bg-white p-5 shadow-sm border border-slate-100">
                    <p class="text-sm font-medium text-slate-500">Total Penugasan</p>
                    <p class="mt-2 text-3xl font-bold text-slate-800">{{ $totalPenugasan }}</p>
                </div>

                <div class="rounded-2xl bg-white p-5 shadow-sm border border-slate-100">
                    <p class="text-sm font-medium text-slate-500">Aktif</p>
                    <p class="mt-2 text-3xl font-bold text-blue-600">{{ $totalAktif }}</p>
                </div>

                <div class="rounded-2xl bg-white p-5 shadow-sm border border-slate-100">
                    <p class="text-sm font-medium text-slate-500">Selesai</p>
                    <p class="mt-2 text-3xl font-bold text-emerald-600">{{ $totalSelesai }}</p>
                </div>

                <div class="rounded-2xl bg-white p-5 shadow-sm border border-slate-100">
                    <p class="text-sm font-medium text-slate-500">Terlewat</p>
                    <p class="mt-2 text-3xl font-bold text-red-600">{{ $totalTerlewat }}</p>
                </div>
            </div>

            {{-- Filter --}}
            <div class="rounded-2xl bg-white p-5 shadow-sm border border-slate-100">
                <form action="{{ route('admin.penugasan.index') }}" method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div class="md:col-span-3">
                        <label for="search" class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">
                            Pencarian
                        </label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Cari kode tugas, nama tugas, nama pegawai, NIP, atau email..."
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit"
                            class="w-full rounded-xl bg-slate-800 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-900">
                            Cari
                        </button>

                        <a href="{{ route('admin.penugasan.index') }}"
                            class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Tabel --}}
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-slate-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                    No
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Tugas
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Anggota
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Periode Tugas
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Batas Lapor
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Admin
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Status
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($penugasan as $index => $p)
                                <tr class="transition hover:bg-slate-50">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                                        {{ $index + 1 }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="max-w-md">
                                            <p class="text-sm font-bold text-slate-800">
                                                {{ $p->tugas->nama_tugas ?? 'Tugas tidak ditemukan' }}
                                            </p>
                                            <p class="mt-1">
                                                <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-700">
                                                    {{ $p->kodetugas }}
                                                </span>
                                            </p>
                                            @if ($p->tugas && $p->tugas->deskripsi)
                                                <p class="mt-2 text-xs leading-5 text-slate-500">
                                                    {{ \Illuminate\Support\Str::limit($p->tugas->deskripsi, 80) }}
                                                </p>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex max-w-sm flex-wrap gap-2">
                                            @forelse ($p->anggota as $anggota)
                                                <span class="inline-flex items-center rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 border border-blue-100">
                                                    {{ $anggota->user->name ?? $anggota->id_user }}
                                                </span>
                                            @empty
                                                <span class="text-xs text-slate-400">Belum ada anggota</span>
                                            @endforelse
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                                        <div class="space-y-1">
                                            <p>
                                                <span class="font-semibold text-slate-700">Mulai:</span>
                                                {{ $p->tugas && $p->tugas->tanggal_mulai ? \Carbon\Carbon::parse($p->tugas->tanggal_mulai)->format('d/m/Y') : '-' }}
                                            </p>
                                            <p>
                                                <span class="font-semibold text-slate-700">Selesai:</span>
                                                {{ $p->tugas && $p->tugas->tanggal_selesai ? \Carbon\Carbon::parse($p->tugas->tanggal_selesai)->format('d/m/Y') : '-' }}
                                            </p>
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                                        {{ $p->batas_waktu_lapor ? \Carbon\Carbon::parse($p->batas_waktu_lapor)->format('d/m/Y') : '-' }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                                        {{ $p->admin->name ?? $p->id_admin }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="space-y-2">
                                            <span class="inline-flex items-center rounded px-2 py-1 text-[10px] font-bold tracking-wider shadow-sm {{ $p->status_penugasan_class }}">
                                                {{ strtoupper($p->status_penugasan_label) }}
                                            </span>

                                            @if ($p->laporan)
                                                <div>
                                                    @if ($p->laporan->status === 'disetujui')
                                                        <span class="inline-flex items-center rounded bg-emerald-50 px-2 py-1 text-[10px] font-bold text-emerald-700 border border-emerald-100">
                                                            LAPORAN DISETUJUI
                                                        </span>
                                                    @elseif ($p->laporan->status === 'diajukan')
                                                        <span class="inline-flex items-center rounded bg-yellow-50 px-2 py-1 text-[10px] font-bold text-yellow-700 border border-yellow-100">
                                                            LAPORAN DIAJUKAN
                                                        </span>
                                                    @elseif ($p->laporan->status === 'revisi')
                                                        <span class="inline-flex items-center rounded bg-orange-50 px-2 py-1 text-[10px] font-bold text-orange-700 border border-orange-100">
                                                            PERLU REVISI
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center rounded bg-slate-50 px-2 py-1 text-[10px] font-bold text-slate-600 border border-slate-100">
                                                            {{ strtoupper($p->laporan->status) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="block text-[10px] font-semibold text-slate-400">
                                                    BELUM ADA LAPORAN AKHIR
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                        <div class="flex justify-end gap-2">
                                            @if (Route::has('admin.penugasan.show'))
                                                <a href="{{ route('admin.penugasan.show', $p->id) }}"
                                                    class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                                                    Detail
                                                </a>
                                            @endif

                                            <a href="{{ route('admin.penugasan.edit', $p->id) }}"
                                                class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 shadow-sm hover:bg-amber-100">
                                                Edit
                                            </a>

                                            <form action="{{ route('admin.penugasan.destroy', $p->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus penugasan untuk tugas {{ $p->tugas->nama_tugas ?? $p->kodetugas }}? Data yang dihapus tidak dapat dikembalikan.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 shadow-sm hover:bg-red-100">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="mx-auto max-w-md">
                                            <p class="text-sm font-semibold text-slate-700">Data penugasan belum tersedia.</p>
                                            <p class="mt-1 text-sm text-slate-500">
                                                Tambahkan penugasan baru atau ubah pencarian.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Keterangan Status --}}
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-bold text-slate-800">Keterangan Status Penugasan</h2>
                <div class="mt-3 grid grid-cols-1 gap-3 text-xs text-slate-600 md:grid-cols-2">
                    <p>
                        <span class="font-bold text-gray-700">Akan Datang</span>
                        berarti tanggal mulai tugas belum berjalan.
                    </p>
                    <p>
                        <span class="font-bold text-blue-700">Aktif</span>
                        berarti hari ini berada di antara tanggal mulai dan tanggal selesai tugas.
                    </p>
                    <p>
                        <span class="font-bold text-red-700">Terlewat: Belum Lapor</span>
                        berarti tanggal selesai tugas sudah lewat dan laporan akhir belum disetujui.
                    </p>
                    <p>
                        <span class="font-bold text-emerald-700">Selesai</span>
                        hanya muncul jika laporan akhir sudah disetujui admin.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection