<!doctype html>
<html lang="id" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="color-scheme" content="light dark">
    <title>Login - AgentKas</title>

    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'ui-sans-serif', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50:  '#eef6fc',
                            100: '#d5e8f7',
                            200: '#b0d4f0',
                            300: '#7cb7e6',
                            400: '#4195d9',
                            500: '#1b78c6',
                            600: '#00529C',
                            700: '#004585',
                            800: '#043b6e',
                            900: '#09325c',
                            950: '#06203d',
                        },
                        accent: {
                            500: '#F37021',
                            600: '#D9580F',
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-screen flex items-center justify-center p-4 selection:bg-primary-500 selection:text-white font-sans">

    <div class="max-w-sm w-full bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-lg p-5 sm:p-6 space-y-4">

        <!-- Logo / Header -->
        <div class="text-center space-y-1">
            <div class="w-11 h-11 mx-auto rounded-xl bg-gradient-to-tr from-primary-700 to-primary-600 text-white flex items-center justify-center font-black text-base shadow-md shadow-primary-700/25">
                AK
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">AgentKas</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Sistem Operasional Kas & Keuangan Agen</p>
        </div>

        <!-- Alert Error / Validation -->
        @if ($errors->any() || session('error'))
            <div class="bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 text-rose-800 dark:text-rose-300 p-3 rounded-xl text-xs font-semibold shadow-2xs">
                <div class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    <div class="space-y-0.5 w-full">
                        <p class="font-bold">Gagal masuk:</p>
                        <ul class="list-disc list-inside space-y-0.5 text-[11px] opacity-90">
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
            <div class="bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900 text-emerald-800 dark:text-emerald-300 p-3 rounded-xl text-xs font-semibold shadow-2xs flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Form Login -->
        <form method="POST" action="{{ route('login') }}" class="space-y-3">
            @csrf

            <!-- Email / Username -->
            <div>
                <label for="email" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Email / Username</label>
                <input type="text"
                       name="email"
                       id="email"
                       value="{{ old('email') }}"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs sm:text-sm font-semibold text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
                       required
                       autofocus
                       placeholder="nama@email.com atau username">
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Kata Sandi</label>
                <input type="password"
                       name="password"
                       id="password"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs sm:text-sm font-semibold text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
                       required
                       placeholder="••••••••">
            </div>

            <!-- Button Submit -->
            <div class="pt-1.5">
                <button type="submit"
                        class="w-full bg-primary-600 hover:bg-primary-700 active:scale-95 text-white py-2.5 rounded-lg text-xs sm:text-sm font-bold shadow-md shadow-primary-600/20 transition cursor-pointer">
                    Masuk ke Sistem
                </button>
            </div>
        </form>

        <!-- Register Link -->
        <div class="text-center text-xs text-slate-500 dark:text-slate-400 font-medium pt-2 border-t border-slate-100 dark:border-slate-800 space-y-1.5">
            <div>
                Belum punya akun? <a href="{{ route('register') }}" class="text-primary-600 dark:text-primary-400 font-bold hover:underline">Daftar akun agen</a>
            </div>
            <div>
                <a href="{{ route('developer.login') }}" class="text-[10px] font-mono text-slate-400 hover:text-primary-600 transition inline-flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5"/></svg>
                    <span>Developer Console</span>
                </a>
            </div>
        </div>

    </div>

</body>
</html>
