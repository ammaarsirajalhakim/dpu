@extends('layouts.admin')

@section('content')
    <div class="min-h-screen bg-slate-50 px-4 py-6 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl space-y-6">

            {{-- Header --}}
            <div class="flex flex-col gap-4 rounded-2xl bg-white p-6 shadow-sm border border-slate-100 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Data Tugas</h1>
                    <p class="mt-1 text-sm text-slate-500">
                        Kelola data tugas, periode pelaksanaan, lampiran, dan status penugasan.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    @if (Route::has('admin.tugas.template'))
                        <a href="{{ route('admin.tugas.template') }}"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                            Template Import
                        </a>
                    @endif

                    @if (Route::has('admin.tugas.export'))
                        <a href="{{ route('admin.tugas.export') }}"
                            class="inline-flex items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 shadow-sm transition hover:bg-emerald-100">
                            Export Excel
                        </a>
                    @endif

                    <a href="{{ route('admin.tugas.create') }}"
                        class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                        Tambah Tugas
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
                $totalTugas = $tugas->count();
                $totalSelesai = $tugas->filter(fn ($item) => $item->status_penugasan_label === 'Selesai')->count();
                $totalAktif = $tugas->filter(fn ($item) => str_starts_with($item->status_penugasan_label, 'Aktif'))->count();
                $totalTerlewat = $tugas->filter(fn ($item) => str_starts_with($item->status_penugasan_label, 'Terlewat'))->count();
            @endphp

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl bg-white p-5 shadow-sm border border-slate-100">
                    <p class="text-sm font-medium text-slate-500">Total Tugas</p>
                    <p class="mt-2 text-3xl font-bold text-slate-800">{{ $totalTugas }}</p>
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
                <form action="{{ route('admin.tugas.index') }}" method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-5">
                    <div class="md:col-span-2">
                        <label for="search" class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">
                            Pencarian
                        </label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Cari kode atau nama tugas..."
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    </div>

                    <div>
                        <label for="bulan" class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">
                            Bulan
                        </label>
                        <select name="bulan" id="bulan"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                            <option value="">Semua Bulan</option>
                            @foreach (range(1, 12) as $bulan)
                                <option value="{{ $bulan }}" {{ request('bulan') == $bulan ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="tahun" class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">
                            Tahun
                        </label>
                        <select name="tahun" id="tahun"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                            <option value="">Semua Tahun</option>
                            @for ($tahun = now()->year + 1; $tahun >= now()->year - 5; $tahun--)
                                <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>
                                    {{ $tahun }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label for="status" class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">
                            Status
                        </label>
                        <select name="status" id="status"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                            <option value="">Semua Status</option>
                            <option value="akan_datang" {{ request('status') == 'akan_datang' ? 'selected' : '' }}>
                                Akan Datang
                            </option>
                            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>
                                Aktif
                            </option>
                            <option value="terlewat" {{ request('status') == 'terlewat' ? 'selected' : '' }}>
                                Terlewat
                            </option>
                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>
                                Selesai
                            </option>
                        </select>
                    </div>

                    <div class="flex gap-2 md:col-span-5">
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-slate-800 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-900">
                            Terapkan Filter
                        </button>

                        <a href="{{ route('admin.tugas.index') }}"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
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
                                    Kode
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Nama Tugas
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Periode
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Admin
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Lampiran
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
                            @forelse ($tugas as $index => $t)
                                <tr class="transition hover:bg-slate-50">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                                        {{ $index + 1 }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-700">
                                            {{ $t->kodetugas }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="max-w-md">
                                            <p class="text-sm font-bold text-slate-800">
                                                {{ $t->nama_tugas }}
                                            </p>
                                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                                {{ \Illuminate\Support\Str::limit($t->deskripsi, 90) }}
                                            </p>
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                                        <div class="space-y-1">
                                            <p>
                                                <span class="font-semibold text-slate-700">Mulai:</span>
                                                {{ $t->tanggal_mulai ? \Carbon\Carbon::parse($t->tanggal_mulai)->format('d/m/Y') : '-' }}
                                            </p>
                                            <p>
                                                <span class="font-semibold text-slate-700">Selesai:</span>
                                                {{ $t->tanggal_selesai ? \Carbon\Carbon::parse($t->tanggal_selesai)->format('d/m/Y') : '-' }}
                                            </p>
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                                        {{ $t->admin->name ?? 'Admin' }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                                        @if ($t->lampiran)
                                            <a href="{{ asset('storage/' . $t->lampiran) }}" target="_blank"
                                                class="inline-flex items-center rounded-lg border border-blue-200 bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-100">
                                                Lihat Lampiran
                                            </a>
                                        @else
                                            <span class="text-xs text-slate-400">Tidak ada</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center rounded px-2 py-1 text-[10px] font-bold tracking-wider shadow-sm {{ $t->status_penugasan_class }}">
                                            {{ strtoupper($t->status_penugasan_label) }}
                                        </span>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                        <div class="flex justify-end gap-2">
                                            @if (Route::has('admin.tugas.show'))
                                                <a href="{{ route('admin.tugas.show', $t->kodetugas) }}"
                                                    class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                                                    Detail
                                                </a>
                                            @endif

                                            <a href="{{ route('admin.tugas.edit', $t->kodetugas) }}"
                                                class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 shadow-sm hover:bg-amber-100">
                                                Edit
                                            </a>

                                            <form action="{{ route('admin.tugas.destroy', $t->kodetugas) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus tugas {{ $t->nama_tugas }}? Data yang dihapus tidak dapat dikembalikan.')">
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
                                            <p class="text-sm font-semibold text-slate-700">Data tugas belum tersedia.</p>
                                            <p class="mt-1 text-sm text-slate-500">
                                                Tambahkan tugas baru atau ubah filter pencarian.
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
                <h2 class="text-sm font-bold text-slate-800">Keterangan Status</h2>
                <div class="mt-3 grid grid-cols-1 gap-3 text-xs text-slate-600 md:grid-cols-2">
                    <p>
                        <span class="font-bold text-gray-700">Akan Datang</span>
                        berarti tanggal mulai tugas belum berjalan.
                    </p>
                    <p>
                        <span class="font-bold text-blue-700">Aktif</span>
                        berarti hari ini berada di antara tanggal mulai dan tanggal selesai.
                    </p>
                    <p>
                        <span class="font-bold text-red-700">Terlewat</span>
                        berarti tanggal selesai sudah lewat dan laporan akhir belum disetujui.
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