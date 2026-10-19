<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - BRILINK</title>

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

    <div class="max-w-md w-full space-y-4">

        <!-- Alert Error / Success -->
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

        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs sm:text-sm font-medium shadow-sm flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Card Container -->
        <div class="bg-white rounded-2xl border border-slate-200/85 shadow-sm p-6 sm:p-8 space-y-6" x-data="{ showPass: false, showConfirmPass: false }">

            <!-- Header -->
            <div class="text-center space-y-1">
                <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">Daftar</h1>
                <p class="text-xs text-slate-500 font-medium">Buat akun baru untuk melanjutkan</p>
            </div>

            <!-- Form Register -->
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <!-- Nama -->
                <div class="space-y-1.5">
                    <label for="nama" class="block text-xs sm:text-sm font-bold text-slate-700">Nama</label>
                    <input type="text" 
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200" 
                           id="nama" 
                           name="nama" 
                           value="{{ old('nama') }}"
                           required 
                           autofocus
                           placeholder="Nama lengkap">
                </div>

                <!-- Alamat Email -->
                <div class="space-y-1.5">
                    <label for="email" class="block text-xs sm:text-sm font-bold text-slate-700">Alamat Email</label>
                    <input type="email" 
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           required
                           placeholder="nama@email.com">
                </div>

                <!-- Kata Sandi -->
                <div class="space-y-1.5">
                    <label for="password" class="block text-xs sm:text-sm font-bold text-slate-700">Kata Sandi</label>
                    <div class="relative flex items-center">
                        <input :type="showPass ? 'text' : 'password'" 
                               class="w-full px-3.5 py-2.5 pr-10 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200" 
                               id="password" 
                               name="password" 
                               required
                               placeholder="••••••••">
                        <button type="button" @click="showPass = !showPass" class="absolute right-3.5 text-slate-400 hover:text-slate-600 focus:outline-none">
                            <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <svg x-show="showPass" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.326 16.167 7.264 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.736 0 8.674 3.333 10.066 7.5a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243l4.242 4.242z"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Konfirmasi Kata Sandi -->
                <div class="space-y-1.5">
                    <label for="password_confirmation" class="block text-xs sm:text-sm font-bold text-slate-700">Konfirmasi Kata Sandi</label>
                    <div class="relative flex items-center">
                        <input :type="showConfirmPass ? 'text' : 'password'" 
                               class="w-full px-3.5 py-2.5 pr-10 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               required
                               placeholder="••••••••">
                        <button type="button" @click="showConfirmPass = !showConfirmPass" class="absolute right-3.5 text-slate-400 hover:text-slate-600 focus:outline-none">
                            <svg x-show="!showConfirmPass" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <svg x-show="showConfirmPass" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.326 16.167 7.264 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.736 0 8.674 3.333 10.066 7.5a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243l4.242 4.242z"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" 
                            class="w-full bg-primary-600 hover:bg-primary-700 active:scale-95 text-white py-2.5 rounded-xl text-xs sm:text-sm font-semibold shadow-sm shadow-primary-500/25 transition-all duration-200 flex items-center justify-center gap-1.5">
                        <span>Daftar</span>
                    </button>
                </div>
            </form>

            <!-- Link Login -->
            <div class="text-center text-xs text-slate-500 font-medium">
                Sudah punya akun? <a href="{{ route('login') }}" class="text-primary-600 font-bold hover:underline">Masuk di sini</a>
            </div>

        </div>

    </div>

    <!-- Alpine.js CDN -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>

</html>