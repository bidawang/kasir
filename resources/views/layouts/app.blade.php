<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="color-scheme" content="light dark">
    <title>{{ $title ?? 'Agent Kas - Sistem Kasir Perbankan' }}</title>

    <!-- Theme Initialization Script (Prevent FOUC) -->
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"></noscript>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-800 antialiased selection:bg-primary-500 selection:text-white flex flex-col min-h-screen">

    @php
        // Ambil daftar cabang yang berhak diakses (hanya kolom yang diperlukan)
        $userRole = auth()->user()->role ?? '';
        $isAdminRole = in_array($userRole, ['admin', 'owner'], true);
        $listWarungs = \App\Models\Kasir::when($userRole === 'kasir', function ($query) {
            $query->where(function ($query) {
                $query->where('id_user', auth()->id())
                    ->orWhereHas('users', fn ($users) => $users->where('user.id', auth()->id()));
            });
        })->when(!$isAdminRole && $userRole !== 'kasir', function ($query) {
            $query->where('id_user', auth()->id());
        })->orderBy('id')->get(['id', 'nama_warung', 'bank_utama', 'id_user']);

        // Auto-set session cabang pertama jika session masih kosong
        if (!session('id_warung') && $listWarungs->isNotEmpty()) {
            session(['id_warung' => $listWarungs->first()->id]);
        }

        // Ambil objek cabang aktif berdasarkan session
        $cabangAktif = $listWarungs->firstWhere('id', session('id_warung')) 
            ?? \App\Models\Kasir::find(session('id_warung'));
            
        // Helper untuk cek keaktifan menu transaksi
        $isTransaksiActive = request()->routeIs('transaksi.menu*') || request()->routeIs('transaksi.index*') || request()->routeIs('transaksi.setor*') || request()->routeIs('transaksi.tarik*') || request()->routeIs('transaksi.kas.*') || request()->routeIs('transaksi.penjualan-item.*');
    @endphp

    <!-- HEADER UTAMA (Sticky Top) -->
    <header class="sticky top-0 z-40 bg-gradient-to-r from-primary-950 via-primary-900 to-primary-800 text-white shadow-md border-b border-primary-700/40">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-14 sm:h-16 gap-2 sm:gap-4">
                
                <!-- Left: Logo & Cabang Info -->
                <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                    <a href="{{ route('dashboard') }}" class="w-8 h-8 sm:w-10 sm:h-10 shrink-0 rounded-xl bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 flex items-center justify-center font-black text-white text-xs sm:text-sm tracking-wider shadow-inner transition active:scale-95">
                        AK
                    </a>

                    <div class="min-w-0 leading-tight">
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-extrabold tracking-widest text-primary-200 uppercase">Agent Kas</span>
                            @if($cabangAktif)
                                <span class="hidden sm:inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    {{ $cabangAktif->bank_utama ?? 'BRI' }}
                                </span>
                            @endif
                        </div>
                        <h1 class="text-xs sm:text-sm font-bold text-white truncate max-w-[130px] sm:max-w-xs">
                            {{ $cabangAktif?->nama_warung ?? 'Pilih Cabang' }}
                        </h1>
                    </div>
                </div>

                <!-- Center Nav Desktop (Tablet / PC View) -->
                <nav class="hidden lg:flex items-center gap-1 bg-white/10 p-1 rounded-xl backdrop-blur-sm border border-white/10">
                    @if($isAdminRole)
                    <a href="{{ route('dashboard') }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ request()->routeIs('dashboard') ? 'bg-white text-primary-950 shadow-sm' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v4.875h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                        </svg>
                        <span>Beranda</span>
                    </a>
                    @endif

                    <a href="{{ route('transaksi.menu') }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ ($isTransaksiActive && !request()->routeIs('transaksi.index')) ? 'bg-white text-primary-950 shadow-sm' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 6.75h15m-15 5.25h15m-15 5.25h15"/>
                        </svg>
                        <span>Transaksi</span>
                    </a>

                    <a href="{{ route('transaksi.index') }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ request()->routeIs('transaksi.index') ? 'bg-white text-primary-950 shadow-sm' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.66 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.66m-5.801 0c-3.04.15-5.5 2.66-5.5 5.75v9.5a2.25 2.25 0 002.25 2.25h13.5A2.25 2.25 0 0021 18.25v-9.5c0-3.09-2.46-5.6-5.5-5.75m-5.801 0a48.108 48.108 0 015.801 0"/>
                        </svg>
                        <span>Riwayat</span>
                    </a>

                    <a href="{{ route('laporan-harian.index') }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ request()->routeIs('laporan-harian.*') ? 'bg-white text-primary-950 shadow-sm' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Laporan Harian</span>
                    </a>

                    @if($isAdminRole)
                    <a href="{{ route('manajemen-kasir.index') }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ request()->routeIs('manajemen-kasir.*') ? 'bg-white text-primary-950 shadow-sm' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                        </svg>
                        <span>Kelola Kasir</span>
                    </a>
                    <a href="{{ route('kas.index') }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ request()->routeIs('kas.*') ? 'bg-white text-primary-950 shadow-sm' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3"/>
                        </svg>
                        <span>Kas</span>
                    </a>

                    <a href="{{ route('item-jualan.index') }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ request()->routeIs('item-jualan.*') ? 'bg-white text-primary-950 shadow-sm' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l2.4 12.2a2 2 0 002 1.6h8.8a2 2 0 002-1.6L22 8H6M10 21h.01M18 21h.01"/>
                        </svg>
                        <span>Barang</span>
                    </a>

                    <a href="{{ route('biaya-admin.index') }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ request()->routeIs('biaya-admin.*') ? 'bg-white text-primary-950 shadow-sm' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m4-9H9.5a2.5 2.5 0 000 5h5a2.5 2.5 0 010 5H8"/>
                            <circle cx="12" cy="12" r="9"/>
                        </svg>
                        <span>Biaya Admin</span>
                    </a>

                    <a href="{{ route('kategori-transaksi.index') }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ request()->routeIs('kategori-transaksi.*') ? 'bg-white text-primary-950 shadow-sm' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                        </svg>
                        <span>Kategori</span>
                    </a>

                    <a href="{{ route('cabang.index') }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ request()->routeIs('cabang.*') ? 'bg-white text-primary-950 shadow-sm' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.25m16.5 0h2.25m-15 0v-7.5A2.25 2.25 0 016 11.25h3a2.25 2.25 0 012.25 2.25V21m-6 0h6"/>
                        </svg>
                        <span>Cabang</span>
                    </a>
                    @endif
                </nav>

                <!-- Right: Dark Mode Toggle & Quick Actions -->
                <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                    
                    <!-- THEME TOGGLE BUTTON (Light / Dark) -->
                    <button type="button"
                            onclick="toggleTheme()"
                            class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white/10 hover:bg-white/20 active:scale-95 text-yellow-300 dark:text-yellow-400 flex items-center justify-center transition border border-white/15 cursor-pointer"
                            title="Ganti Tema (Terang / Gelap)"
                            aria-label="Toggle Theme">
                        <!-- Sun Icon (shown in dark mode) -->
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 hidden dark:block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                        </svg>
                        <!-- Moon Icon (shown in light mode) -->
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 block dark:hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                        </svg>
                    </button>

                    <!-- Shortcut Transaksi -->
                    <a href="{{ route('transaksi.menu') }}"
                       class="bg-white/15 hover:bg-white/25 active:scale-95 text-white px-2.5 sm:px-3 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all shadow-sm flex items-center gap-1.5 border border-white/10">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        <span class="hidden sm:inline">Transaksi</span>
                    </a>

                    <!-- Logout Button -->
                    <button type="button" 
                            onclick="event.preventDefault(); if(confirm('Apakah Anda yakin ingin keluar?')) document.getElementById('logout-form').submit();"
                            class="hidden lg:flex items-center justify-center p-2 rounded-xl text-blue-200 hover:text-white hover:bg-white/10 transition-colors border border-transparent hover:border-white/10"
                            title="Keluar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                        </svg>
                    </button>
                </div>

            </div>

            <!-- Cabang Quick Selector Pill Bar -->
            @if($isAdminRole && $listWarungs->count() > 1)
                <div class="pb-2 pt-0.5 border-t border-white/10">
                    <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-0.5">
                        <span class="text-[10px] font-bold text-primary-200 uppercase tracking-wider shrink-0 mr-1">Cabang:</span>
                        
                        @foreach($listWarungs as $warung)
                            @php $isActive = session('id_warung') == $warung->id; @endphp
                            <form method="POST" action="{{ route('cabang.pilih', $warung->id) }}" class="shrink-0">
                                @csrf
                                <button type="submit"
                                        class="px-2.5 py-1 rounded-lg text-xs transition-all flex items-center gap-1 cursor-pointer
                                        {{ $isActive 
                                            ? 'bg-white text-primary-950 font-bold shadow-sm' 
                                            : 'bg-white/10 text-blue-100 hover:bg-white/20 hover:text-white border border-white/10 font-medium' }}">
                                    <span>{{ $warung->nama_warung }}</span>
                                    @if($isActive)
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    @endif
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </header>

    <!-- CONTENT UTAMA -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-3 sm:px-6 lg:px-8 py-3 sm:py-5 pb-28 lg:pb-8">
        @yield('content')
    </main>

    <!-- BOTTOM NAVIGATION (HP dan tablet) -->
    <nav aria-label="Menu Navigasi Bawah" class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-t border-slate-200/80 shadow-mobile-nav pb-safe" x-data="{ menu: false }">
        @if($isAdminRole)
        <div x-show="menu" x-cloak @click.outside="menu = false" class="absolute bottom-full left-3 right-3 mb-2 rounded-2xl border border-slate-200 bg-white p-2.5 shadow-xl">
            <div class="grid grid-cols-2 gap-1.5 text-xs font-semibold">
                <a href="{{ route('laporan-harian.index') }}" class="rounded-xl px-3 py-2.5 flex items-center gap-2 {{ request()->routeIs('laporan-harian.*') ? 'bg-primary-50 text-primary-800' : 'text-slate-700 hover:bg-slate-50' }}">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Laporan Kas Harian</span>
                </a>
                <a href="{{ route('manajemen-kasir.index') }}" class="rounded-xl px-3 py-2.5 flex items-center gap-2 {{ request()->routeIs('manajemen-kasir.*') ? 'bg-primary-50 text-primary-800' : 'text-slate-700 hover:bg-slate-50' }}">
                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                    <span>Kelola Kasir</span>
                </a>
                <a href="{{ route('kas.index') }}" class="rounded-xl px-3 py-2.5 flex items-center gap-2 {{ request()->routeIs('kas.*') ? 'bg-primary-50 text-primary-800' : 'text-slate-700 hover:bg-slate-50' }}">
                    <span>Kas & Rekening</span>
                </a>
                <a href="{{ route('kategori-transaksi.index') }}" class="rounded-xl px-3 py-2.5 flex items-center gap-2 {{ request()->routeIs('kategori-transaksi.*') ? 'bg-primary-50 text-primary-800' : 'text-slate-700 hover:bg-slate-50' }}">
                    <span>Kategori Transaksi</span>
                </a>
                <a href="{{ route('biaya-admin.index') }}" class="rounded-xl px-3 py-2.5 flex items-center gap-2 {{ request()->routeIs('biaya-admin.*') ? 'bg-primary-50 text-primary-800' : 'text-slate-700 hover:bg-slate-50' }}">
                    <span>Biaya Admin</span>
                </a>
                <a href="{{ route('item-jualan.index') }}" class="rounded-xl px-3 py-2.5 flex items-center gap-2 {{ request()->routeIs('item-jualan.*') ? 'bg-primary-50 text-primary-800' : 'text-slate-700 hover:bg-slate-50' }}">
                    <span>Barang & Jasa</span>
                </a>
                <a href="{{ route('cabang.index') }}" class="rounded-xl px-3 py-2.5 flex items-center gap-2 {{ request()->routeIs('cabang.*') ? 'bg-primary-50 text-primary-800' : 'text-slate-700 hover:bg-slate-50' }}">
                    <span>Kelola Cabang</span>
                </a>
                <a href="{{ route('laporan.index') }}" class="rounded-xl px-3 py-2.5 flex items-center gap-2 {{ request()->routeIs('laporan.index') ? 'bg-primary-50 text-primary-800' : 'text-slate-700 hover:bg-slate-50' }}">
                    <span>Laporan Periode</span>
                </a>
                <div class="col-span-2 pt-1 border-t border-slate-100">
                    <button type="button" onclick="event.preventDefault(); if(confirm('Apakah Anda yakin ingin keluar?')) document.getElementById('logout-form').submit();" class="w-full rounded-xl px-3 py-2 text-left font-bold text-rose-700 hover:bg-rose-50 flex items-center gap-2">
                        <span>Keluar Akun</span>
                    </button>
                </div>
            </div>
        </div>
        @endif

        <div class="mx-auto grid grid-cols-4 max-w-lg">
            @if($isAdminRole)
            <a href="{{ route('dashboard') }}" class="flex min-h-14 flex-col items-center justify-center gap-0.5 {{ request()->routeIs('dashboard') ? 'text-primary-600 dark:text-primary-400 font-bold' : 'text-slate-500 font-medium' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v4.875h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                <span class="text-[11px]">Beranda</span>
            </a>
            @endif

            <a href="{{ route('transaksi.menu') }}" class="flex min-h-14 flex-col items-center justify-center gap-0.5 {{ $isTransaksiActive && !request()->routeIs('transaksi.index') ? 'text-primary-600 dark:text-primary-400 font-bold' : 'text-slate-500 font-medium' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 6.75h15m-15 5.25h15m-15 5.25h15"/></svg>
                <span class="text-[11px]">Transaksi</span>
            </a>

            <a href="{{ route('transaksi.index') }}" class="flex min-h-14 flex-col items-center justify-center gap-0.5 {{ request()->routeIs('transaksi.index') ? 'text-primary-600 dark:text-primary-400 font-bold' : 'text-slate-500 font-medium' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span class="text-[11px]">Riwayat</span>
            </a>

            @if($isAdminRole)
            <button type="button" @click="menu = !menu" class="flex min-h-14 flex-col items-center justify-center gap-0.5 text-slate-500 font-medium cursor-pointer" :class="menu ? 'text-primary-600 dark:text-primary-400 font-bold' : ''">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                <span class="text-[11px]">Menu</span>
            </button>
            @else
            <a href="{{ route('laporan-harian.index') }}" class="flex min-h-14 flex-col items-center justify-center gap-0.5 {{ request()->routeIs('laporan-harian.*') ? 'text-primary-600 dark:text-primary-400 font-bold' : 'text-slate-500 font-medium' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-[11px]">Laporan Kas</span>
            </a>
            @endif
        </div>
    </nav>

    <!-- Hidden Form Logout -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

</body>
</html>
