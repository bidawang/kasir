<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="color-scheme" content="light dark">
    <title>{{ $title ?? 'Agent Kas - Sistem Kasir Perbankan' }}</title>

    <!-- Theme & Compact Mode Initialization Script (Prevent FOUC) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }

            // Inisialisasi Mode Ringkas (Sembunyikan teks penjelasan panjang)
            const savedCompact = localStorage.getItem('compact_mode');
            if (savedCompact === 'true') {
                document.documentElement.classList.add('compact-mode');
            }
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Play CDN - Ringan & Tanpa Perlu Build NPM) -->
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
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            950: '#172554',
                        }
                    },
                    boxShadow: {
                        'soft': '0 2px 15px -3px rgb(0 0 0 / 0.05), 0 4px 6px -2px rgb(0 0 0 / 0.02)',
                        'mobile-nav': '0 -2px 10px rgb(0 0 0 / 0.08)',
                        '2xs': '0 1px 2px 0 rgb(0 0 0 / 0.05)',
                        'xs': '0 1px 2px 0 rgb(0 0 0 / 0.05)',
                    }
                }
            }
        };
    </script>

    <!-- Alpine.js & Plugins via CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        html { font-size: 16px; scroll-behavior: smooth; }
        body { -webkit-tap-highlight-color: transparent; }
        input, select, textarea { font-size: 0.95rem; }
        button, a, input, select, textarea { touch-action: manipulation; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .pb-safe { padding-bottom: max(env(safe-area-inset-bottom, 0px), 12px); }
        [x-cloak] { display: none !important; }

        /* TEMA GELAP (DARK MODE)
           Hanya untuk area konten (<main>). Header & nav bawah sudah punya varian dark: sendiri. */
        html.dark { color-scheme: dark; }
        html.dark body, html.dark { background-color: #0b1120 !important; color: #f1f5f9 !important; }
        html.dark main [class~="bg-white"] { background-color: #162032 !important; border-color: #243248 !important; }
        html.dark main [class~="bg-slate-50"], html.dark main [class~="bg-slate-100"] { background-color: #0d1526 !important; }
        html.dark main [class~="text-slate-900"], html.dark main [class~="text-slate-800"] { color: #f8fafc !important; }
        html.dark main [class~="text-slate-700"], html.dark main [class~="text-slate-600"] { color: #cbd5e1 !important; }
        html.dark main [class~="text-slate-500"], html.dark main [class~="text-slate-400"] { color: #94a3b8 !important; }
        html.dark main [class~="border-slate-100"], html.dark main [class~="border-slate-200"], html.dark main [class~="border-slate-300"] { border-color: #243248 !important; }
        html.dark main [class~="divide-slate-100"] > :not([hidden]) ~ :not([hidden]),
        html.dark main [class~="divide-slate-200"] > :not([hidden]) ~ :not([hidden]) { border-color: #243248 !important; }
        html.dark main input, html.dark main select, html.dark main textarea {
            background-color: #1a273e !important;
            border-color: #2e4161 !important;
            color: #f8fafc !important;
        }

        /* MODE RINGKAS (Minimal View / Tanpa Penjelasan Panjang) */
        html.compact-mode .helper-text,
        html.compact-mode .sub-explanation,
        html.compact-mode [data-explanation],
        html.compact-mode .lead-description {
            display: none !important;
        }
        html.compact-mode .compact-p-reduce {
            padding-top: 0.35rem !important;
            padding-bottom: 0.35rem !important;
        }
    </style>

    <!-- Global Helpers & Directives (Leading Zero Cleaner & Rupiah Formatter) -->
    <script>
        window.stripLeadingZeros = function(value) {
            if (value === null || value === undefined) return '';
            return value.toString().replace(/^0+(?=\d)/, '');
        };

        window.cleanDigits = function(value) {
            if (value === null || value === undefined) return '';
            const raw = value.toString().replace(/[^0-9]/g, '');
            return raw.replace(/^0+(?=\d)/, '');
        };

        window.formatRupiah = function(value) {
            if (value === null || value === undefined || value === '') return '';
            const clean = window.cleanDigits(value);
            if (!clean && clean !== '0') return '';
            const num = parseInt(clean, 10);
            return isNaN(num) ? '' : new Intl.NumberFormat('id-ID').format(num);
        };

        window.unformatRupiah = function(value) {
            if (!value) return 0;
            const clean = window.cleanDigits(value);
            return parseInt(clean, 10) || 0;
        };

        window.toggleTheme = function() {
            const isDark = document.documentElement.classList.contains('dark');
            if (isDark) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
            window.dispatchEvent(new CustomEvent('theme-changed', {
                detail: { isDark: !isDark }
            }));
        };

        window.toggleCompactMode = function() {
            const isCompact = document.documentElement.classList.toggle('compact-mode');
            localStorage.setItem('compact_mode', isCompact ? 'true' : 'false');
            window.updateCompactBtnState();
            window.dispatchEvent(new CustomEvent('compact-mode-changed', {
                detail: { isCompact }
            }));
        };

        window.updateCompactBtnState = function() {
            const btn = document.getElementById('compact-toggle-btn');
            const label = document.getElementById('compact-btn-label');
            const isCompact = document.documentElement.classList.contains('compact-mode');
            if (btn) {
                if (isCompact) {
                    btn.classList.add('bg-amber-400', 'text-slate-950', 'font-black', 'border-amber-300');
                    btn.classList.remove('bg-white/10', 'text-blue-100', 'border-white/15');
                    if (label) label.textContent = 'Ringkas: ON';
                } else {
                    btn.classList.remove('bg-amber-400', 'text-slate-950', 'font-black', 'border-amber-300');
                    btn.classList.add('bg-white/10', 'text-blue-100', 'border-white/15');
                    if (label) label.textContent = 'Ringkas';
                }
            }
        };

        document.addEventListener('DOMContentLoaded', window.updateCompactBtnState);

        document.addEventListener('alpine:init', () => {
            Alpine.directive('rupiah', (el) => {
                el.addEventListener('input', (e) => {
                    const clean = window.cleanDigits(e.target.value);
                    e.target.value = clean ? window.formatRupiah(clean) : '';
                });
                el.addEventListener('focus', (e) => {
                    setTimeout(() => {
                        if (e.target === document.activeElement && typeof e.target.select === 'function') {
                            e.target.select();
                        }
                    }, 30);
                });
            });
        });

        // Auto-clean & format input angka depan 0: '010' -> '10', '0000010' -> '10'
        // Serta format otomatis ke Rupiah (contoh: '1.000', '10.000')
        function isPhoneOrAccountField(target) {
            if (!target) return false;
            if (target.type === 'tel' || target.type === 'password' || target.type === 'email') return true;
            const nameOrId = ((target.name || '') + ' ' + (target.id || '')).toLowerCase();
            return /(^|[^a-z])(telepon|phone|hp|whatsapp|wa|no_?rek|norek|rekening|account)([^a-z]|$)/.test(nameOrId);
        }

        function sanitizeNumberTarget(target) {
            if (!target || target.tagName !== 'INPUT' || target._isSanitizing) return;
            if (isPhoneOrAccountField(target)) return;

            // 1. Input berformat Rupiah (.input-rupiah atau data-rupiah)
            if (target.matches('.input-rupiah, [data-rupiah]')) {
                const clean = window.cleanDigits(target.value);
                const formatted = clean ? window.formatRupiah(clean) : (target.value === '0' ? '0' : '');
                if (target.value !== formatted) {
                    target._isSanitizing = true;
                    target.value = formatted;
                    target.dispatchEvent(new Event('input', { bubbles: true }));
                    target._isSanitizing = false;
                }
                return;
            }

            // 2. Input tipe number, inputmode numeric, atau kolom angka (jumlah, stok, pecahan, qty)
            const isNumericInput = target.type === 'number'
                || target.getAttribute('inputmode') === 'numeric'
                || /(^|\b|_)(jumlah|qty|stok|nominal|pecahan|total|harga|tarif|biaya)(\b|_|\[)/i.test(target.name || target.id || '');

            if (isNumericInput && typeof target.value === 'string') {
                if (/^0+\d+/.test(target.value)) {
                    const stripped = target.value.replace(/^0+(?=\d)/, '');
                    if (target.value !== stripped) {
                        target._isSanitizing = true;
                        target.value = stripped;
                        target.dispatchEvent(new Event('input', { bubbles: true }));
                        target._isSanitizing = false;
                    }
                }
            }
        }

        document.addEventListener('input', function(e) {
            sanitizeNumberTarget(e.target);
        }, true);

        document.addEventListener('change', function(e) {
            sanitizeNumberTarget(e.target);
        }, true);

        document.addEventListener('blur', function(e) {
            sanitizeNumberTarget(e.target);
        }, true);

        document.addEventListener('focus', function(e) {
            const target = e.target;
            if (!target || target.tagName !== 'INPUT') return;
            if (target.type === 'number' || target.getAttribute('inputmode') === 'numeric' || target.matches('.input-rupiah, [data-rupiah]')) {
                setTimeout(() => {
                    if (target === document.activeElement && typeof target.select === 'function') {
                        target.select();
                    }
                }, 30);
            }
        }, true);
    </script>
</head>
<body class="bg-slate-100 text-slate-800 antialiased selection:bg-primary-500 selection:text-white flex flex-col min-h-screen">

    @php
        $userRole = auth()->user()?->role ?? '';
        $isAdminRole = in_array($userRole, ['admin', 'owner'], true);

        // Ambil daftar cabang yang berhak diakses (hanya kolom yang diperlukan)
        $listWarungs = \App\Models\Kasir::query()
            ->when($userRole === 'kasir', function ($query) {
                $query->where(function ($query) {
                    $query->where('id_user', auth()->id())
                        ->orWhereHas('users', fn ($users) => $users->where('user.id', auth()->id()));
                });
            })
            ->when($isAdminRole, fn ($query) => $query->where('id_user', auth()->id()))
            // Role tidak dikenal: tidak boleh melihat cabang apa pun
            ->when(!$isAdminRole && $userRole !== 'kasir', fn ($query) => $query->whereRaw('1 = 0'))
            ->orderBy('id')
            ->get(['id', 'nama_warung', 'bank_utama', 'id_user']);

        // Pastikan session cabang valid & milik user ini
        if ($listWarungs->isNotEmpty() && !$listWarungs->contains('id', session('id_warung'))) {
            session(['id_warung' => $listWarungs->first()->id]);
        }

        // Ambil objek cabang aktif berdasarkan session
        $cabangAktif = $listWarungs->firstWhere('id', session('id_warung'));

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
                    <a href="{{ route('dashboard') }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ request()->routeIs('dashboard') ? 'bg-white text-primary-950 shadow-sm' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v4.875h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                        </svg>
                        <span>Beranda</span>
                    </a>

                    <a href="{{ route('transaksi.menu') }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ ($isTransaksiActive && !request()->routeIs('transaksi.index') && !request()->routeIs('transaksi.penjualan-item.*')) ? 'bg-white text-primary-950 shadow-sm' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 6.75h15m-15 5.25h15m-15 5.25h15"/>
                        </svg>
                        <span>Transaksi</span>
                    </a>

                    <a href="{{ route('transaksi.penjualan-item.create') }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ request()->routeIs('transaksi.penjualan-item.*') ? 'bg-white text-primary-950 shadow-sm' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
                        </svg>
                        <span>Jual Barang</span>
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

                    <a href="{{ route('owner.monitoring') }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-extrabold transition-all flex items-center gap-1.5 {{ request()->routeIs('owner.monitoring*') ? 'bg-amber-400 text-slate-950 shadow-sm' : 'text-amber-300 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6"/>
                        </svg>
                        <span>Pantau Cabang</span>
                    </a>
                    @endif
                </nav>

                <!-- Right: Dark Mode Toggle & Quick Actions -->
                <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">

                    <!-- BUTTON MODE RINGKAS (Kurangi Teks & Penjelasan Panjang) -->
                    <button type="button"
                            onclick="toggleCompactMode()"
                            id="compact-toggle-btn"
                            class="h-9 sm:h-10 px-2 sm:px-2.5 rounded-xl bg-white/10 hover:bg-white/20 active:scale-95 text-blue-100 flex items-center justify-center gap-1.5 transition border border-white/15 cursor-pointer"
                            title="Mode Ringkas: Sembunyikan penjelasan & teks panduan agar layar bersih dan tidak pusing"
                            aria-label="Toggle Compact Mode">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
                        </svg>
                        <span class="text-[10px] sm:text-xs font-bold hidden sm:inline" id="compact-btn-label">Ringkas</span>
                    </button>

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

            <!-- Cabang Quick Selector Pill Bar (admin & kasir dengan lebih dari 1 cabang) -->
            @if($listWarungs->count() > 1)
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
    <nav aria-label="Menu Navigasi Bawah" class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-t border-slate-200/80 dark:border-slate-800 shadow-mobile-nav pb-safe" x-data="{ menu: false }">
        <!-- MODAL MENU POPUP MOBILE -->
        <div x-show="menu" x-cloak @click.outside="menu = false" class="absolute bottom-full left-3 right-3 mb-2 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-3 shadow-2xl space-y-2">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                <span class="text-xs font-black uppercase tracking-wider text-slate-500">Menu Akses Cepat</span>
                <button type="button" @click="menu = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Toggles Cepat: Mode Ringkas & Tema -->
            <div class="grid grid-cols-2 gap-2 pb-2 border-b border-slate-100 dark:border-slate-800">
                <button type="button" onclick="toggleCompactMode()" class="px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 flex items-center justify-center gap-1.5 border border-slate-200 dark:border-slate-700 cursor-pointer">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12"/></svg>
                    <span>Mode Ringkas</span>
                </button>
                <button type="button" onclick="toggleTheme()" class="px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 flex items-center justify-center gap-1.5 border border-slate-200 dark:border-slate-700 cursor-pointer">
                    <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/></svg>
                    <span>Terang / Gelap</span>
                </button>
            </div>

            @if($isAdminRole)
            <div class="grid grid-cols-2 gap-1.5 text-xs font-semibold">
                <a href="{{ route('transaksi.penjualan-item.create') }}" class="rounded-xl px-3 py-2 flex items-center gap-2 {{ request()->routeIs('transaksi.penjualan-item.*') ? 'bg-primary-50 text-primary-800 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <svg class="w-4 h-4 text-purple-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                    <span>Jual Barang</span>
                </a>
                <a href="{{ route('laporan-harian.index') }}" class="rounded-xl px-3 py-2 flex items-center gap-2 {{ request()->routeIs('laporan-harian.*') ? 'bg-primary-50 text-primary-800 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Laporan Kas</span>
                </a>
                <a href="{{ route('manajemen-kasir.index') }}" class="rounded-xl px-3 py-2 flex items-center gap-2 {{ request()->routeIs('manajemen-kasir.*') ? 'bg-primary-50 text-primary-800 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <svg class="w-4 h-4 text-primary-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                    <span>Kelola Kasir</span>
                </a>
                <a href="{{ route('kas.index') }}" class="rounded-xl px-3 py-2 flex items-center gap-2 {{ request()->routeIs('kas.*') ? 'bg-primary-50 text-primary-800 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <svg class="w-4 h-4 text-indigo-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3"/></svg>
                    <span>Kas & Rekening</span>
                </a>
                <a href="{{ route('cabang.index') }}" class="rounded-xl px-3 py-2 flex items-center gap-2 {{ request()->routeIs('cabang.*') ? 'bg-primary-50 text-primary-800 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.25m16.5 0h2.25m-15 0v-7.5A2.25 2.25 0 016 11.25h3a2.25 2.25 0 012.25 2.25V21m-6 0h6"/></svg>
                    <span>Kelola Cabang</span>
                </a>
                <a href="{{ route('item-jualan.index') }}" class="rounded-xl px-3 py-2 flex items-center gap-2 {{ request()->routeIs('item-jualan.*') ? 'bg-primary-50 text-primary-800 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <svg class="w-4 h-4 text-purple-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l2.4 12.2a2 2 0 002 1.6h8.8a2 2 0 002-1.6L22 8H6M10 21h.01M18 21h.01"/></svg>
                    <span>Barang & Jasa</span>
                </a>
                <a href="{{ route('kategori-transaksi.index') }}" class="rounded-xl px-3 py-2 flex items-center gap-2 {{ request()->routeIs('kategori-transaksi.*') ? 'bg-primary-50 text-primary-800 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <svg class="w-4 h-4 text-sky-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                    <span>Kategori Transaksi</span>
                </a>
                <a href="{{ route('biaya-admin.index') }}" class="rounded-xl px-3 py-2 flex items-center gap-2 {{ request()->routeIs('biaya-admin.*') ? 'bg-primary-50 text-primary-800 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m4-9H9.5a2.5 2.5 0 000 5h5a2.5 2.5 0 010 5H8"/><circle cx="12" cy="12" r="9"/></svg>
                    <span>Biaya Admin</span>
                </a>
                <a href="{{ route('laporan.index') }}" class="rounded-xl px-3 py-2 flex items-center gap-2 {{ request()->routeIs('laporan.index') ? 'bg-primary-50 text-primary-800 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Laporan Periode</span>
                </a>
                <a href="{{ route('owner.monitoring') }}" class="col-span-2 rounded-xl px-3 py-2.5 flex items-center gap-2 bg-amber-500/15 text-amber-900 dark:text-amber-300 border border-amber-300 dark:border-amber-700/60 font-black">
                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6"/></svg>
                    <span>Pusat Monitoring Semua Cabang</span>
                </a>
            </div>
            @else
            <!-- Menu Akses Cepat Kasir -->
            <div class="grid grid-cols-2 gap-1.5 text-xs font-semibold">
                <a href="{{ route('transaksi.menu') }}" class="rounded-xl px-3 py-2 flex items-center gap-2 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                    <svg class="w-4 h-4 text-primary-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    <span>Transaksi Baru</span>
                </a>
                <a href="{{ route('transaksi.penjualan-item.create') }}" class="rounded-xl px-3 py-2 flex items-center gap-2 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                    <svg class="w-4 h-4 text-purple-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                    <span>Jual Barang</span>
                </a>
                <a href="{{ route('transaksi.setor') }}" class="rounded-xl px-3 py-2 flex items-center gap-2 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l6-6m-6 6l-6-6"/></svg>
                    <span>Setor Tunai</span>
                </a>
                <a href="{{ route('transaksi.tarik') }}" class="rounded-xl px-3 py-2 flex items-center gap-2 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5v-15m0 0l-6 6m6-6l6 6"/></svg>
                    <span>Tarik Tunai</span>
                </a>
                <a href="{{ route('laporan-harian.index') }}" class="rounded-xl px-3 py-2 flex items-center gap-2 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Laporan Kas Fisik</span>
                </a>
                <a href="{{ route('transaksi.index') }}" class="rounded-xl px-3 py-2 flex items-center gap-2 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                    <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.66 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.66m-5.801 0c-3.04.15-5.5 2.66-5.5 5.75v9.5a2.25 2.25 0 002.25 2.25h13.5A2.25 2.25 0 0021 18.25v-9.5c0-3.09-2.46-5.6-5.5-5.75m-5.801 0a48.108 48.108 0 015.801 0"/></svg>
                    <span>Riwayat Transaksi</span>
                </a>
            </div>
            @endif

            <div class="pt-1 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="event.preventDefault(); if(confirm('Apakah Anda yakin ingin keluar?')) document.getElementById('logout-form').submit();" class="w-full rounded-xl px-3 py-2.5 text-left font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                    <span>Keluar Akun</span>
                </button>
            </div>
        </div>

        <div class="mx-auto grid grid-cols-4 max-w-lg">
            <a href="{{ route('dashboard') }}" class="flex min-h-14 flex-col items-center justify-center gap-0.5 {{ request()->routeIs('dashboard') ? 'text-primary-600 dark:text-primary-400 font-bold' : 'text-slate-500 font-medium' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v4.875h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                <span class="text-[11px]">Beranda</span>
            </a>

            <a href="{{ route('transaksi.menu') }}" class="flex min-h-14 flex-col items-center justify-center gap-0.5 {{ $isTransaksiActive && !request()->routeIs('transaksi.index') ? 'text-primary-600 dark:text-primary-400 font-bold' : 'text-slate-500 font-medium' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 6.75h15m-15 5.25h15m-15 5.25h15"/></svg>
                <span class="text-[11px]">Transaksi</span>
            </a>

            <a href="{{ route('transaksi.index') }}" class="flex min-h-14 flex-col items-center justify-center gap-0.5 {{ request()->routeIs('transaksi.index') ? 'text-primary-600 dark:text-primary-400 font-bold' : 'text-slate-500 font-medium' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span class="text-[11px]">Riwayat</span>
            </a>

            <button type="button" @click="menu = !menu" class="flex min-h-14 flex-col items-center justify-center gap-0.5 text-slate-500 font-medium cursor-pointer" :class="menu ? 'text-primary-600 dark:text-primary-400 font-bold' : ''">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                <span class="text-[11px]">Menu</span>
            </button>
        </div>
    </nav>

    <!-- Hidden Form Logout -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

</body>
</html>