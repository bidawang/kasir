<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - BRILINK</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-sm w-full bg-white rounded-2xl border border-slate-200/85 shadow-sm p-6 sm:p-8 space-y-6">

        <!-- Logo / Header -->
        <div class="text-center space-y-1">
            <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">BRILINK</h1>
            <p class="text-xs text-slate-500 font-medium">Masuk untuk mengelola transaksi kas</p>
        </div>

        <!-- Alert Error / Validation -->
        @if ($errors->any() || session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-xs sm:text-sm font-medium shadow-sm">
                <div class="flex items-start gap-2.5">
                    <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    <div class="space-y-1 w-full">
                        <p class="font-bold">Terjadi kesalahan:</p>
                        <ul class="list-disc list-inside space-y-0.5 text-xs">
                            @if (session('error'))
                                <li>{{ session('error') }}</li>
                            @endif
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form Login -->
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email -->
            <div class="space-y-1.5">
                <label for="email" class="block text-xs sm:text-sm font-bold text-slate-700">Alamat Email</label>
                <input type="email" 
                       name="email" 
                       id="email" 
                       value="{{ old('email') }}"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200" 
                       required 
                       autofocus
                       placeholder="nama@email.com">
            </div>

            <!-- Password -->
            <div class="space-y-1.5">
                <label for="password" class="block text-xs sm:text-sm font-bold text-slate-700">Kata Sandi</label>
                <input type="password" 
                       name="password" 
                       id="password" 
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200" 
                       required
                       placeholder="••••••••">
            </div>

            <!-- Button Submit -->
            <div class="pt-2">
                <button type="submit" 
                        class="w-full bg-primary-600 hover:bg-primary-700 active:scale-95 text-white py-2.5 rounded-xl text-xs sm:text-sm font-semibold shadow-sm shadow-primary-500/25 transition-all duration-200 flex items-center justify-center gap-1.5">
                    <span>Masuk</span>
                </button>
            </div>
        </form>

        <!-- Register Link -->
        <div class="text-center text-xs text-slate-500 font-medium">
            Belum punya akun? <a href="{{ route('register') }}" class="text-primary-600 font-bold hover:underline">Daftar di sini</a>
        </div>

    </div>

</body>

</html>