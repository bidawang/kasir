<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>{{ $title ?? 'Agen BRILink' }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: "#eef6ff",
                            100: "#d6e9fe",
                            200: "#b5d8fe",
                            300: "#84bffd",
                            400: "#499efb",
                            500: "#1e82f7",
                            600: "#0b63ce",
                            700: "#084fa6",
                            800: "#094287",
                            900: "#0d376d",
                            950: "#082347",
                        }
                    },
                    boxShadow: {
                        'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02)',
                        'mobile-nav': '0 -2px 10px rgba(0, 0, 0, 0.05)'
                    }
                }
            }
        }
    </script>

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        .pb-safe {
            padding-bottom: env(safe-area-inset-bottom, 0px);
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased selection:bg-primary-500 selection:text-white flex flex-col min-h-screen">

    @php
        // Ambil daftar cabang yang berhak diakses
        $userRole = auth()->user()->role ?? '';
        $listWarungs = \App\Models\Kasir::when(!in_array($userRole, ['owner', 'admin']), function ($q) {
            $q->where('id_user', auth()->id());
        })->orderBy('id')->get();

        // Auto-set session cabang pertama jika session masih kosong
        if (!session('id_warung') && $listWarungs->isNotEmpty()) {
            session(['id_warung' => $listWarungs->first()->id]);
        }

        // Ambil objek cabang aktif berdasarkan session
        $cabangAktif = $listWarungs->firstWhere('id', session('id_warung')) 
            ?? \App\Models\Kasir::find(session('id_warung'));
            
        // Helper untuk cek keaktifan menu transaksi
        $isTransaksiActive = request()->routeIs('transaksi.index*') || request()->routeIs('transaksi.setor*') || request()->routeIs('transaksi.tarik*');
    @endphp

    <!-- HEADER UTAMA (Sticky Top) -->
    <header class="sticky top-0 z-40 bg-gradient-to-r from-primary-900 via-primary-800 to-primary-700 text-white shadow-md border-b border-primary-600/30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-14 sm:h-16 gap-3">
                
                <!-- Left: Logo & Cabang Info -->
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 shrink-0 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center font-black text-white text-xs sm:text-sm tracking-wider shadow-inner">
                        BL
                    </div>

                    <div class="min-w-0 leading-tight">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[9px] sm:text-[10px] font-extrabold tracking-widest text-primary-200 uppercase">BRILink</span>
                            @if($cabangAktif)
                                <span class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Active
                                </span>
                            @endif
                        </div>
                        <h1 class="text-xs sm:text-sm md:text-base font-bold text-white truncate max-w-[110px] sm:max-w-[200px] md:max-w-xs">
                            {{ $cabangAktif?->nama_warung ?? 'Pilih Cabang' }}
                        </h1>
                    </div>
                </div>

                <!-- Center Nav Desktop (Tablet / PC View) -->
<nav class="hidden md:flex items-center gap-1 bg-white/10 p-1 rounded-xl backdrop-blur-sm border border-white/10">

    {{-- Beranda --}}
    <a href="{{ route('dashboard') }}" 
       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-2 {{ request()->routeIs('dashboard') ? 'bg-white text-primary-900 shadow-sm' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">

        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v4.875h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
        </svg>

        Beranda
    </a>


    {{-- Riwayat --}}
    <a href="{{ route('transaksi.index') }}" 
       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-2 {{ $isTransaksiActive ? 'bg-white text-primary-900 shadow-sm' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">

        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.66 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.66m-5.801 0c-3.04.15-5.5 2.66-5.5 5.75v9.5a2.25 2.25 0 002.25 2.25h13.5A2.25 2.25 0 0021 18.25v-9.5c0-3.09-2.46-5.6-5.5-5.75m-5.801 0a48.108 48.108 0 015.801 0"/>
        </svg>

        Riwayat
    </a>


    {{-- Kas --}}
    <a href="{{ route('kas.index') }}" 
       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-2 {{ request()->routeIs('kas.*') ? 'bg-white text-primary-900 shadow-sm' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">

        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3"/>
        </svg>

        Kas
    </a>

    {{-- Cabang --}}
    @if(in_array($userRole, ['admin', 'owner']))
        <a href="{{ route('cabang.index') }}" 
           class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-2 {{ request()->routeIs('cabang.*') ? 'bg-white text-primary-900 shadow-sm' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">

            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.25m16.5 0h2.25m-15 0v-7.5A2.25 2.25 0 016 11.25h3a2.25 2.25 0 012.25 2.25V21m-6 0h6"/>
            </svg>

            Cabang
        </a>
    @endif

</nav>

                <!-- Right: Action Buttons (Setor / Tarik) & Logout -->
                <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                    
                    <!-- Tombol Setor Tunai -->
                    <a href="{{ route('transaksi.setor') }}"
                       class="bg-emerald-500 hover:bg-emerald-600 active:scale-95 text-white px-2.5 py-1.5 sm:px-3 sm:py-2 rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-1.5">
                        <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l-4.5-4.5M12 19.5l4.5-4.5"/>
                        </svg>
                        <span class="hidden sm:inline">Setor Tunai</span>
                        <span class="sm:hidden">Setor</span>
                    </a>

                    <!-- Tombol Tarik Tunai -->
                    <a href="{{ route('transaksi.tarik') }}"
                       class="bg-amber-500 hover:bg-amber-600 active:scale-95 text-white px-2.5 py-1.5 sm:px-3 sm:py-2 rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-1.5">
                        <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5v-15m0 0l-4.5 4.5M12 4.5l4.5 4.5"/>
                        </svg>
                        <span class="hidden sm:inline">Tarik Tunai</span>
                        <span class="sm:hidden">Tarik</span>
                    </a>

                    <button type="button" 
                            onclick="event.preventDefault(); if(confirm('Yakin ingin keluar dari sistem?')) document.getElementById('logout-form').submit();"
                            class="hidden md:flex items-center justify-center p-2 rounded-xl text-blue-200 hover:text-white hover:bg-white/10 transition-colors ml-1"
                            title="Keluar / Logout">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                        </svg>
                    </button>
                </div>

            </div>

            <!-- Cabang Quick Selector Pill Bar -->
            @if($listWarungs->count() > 1)
                <div class="pb-2 pt-0.5 border-t border-white/10">
                    <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-0.5">
                        <span class="text-[9px] font-bold text-primary-200 uppercase tracking-wider shrink-0 mr-1">Cabang:</span>
                        
                        @foreach($listWarungs as $warung)
                            @php $isActive = session('id_warung') == $warung->id; @endphp
                            <form method="POST" action="{{ route('cabang.pilih', $warung->id) }}" class="shrink-0">
                                @csrf
                                <button type="submit"
                                        class="px-2.5 py-0.5 rounded-md text-[11px] transition-all flex items-center gap-1
                                        {{ $isActive 
                                            ? 'bg-white text-primary-900 font-bold shadow-sm' 
                                            : 'bg-white/10 text-blue-100 hover:bg-white/20 hover:text-white border border-white/10 font-medium' }}">
                                    <span>{{ $warung->nama_warung }}</span>
                                    @if($isActive)
                                        <span class="w-1.5 h-1.5 rounded-full bg-primary-600"></span>
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
    <main class="flex-1 max-w-7xl w-full mx-auto px-3 sm:px-6 lg:px-8 py-3 sm:py-6 pb-28 md:pb-8">
        @yield('content')
    </main>

    <!-- BOTTOM NAVIGATION (Hanya Tampil di Mobile/Tablet < md) -->
    <nav aria-label="Menu Navigasi Bawah" class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-t border-slate-200/80 shadow-mobile-nav pb-safe">
        <div class="max-w-md mx-auto px-1">
            <div class="grid {{ in_array($userRole, ['admin', 'owner']) ? 'grid-cols-5' : 'grid-cols-4' }} items-center h-13">

                <!-- 1. Beranda -->
                <a href="{{ route('dashboard') }}"
                   class="flex flex-col items-center justify-center py-1 transition-colors group {{ request()->routeIs('dashboard') ? 'text-primary-600 font-bold' : 'text-slate-400 hover:text-slate-600 font-medium' }}">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 transition-transform group-active:scale-90" fill="none" stroke="currentColor" stroke-width="{{ request()->routeIs('dashboard') ? '2.5' : '2' }}" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v4.875h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                    </svg>
                    <span class="text-[9px] sm:text-[10px] tracking-tight mt-0.5">Beranda</span>
                </a>

                <!-- 2. Riwayat Transaksi -->
                <a href="{{ route('transaksi.index') }}"
                   class="flex flex-col items-center justify-center py-1 transition-colors group {{ $isTransaksiActive ? 'text-primary-600 font-bold' : 'text-slate-400 hover:text-slate-600 font-medium' }}">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 transition-transform group-active:scale-90" fill="none" stroke="currentColor" stroke-width="{{ $isTransaksiActive ? '2.5' : '2' }}" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.66 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.66m-5.801 0c-3.04.15-5.5 2.66-5.5 5.75v9.5a2.25 2.25 0 002.25 2.25h13.5A2.25 2.25 0 0021 18.25v-9.5c0-3.09-2.46-5.6-5.5-5.75m-5.801 0a48.108 48.108 0 015.801 0"/>
                    </svg>
                    <span class="text-[9px] sm:text-[10px] tracking-tight mt-0.5">Riwayat</span>
                </a>

                <!-- 3. Kas -->
                <a href="{{ route('kas.index') }}"
                   class="flex flex-col items-center justify-center py-1 transition-colors group {{ request()->routeIs('kas.*') ? 'text-primary-600 font-bold' : 'text-slate-400 hover:text-slate-600 font-medium' }}">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 transition-transform group-active:scale-90" fill="none" stroke="currentColor" stroke-width="{{ request()->routeIs('kas.*') ? '2.5' : '2' }}" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3"/>
                    </svg>
                    <span class="text-[9px] sm:text-[10px] tracking-tight mt-0.5">Kas</span>
                </a>

                <!-- 4. Cabang (Admin / Owner Only) -->
                @if(in_array($userRole, ['admin', 'owner']))
                    <a href="{{ route('cabang.index') }}"
                       class="flex flex-col items-center justify-center py-1 transition-colors group {{ request()->routeIs('cabang.*') ? 'text-primary-600 font-bold' : 'text-slate-400 hover:text-slate-600 font-medium' }}">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 transition-transform group-active:scale-90" fill="none" stroke="currentColor" stroke-width="{{ request()->routeIs('cabang.*') ? '2.5' : '2' }}" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.25m16.5 0h2.25m-15 0v-7.5A2.25 2.25 0 016 11.25h3a2.25 2.25 0 012.25 2.25V21m-6 0h6"/>
                        </svg>
                        <span class="text-[9px] sm:text-[10px] tracking-tight mt-0.5">Cabang</span>
                    </a>
                @endif

                <!-- 5. Keluar -->
                <button type="button" 
                        onclick="event.preventDefault(); if(confirm('Yakin ingin keluar dari sistem?')) document.getElementById('logout-form').submit();"
                        class="flex flex-col items-center justify-center py-1 text-slate-400 hover:text-rose-500 transition-colors group font-medium">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 transition-transform group-active:scale-90" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                    </svg>
                    <span class="text-[9px] sm:text-[10px] tracking-tight mt-0.5">Keluar</span>
                </button>

            </div>
        </div>
    </nav>

    <!-- Hidden Form Logout -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

</body>
</html>