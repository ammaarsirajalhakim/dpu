<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POINTIFY - Sistem Penugasan</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>

<body class="bg-slate-50 flex flex-col min-h-screen antialiased">
    <header class="bg-white text-slate-800 border-b border-slate-200 sticky top-0 z-50 shadow-sm flex-shrink-0 h-20 flex items-center">
        <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="bg-slate-900 p-2 rounded-xl shadow-sm shadow-slate-900/10 hidden sm:block">
                        <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xl font-black tracking-wider text-slate-900">POINT<span class="text-blue-600">IFY</span></span>
                        <span class="text-[9px] font-bold text-slate-400 tracking-widest uppercase leading-none mt-0.5">Sistem Penugasan</span>
                    </div>
                </div>

                <nav class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('dashboard') }}" class="px-4 py-2.5 text-xs font-bold tracking-wide rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-blue-600' }}">DASHBOARD</a>
                    <a href="{{ route('penugasan.index') }}" class="px-4 py-2.5 text-xs font-bold tracking-wide rounded-xl transition-all duration-200 {{ request()->routeIs('penugasan.*') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-blue-600' }}">PENUGASAN SAYA</a>
                    <a href="{{ route('laporan.index') }}" class="px-4 py-2.5 text-xs font-bold tracking-wide rounded-xl transition-all duration-200 {{ request()->routeIs('laporan.*') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-blue-600' }}">LAPORAN</a>
                </nav>

                <div class="flex items-center space-x-3">
                    <div class="hidden sm:flex flex-col items-end text-right mr-1">
                        <span class="text-xs font-bold text-slate-800">{{ Auth::user()->name }}</span>
                        <span class="text-[9px] font-extrabold text-blue-600 bg-blue-50 border border-blue-100 px-1.5 py-0.5 rounded uppercase tracking-wider mt-0.5">{{ Auth::user()->role }}</span>
                    </div>

                    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'superadmin')
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 hover:border-blue-500 hover:text-blue-600 rounded-xl transition-all duration-200 shadow-sm uppercase tracking-wide" title="Masuk ke Panel Kontrol Admin">ADMIN PANEL</a>
                    @endif

                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="flex items-center px-4 py-2.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-900 hover:text-white border border-slate-200 rounded-xl transition-all duration-200 uppercase tracking-wide" title="Keluar dari Sistem">LOGOUT</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <div class="md:hidden bg-white border-b border-slate-200 flex justify-around py-3 px-2 text-center text-[10px] font-bold tracking-widest uppercase shadow-sm">
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'text-blue-600 font-black' : 'text-slate-500' }}">Dashboard</a>
        <a href="{{ route('penugasan.index') }}" class="{{ request()->routeIs('penugasan.*') ? 'text-blue-600 font-black' : 'text-slate-500' }}">Penugasan</a>
        <a href="{{ route('laporan.index') }}" class="{{ request()->routeIs('laporan.*') ? 'text-blue-600 font-black' : 'text-slate-500' }}">Laporan</a>
    </div>

    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-slate-200 text-slate-400 py-6 text-center text-xs font-medium mt-auto flex-shrink-0 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-1">
            <p class="text-slate-600">&copy; {{ date('Y') }} <span class="font-bold text-slate-800 tracking-wide">POINTIFY</span>. Hak Cipta Dilindungi Undang-Undang.</p>
            <p class="text-[10px] text-slate-400 font-mono tracking-wider uppercase">User Console Layout &bull; Core Platform Powered by Laravel</p>
        </div>
    </footer>

    <script>
        (function () {
            const storageKey = 'pointify_local_login_active';
            const lastNotifyKey = 'pointify_last_daily_report_notification';
            const endpoint = @json(route('daily-progress.pending-summary'));

            localStorage.setItem(storageKey, '1');

            async function checkDailyProgressReminder() {
                if (!localStorage.getItem(storageKey)) return;

                try {
                    const response = await fetch(endpoint, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin'
                    });

                    if (!response.ok) return;

                    const data = await response.json();
                    if (!data.count || data.count < 1) return;

                    const now = Date.now();
                    const lastNotify = parseInt(localStorage.getItem(lastNotifyKey) || '0', 10);
                    const oneHour = 60 * 60 * 1000;

                    if (now - lastNotify < oneHour) return;
                    localStorage.setItem(lastNotifyKey, String(now));

                    const message = data.count + ' penugasan belum memiliki laporan harian hari ini.';

                    if ('Notification' in window) {
                        if (Notification.permission === 'granted') {
                            new Notification('POINTIFY - Laporan Harian', { body: message });
                        } else if (Notification.permission !== 'denied') {
                            Notification.requestPermission().then(function (permission) {
                                if (permission === 'granted') new Notification('POINTIFY - Laporan Harian', { body: message });
                            });
                        }
                    }
                } catch (error) {
                    console.warn('[POINTIFY] Gagal cek reminder laporan harian.', error);
                }
            }

            window.addEventListener('load', function () {
                setTimeout(checkDailyProgressReminder, 5000);
                setInterval(checkDailyProgressReminder, 60 * 60 * 1000);
            });
        })();
    </script>

    @include('partials.sweet-alert')
</body>
</html>
