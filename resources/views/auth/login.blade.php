<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - POINTIFY</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen antialiased overflow-hidden">
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-32 -left-20 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl"></div>
    </div>

    <main class="relative min-h-screen flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-6xl grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
            <section class="hidden lg:flex bg-white border border-slate-200/80 rounded-3xl shadow-sm overflow-hidden relative p-8 xl:p-10 flex-col justify-between animate-slide-up">
                <div class="absolute top-0 right-0 w-80 h-80 bg-gradient-to-tr from-blue-500/5 to-indigo-500/5 rounded-full blur-3xl"></div>

                <div class="relative space-y-8 z-10">
                    <div class="flex items-center gap-3">
                        <div class="bg-slate-900 p-3 rounded-2xl shadow-sm shadow-slate-900/10">
                            <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-2xl font-black tracking-wider text-slate-900">POINT<span class="text-blue-600">IFY</span></span>
                            <span class="text-[10px] font-bold text-slate-400 tracking-widest uppercase leading-none mt-0.5">Sistem Penugasan</span>
                        </div>
                    </div>

                    <div class="space-y-4 max-w-xl">
                        <div class="flex items-center gap-2">
                            <span class="flex h-2 w-2 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-600"></span>
                            </span>
                            <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Konsol Utama Agen</span>
                        </div>
                        <h1 class="text-4xl xl:text-5xl font-black text-slate-900 tracking-tight leading-tight">
                            Masuk ke Sistem Informasi Penugasan DPU.
                        </h1>
                        <p class="text-sm font-medium text-slate-500 leading-relaxed">
                            Pantau distribusi tugas, perkembangan laporan, dan prioritas pekerjaan lapangan secara transparan melalui dashboard POINTIFY.
                        </p>
                    </div>
                </div>

                <div class="relative grid grid-cols-3 gap-4 z-10">
                    <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 shadow-inner">
                        <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Akses</p>
                        <p class="text-xs font-bold text-slate-800 mt-1">Admin & User</p>
                    </div>
                    <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 shadow-inner">
                        <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Status</p>
                        <p class="text-xs font-bold text-slate-800 mt-1">Realtime</p>
                    </div>
                    <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 shadow-inner">
                        <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest">DSS</p>
                        <p class="text-xs font-bold text-slate-800 mt-1">Prioritas</p>
                    </div>
                </div>
            </section>

            <section class="bg-white border border-slate-200/80 rounded-3xl shadow-sm overflow-hidden animate-slide-up" style="animation-delay: 100ms;">
                <div class="h-1 bg-gradient-to-r from-blue-600 to-indigo-600"></div>

                <div class="p-6 sm:p-8 lg:p-10">
                    <div class="lg:hidden flex items-center justify-center gap-3 mb-8">
                        <div class="bg-slate-900 p-2 rounded-xl shadow-sm shadow-slate-900/10">
                            <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xl font-black tracking-wider text-slate-900">POINT<span class="text-blue-600">IFY</span></span>
                            <span class="text-[9px] font-bold text-slate-400 tracking-widest uppercase leading-none mt-0.5">Sistem Penugasan</span>
                        </div>
                    </div>

                    <div class="text-center lg:text-left space-y-2 mb-8">
                        <p class="text-[10px] font-black text-blue-600 uppercase tracking-[0.25em]">Secure Login</p>
                        <h2 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">Masuk ke Akun Anda</h2>
                        <p class="text-xs font-medium text-slate-500 leading-relaxed">
                            Gunakan email dan kata sandi yang telah terdaftar pada sistem.
                        </p>
                    </div>

                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-2xl flex items-start gap-3">
                            <div class="p-2 bg-red-500/10 rounded-xl">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-red-800">Login gagal</h3>
                                <p class="text-sm font-medium text-red-600 mt-0.5">{{ $errors->first() }}</p>
                            </div>
                        </div>
                    @endif

                    <form action="{{ url('login') }}" method="POST" class="space-y-5">
                        @csrf

                        <div class="space-y-2">
                            <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Alamat Email</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </span>
                                <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" required
                                    class="block w-full pl-10 pr-4 py-3 bg-slate-50/70 border border-slate-200 text-sm font-medium text-slate-800 rounded-xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-600 transition-all outline-none @error('email') border-red-300 @enderror"
                                    placeholder="nama@email.com">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kata Sandi</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </span>
                                <input id="password" name="password" type="password" autocomplete="current-password" required
                                    class="block w-full pl-10 pr-4 py-3 bg-slate-50/70 border border-slate-200 text-sm font-medium text-slate-800 rounded-xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-600 transition-all outline-none"
                                    placeholder="••••••••">
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <label for="remember_me" class="flex items-center gap-2 text-xs font-semibold text-slate-600 cursor-pointer">
                                <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300 rounded cursor-pointer">
                                Ingat saya
                            </label>
                            <a href="#" class="text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline">Lupa kata sandi?</a>
                        </div>

                        <button type="submit"
                            class="w-full flex justify-center py-3 px-4 rounded-xl shadow-md shadow-blue-500/10 text-sm font-black text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 hover:-translate-y-0.5 uppercase tracking-wider">
                            Masuk Sekarang
                        </button>
                    </form>

                    <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            &copy; {{ date('Y') }} POINTIFY. Sistem Penugasan DPU.
                        </p>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <style>
        @keyframes slideUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        .animate-slide-up { animation: slideUp 0.45s ease-out both; }
    </style>
</body>

</html>
