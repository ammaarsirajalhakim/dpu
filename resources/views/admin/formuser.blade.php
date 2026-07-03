@extends('layout.layoutadmin')

@section('content')
@php
    $isEdit = isset($user);
    $selectedRole = old('role', $isEdit ? $user->role : '');
@endphp

<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @if($isEdit)
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    @endif
                </svg>
                {{ $isEdit ? 'Edit User' : 'Tambah User Baru' }}
            </h1>
            <p class="text-xs font-medium text-slate-400 mt-1">
                {{ $isEdit ? 'Perbarui data pengguna, role akses, dan kata sandi bila diperlukan.' : 'Daftarkan pengguna baru ke dalam sistem penugasan beserta penentuan hak aksesnya.' }}
            </p>
        </div>
        <div>
            <a href="{{ route('admin.user.index') }}"
                class="flex items-center px-4 py-2.5 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all duration-200">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                KEMBALI
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 text-xs font-semibold rounded-xl space-y-1 shadow-sm">
            <div class="flex items-center gap-2 mb-1 text-red-700 font-bold">
                <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Terjadi kesalahan pengisian data:
            </div>
            <ul class="list-disc pl-6 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <form action="{{ $isEdit ? route('admin.user.update', $user->nip) : route('admin.user.store') }}" method="POST" class="p-6 md:p-8 space-y-6">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label for="nip" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nomor Induk Pegawai (NIP)</label>
                    <input type="text" name="nip" id="nip" value="{{ old('nip', $isEdit ? $user->nip : '') }}" required
                        class="block w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 text-xs font-medium text-slate-800 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all outline-none"
                        placeholder="Contoh: 19900101XXXXXXXXXX">
                </div>

                <div class="space-y-2">
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $isEdit ? $user->name : '') }}" required
                        class="block w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 text-xs font-medium text-slate-800 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all outline-none"
                        placeholder="Masukkan nama lengkap pegawai">
                </div>

                <div class="space-y-2">
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Alamat Email Resmi</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $isEdit ? $user->email : '') }}" required
                        class="block w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 text-xs font-medium text-slate-800 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all outline-none"
                        placeholder="nama@instansi.go.id">
                </div>

                <div class="space-y-2">
                    <label for="role" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Hak Akses Sistem (Role)</label>
                    <select name="role" id="role" required
                        class="block w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 text-xs font-semibold text-slate-700 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all outline-none cursor-pointer">
                        <option value="" disabled {{ $selectedRole ? '' : 'selected' }}>-- Pilih Tingkat Hak Akses --</option>
                        <option value="user" {{ $selectedRole === 'user' ? 'selected' : '' }}>USER / ANGGOTA</option>
                        <option value="admin" {{ $selectedRole === 'admin' ? 'selected' : '' }}>ADMINISTRATOR</option>
                        <option value="superadmin" {{ $selectedRole === 'superadmin' ? 'selected' : '' }}>SUPER ADMIN</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Kata Sandi {{ $isEdit ? '(Opsional)' : '(Password)' }}
                    </label>
                    <input type="password" name="password" id="password" {{ $isEdit ? '' : 'required' }}
                        class="block w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 text-xs font-medium text-slate-800 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all outline-none"
                        placeholder="{{ $isEdit ? 'Kosongkan jika tidak ingin mengganti password' : 'Minimal 6 karakter unik' }}">
                </div>

                <div class="space-y-2">
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Ulangi Kata Sandi</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" {{ $isEdit ? '' : 'required' }}
                        class="block w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 text-xs font-medium text-slate-800 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all outline-none"
                        placeholder="{{ $isEdit ? 'Ulangi password baru jika diisi' : 'Pastikan kata sandi sama persis' }}">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                @if(!$isEdit)
                    <button type="reset"
                        class="px-5 py-2.5 text-xs font-bold text-slate-500 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl transition-colors">
                        KOSONGKAN FORM
                    </button>
                @endif
                <button type="submit"
                    class="px-6 py-2.5 text-xs font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 rounded-xl shadow-md shadow-blue-500/10 transition-all duration-200 hover:-translate-y-0.5">
                    {{ $isEdit ? 'SIMPAN PERUBAHAN' : 'SIMPAN USER BARU' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
