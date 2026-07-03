<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Kata Sandi - POINTIFY</title>
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
        <section class="w-full max-w-md bg-white border border-slate-200/80 rounded-3xl shadow-sm overflow-hidden">
            <div class="h-1 bg-gradient-to-r from-blue-600 to-indigo-600"></div>
            <div class="p-8 space-y-6">
                <div class="text-center space-y-2">
                    <div class="text-3xl font-black text-slate-900">POINT<span class="text-blue-600">IFY</span></div>
                    <h1 class="text-xl font-black text-slate-900">Atur Kata Sandi Baru</h1>
                    <p class="text-xs font-medium text-slate-500">Buat kata sandi baru untuk akun Anda.</p>
                </div>

                <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input name="email" type="email" value="{{ old('email', $email) }}" required class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 text-sm font-medium text-slate-800 placeholder-slate-400 rounded-xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-600 outline-none" placeholder="nama@email.com">
                    <input name="password" type="password" required class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 text-sm font-medium text-slate-800 placeholder-slate-400 rounded-xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-600 outline-none" placeholder="Kata sandi baru">
                    <input name="password_confirmation" type="password" required class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 text-sm font-medium text-slate-800 placeholder-slate-400 rounded-xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-600 outline-none" placeholder="Ulangi kata sandi baru">
                    <button type="submit" class="w-full py-3 rounded-xl text-sm font-black text-white bg-gradient-to-r from-blue-600 to-indigo-600 uppercase tracking-wider">Simpan Kata Sandi</button>
                </form>
            </div>
        </section>
    </main>
    @include('partials.sweet-alert')
</body>
</html>
