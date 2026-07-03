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
    <style>body{font-family:'Plus Jakarta Sans',sans-serif}</style>
</head>
<body class="bg-slate-50 min-h-screen antialiased overflow-hidden">
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-32 -left-20 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl"></div>
    </div>

    <main class="relative min-h-screen flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-6xl grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
            <section class="hidden lg:flex bg-white border border-slate-200/80 rounded-3xl shadow-sm overflow-hidden relative p-8 xl:p-10 flex-col justify-center animate-slide-up">
                <div class="absolute top-0 right-0 w-80 h-80 bg-gradient-to-tr from-blue-500/5 to-indigo-500/5 rounded-full blur-3xl"></div>
                <div class="relative space-y-8 z-10">
                    <div class="flex items-center gap-3">
                        <div class="bg-slate-900 p-3 rounded-2xl shadow-sm shadow-slate-900/10"><span class="text-yellow-400 text-xl font-black">⚡</span></div>
                        <div class="flex flex-col">
                            <span class="text-2xl font-black tracking-wider text-slate-900">POINT<span class="text-blue-600">IFY</span></span>
                            <span class="text-[10px] font-bold text-slate-400 tracking-widest uppercase leading-none mt-0.5">Sistem Penugasan</span>
                        </div>
                    </div>
                    <div class="space-y-4 max-w-xl">
                        <div class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Konsol Utama Agen</div>
                        <h1 class="text-4xl xl:text-5xl font-black text-slate-900 tracking-tight leading-tight">Masuk ke Sistem Informasi Penugasan DPU.</h1>
                        <p class="text-sm font-medium text-slate-500 leading-relaxed">Pantau distribusi tugas, perkembangan laporan, dan pekerjaan lapangan secara transparan melalui dashboard POINTIFY.</p>
                    </div>
                </div>
            </section>

            <section class="bg-white border border-slate-200/80 rounded-3xl shadow-sm overflow-hidden animate-slide-up" style="animation-delay:100ms;">
                <div class="h-1 bg-gradient-to-r from-blue-600 to-indigo-600"></div>
                <div class="p-6 sm:p-8 lg:p-10">
                    <div class="lg:hidden flex items-center justify-center gap-3 mb-8">
                        <div class="bg-slate-900 p-2 rounded-xl shadow-sm shadow-slate-900/10 text-yellow-400 font-black">⚡</div>
                        <div class="flex flex-col">
                            <span class="text-xl font-black tracking-wider text-slate-900">POINT<span class="text-blue-600">IFY</span></span>
                            <span class="text-[9px] font-bold text-slate-400 tracking-widest uppercase leading-none mt-0.5">Sistem Penugasan</span>
                        </div>
                    </div>

                    <div class="text-center lg:text-left space-y-2 mb-8">
                        <p class="text-[10px] font-black text-blue-600 uppercase tracking-[0.25em]">Secure Login</p>
                        <h2 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">Masuk ke Akun Anda</h2>
                        <p class="text-xs font-medium text-slate-500 leading-relaxed">Gunakan email dan kata sandi yang telah terdaftar pada sistem.</p>
                    </div>

                    <form action="{{ url('login') }}" method="POST" class="space-y-5">
                        @csrf
                        <div class="space-y-2">
                            <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Alamat Email</label>
                            <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" required class="block w-full px-4 py-3 bg-slate-50/70 border border-slate-200 text-sm font-medium text-slate-800 rounded-xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-600 transition-all outline-none" placeholder="nama@email.com">
                        </div>

                        <div class="space-y-2">
                            <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kata Sandi</label>
                            <input id="password" name="password" type="password" autocomplete="current-password" required class="block w-full px-4 py-3 bg-slate-50/70 border border-slate-200 text-sm font-medium text-slate-800 rounded-xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-600 transition-all outline-none" placeholder="••••••••">
                        </div>

                        <div class="flex items-center justify-between">
                            <label for="remember_me" class="flex items-center gap-2 text-xs font-semibold text-slate-600 cursor-pointer"><input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300 rounded cursor-pointer">Ingat saya</label>
                            <a href="{{ route('password.request') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline">Lupa kata sandi?</a>
                        </div>

                        <button type="submit" class="w-full flex justify-center py-3 px-4 rounded-xl shadow-md shadow-blue-500/10 text-sm font-black text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 hover:-translate-y-0.5 uppercase tracking-wider">Masuk Sekarang</button>
                    </form>

                    <div class="mt-8 pt-6 border-t border-slate-100 text-center"><p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">&copy; {{ date('Y') }} POINTIFY. Sistem Penugasan DPU.</p></div>
                </div>
            </section>
        </div>
    </main>
    <style>@keyframes slideUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}.animate-slide-up{animation:slideUp .45s ease-out both}</style>
    @include('partials.sweet-alert')
</body>
</html>
