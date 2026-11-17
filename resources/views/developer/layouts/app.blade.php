<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>{{ $title ?? 'Developer Console' }} - Core Platform Management</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Play CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        mono: ['JetBrains Mono', 'ui-monospace', 'monospace'],
                    },
                    colors: {
                        dev: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                            950: '#1e1b4b',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
</head>
<body class="h-full bg-slate-900 text-slate-100 font-sans antialiased" x-data="{ mobileSidebarOpen: false }">

    <div class="min-h-full flex flex-col md:flex-row">

        <!-- DESKTOP SIDEBAR -->
        <aside class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 bg-slate-950 border-r border-slate-800/80 z-30">
            <!-- Brand Box -->
            <div class="h-16 flex items-center gap-3 px-6 border-b border-slate-800/80 bg-slate-950/80">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center font-black text-white text-base shadow-md shadow-indigo-600/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5"/>
                    </svg>
                </div>
                <div>
                    <span class="font-black text-sm tracking-tight text-white flex items-center gap-1">
                        DEV <span class="text-indigo-400">CONSOLE</span>
                    </span>
                    <span class="text-[10px] text-slate-400 font-mono block">Platform Provisioning</span>
                </div>
            </div>

            <!-- Developer Identity Pill -->
            <div class="px-4 py-3 border-b border-slate-800/60 bg-slate-900/40">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-indigo-500/20 border border-indigo-500/30 text-indigo-300 font-black text-xs flex items-center justify-center font-mono">
                        {{ strtoupper(substr(auth()->user()->nama ?? 'D', 0, 2)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-xs font-bold text-white truncate">{{ auth()->user()->nama ?? 'Developer' }}</div>
                        <div class="text-[10px] font-mono text-indigo-400 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            ROLE: DEVELOPER
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <div class="px-3 pb-1 text-[10px] font-mono font-bold uppercase tracking-wider text-slate-500">
                    Core Modules
                </div>

                <!-- Dashboard -->
                <a href="{{ route('developer.dashboard') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition {{ request()->routeIs('developer.dashboard') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                    <span>Dashboard Platform</span>
                </a>

                <!-- Kelola Owner -->
                <a href="{{ route('developer.owner.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition {{ request()->routeIs('developer.owner.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                    <span>Kelola Owner</span>
                </a>

                <!-- Kelola Cabang -->
                <a href="{{ route('developer.cabang.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition {{ request()->routeIs('developer.cabang.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009 9.35c.692-.802 1.73-1.35 2.89-1.35 1.16 0 2.198.548 2.89 1.35a2.993 2.993 0 002.36-.615 3.001 3.001 0 003.75.615m-18 0l2.25-5.25a1.5 1.5 0 011.38-.909h10.74a1.5 1.5 0 011.38.909l2.25 5.25"/></svg>
                    <span>Kelola Cabang</span>
                </a>

                <!-- Kelola Kasir -->
                <a href="{{ route('developer.kasir.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition {{ request()->routeIs('developer.kasir.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
                    <span>Kelola Kasir</span>
                </a>

                <div class="pt-4 px-3 pb-1 text-[10px] font-mono font-bold uppercase tracking-wider text-slate-500">
                    System &amp; Settings
                </div>

                <!-- Profil -->
                <a href="{{ route('developer.profile') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition {{ request()->routeIs('developer.profile') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Profil Pengembang</span>
                </a>
            </nav>

            <!-- Bottom Logout -->
            <div class="p-3 border-t border-slate-800/80 bg-slate-950">
                <form action="{{ route('developer.logout') }}" method="POST">
                    @csrf
                    <button type="submit" 
                        class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-xs font-bold text-rose-400 hover:text-rose-200 hover:bg-rose-950/40 border border-transparent hover:border-rose-900/50 transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
                        <span>Logout Developer</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- MOBILE HEADER & DRAWER -->
        <div class="md:hidden w-full bg-slate-950 border-b border-slate-800 flex items-center justify-between px-4 h-16 sticky top-0 z-40">
            <div class="flex items-center gap-2.5">
                <button type="button" @click="mobileSidebarOpen = !mobileSidebarOpen" class="text-slate-400 hover:text-white p-1.5 rounded-lg bg-slate-900 border border-slate-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                </button>
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-indigo-600 flex items-center justify-center font-bold text-white text-xs">
                        DEV
                    </div>
                    <span class="font-black text-sm text-white">DEVELOPER CONSOLE</span>
                </div>
            </div>

            <form action="{{ route('developer.logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-xs font-bold text-rose-400 hover:text-rose-300">Logout</button>
            </form>
        </div>

        <!-- MOBILE SIDEBAR OVERLAY -->
        <div x-show="mobileSidebarOpen" class="md:hidden fixed inset-0 z-50 flex" style="display: none;">
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm" @click="mobileSidebarOpen = false"></div>
            <div class="relative flex-1 flex flex-col max-w-xs w-full bg-slate-950 border-r border-slate-800 p-4 space-y-3">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <span class="font-black text-white text-sm">DEVELOPER CONSOLE</span>
                    <button type="button" @click="mobileSidebarOpen = false" class="text-slate-400 hover:text-white">✕</button>
                </div>
                <nav class="space-y-1 text-sm font-bold">
                    <a href="{{ route('developer.dashboard') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-900">Dashboard</a>
                    <a href="{{ route('developer.owner.index') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-900">Kelola Owner</a>
                    <a href="{{ route('developer.cabang.index') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-900">Kelola Cabang</a>
                    <a href="{{ route('developer.kasir.index') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-900">Kelola Kasir</a>
                    <a href="{{ route('developer.profile') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-900">Profil Developer</a>
                </nav>
            </div>
        </div>

        <!-- MAIN CONTENT AREA -->
        <div class="md:pl-64 flex-1 flex flex-col min-h-screen bg-slate-900">

            <!-- Top bar -->
            <header class="h-16 border-b border-slate-800/80 bg-slate-950/50 backdrop-blur-md px-4 sm:px-8 flex items-center justify-between sticky top-0 z-20">
                <div class="flex items-center gap-2 text-xs sm:text-sm text-slate-400">
                    <span class="text-indigo-400 font-mono font-bold">DEVELOPER CONSOLE</span>
                    <span>/</span>
                    <span class="text-white font-bold">{{ $breadcrumb ?? 'Platform Control' }}</span>
                </div>

                <div class="flex items-center gap-3">
                    <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-mono bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        SYSTEM OPERATIONAL
                    </span>
                    <a href="{{ route('dashboard') }}" class="text-xs font-bold text-slate-400 hover:text-indigo-300 px-3 py-1.5 rounded-lg border border-slate-800 bg-slate-900/60 transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        <span>Ke Portal Kasir/Owner</span>
                    </a>
                </div>
            </header>

            <!-- Page Body -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl w-full mx-auto">
                <!-- Flash Alerts -->
                @if(session('success'))
                    <div class="p-4 rounded-xl bg-emerald-950/60 border border-emerald-800 text-emerald-300 text-xs sm:text-sm flex items-center gap-3 shadow-lg shadow-emerald-950/20">
                        <svg class="w-5 h-5 shrink-0 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="font-semibold">{{ session('success') }}</div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="p-4 rounded-xl bg-rose-950/60 border border-rose-800 text-rose-300 text-xs sm:text-sm flex items-center gap-3 shadow-lg shadow-rose-950/20">
                        <svg class="w-5 h-5 shrink-0 text-rose-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                        <div class="font-semibold">{{ session('error') }}</div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="p-4 rounded-xl bg-rose-950/60 border border-rose-800 text-rose-300 text-xs sm:text-sm space-y-1">
                        <div class="font-bold flex items-center gap-2 text-rose-200">
                            <svg class="w-4 h-4 shrink-0 text-rose-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                            <span>Terjadi kesalahan pada input:</span>
                        </div>
                        <ul class="list-disc pl-5 space-y-0.5 text-xs">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="border-t border-slate-800/80 px-6 py-4 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} Developer Console &bull; Mengelola Owner, Cabang, dan Kasir tanpa modul transaksi.
            </footer>
        </div>

    </div>

</body>
</html>
