<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POINTIFY - Memuat Sistem</title>

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
<body class="bg-slate-50 min-h-screen flex items-center justify-center antialiased overflow-hidden">
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-32 -left-20 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl"></div>
    </div>

    <main class="relative w-full max-w-md px-6">
        <div class="bg-white border border-slate-200/80 rounded-3xl shadow-sm overflow-hidden text-center animate-fade-in">
            <div class="h-1 bg-gradient-to-r from-blue-600 to-indigo-600"></div>

            <div class="p-8 sm:p-10 space-y-7">
                <div class="flex justify-center">
                    <div class="w-28 h-28 rounded-3xl bg-slate-50 border border-slate-200/80 shadow-inner flex items-center justify-center p-4">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo POINTIFY" class="max-w-full max-h-full object-contain">
                    </div>
                </div>

                <div class="space-y-2">
                    <p class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em]">Sistem Penugasan DPU</p>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tight">
                        POINT<span class="text-blue-600">IFY</span>
                    </h1>
                    <p class="text-xs font-medium text-slate-500 leading-relaxed max-w-xs mx-auto">
                        Menyiapkan konsol penugasan dan pelaporan proyek infrastruktur daerah.
                    </p>
                </div>

                <div class="flex flex-col items-center gap-3">
                    <div class="w-12 h-12 border-4 border-slate-100 border-t-blue-600 rounded-full animate-spin"></div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Memuat halaman login</p>
                </div>
            </div>
        </div>
    </main>

    <script>
        setTimeout(function() {
            window.location.href = "{{ route('login') }}";
        }, 1800);
    </script>

    <style>
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.45s ease-out both; }
    </style>
</body>
</html>
