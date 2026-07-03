@extends('layout.layoutadmin')

@section('content')
@php
    $isEdit = isset($user);
    $selectedRole = old('role', $isEdit ? $user->role : 'user');
@endphp

<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">{{ $isEdit ? 'Edit User' : 'Tambah User Baru' }}</h1>
            <p class="text-xs font-medium text-slate-400 mt-1">{{ $isEdit ? 'Perbarui data pengguna.' : 'Tambahkan akun pengguna baru.' }}</p>
        </div>
        <a href="{{ route('admin.user.index') }}" class="px-4 py-2.5 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl">KEMBALI</a>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 text-xs font-semibold rounded-xl">
            <div class="font-bold mb-1">Terjadi kesalahan pengisian data:</div>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 text-xs font-semibold rounded-xl">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <form action="{{ $isEdit ? route('admin.user.update', $user->nip) : route('admin.user.store') }}" method="POST" class="p-6 md:p-8 space-y-6">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">NIP / Kode Pegawai</label>
                    <input type="text" name="nip" value="{{ old('nip', $isEdit ? $user->nip : '') }}" required maxlength="20" class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-xs font-medium text-slate-800 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 outline-none" placeholder="Masukkan NIP atau kode pegawai">
                    <p class="text-[10px] text-slate-400">Maksimal 20 karakter.</p>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $isEdit ? $user->name : '') }}" required class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-xs font-medium text-slate-800 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 outline-none" placeholder="Masukkan nama lengkap">
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Email</label>
                    <input type="email" name="email" value="{{ old('email', $isEdit ? $user->email : '') }}" required class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-xs font-medium text-slate-800 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 outline-none" placeholder="nama@email.com">
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Role</label>
                    <select name="role" required class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 outline-none">
                        <option value="user" {{ $selectedRole === 'user' ? 'selected' : '' }}>USER / ANGGOTA</option>
                        <option value="admin" {{ $selectedRole === 'admin' ? 'selected' : '' }}>ADMINISTRATOR</option>
                        <option value="superadmin" {{ $selectedRole === 'superadmin' ? 'selected' : '' }}>SUPER ADMIN</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kata Sandi {{ $isEdit ? '(Opsional)' : '(Wajib)' }}</label>
                    <input type="password" name="password" {{ $isEdit ? '' : 'required' }} minlength="6" class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-xs font-medium text-slate-800 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 outline-none" placeholder="Minimal 6 karakter">
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Ulangi Kata Sandi</label>
                    <input type="password" name="password_confirmation" {{ $isEdit ? '' : 'required' }} minlength="6" class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-xs font-medium text-slate-800 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 outline-none" placeholder="Ulangi kata sandi">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="submit" class="px-6 py-2.5 text-xs font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 rounded-xl shadow-md shadow-blue-500/10">
                    {{ $isEdit ? 'SIMPAN PERUBAHAN' : 'SIMPAN USER BARU' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
