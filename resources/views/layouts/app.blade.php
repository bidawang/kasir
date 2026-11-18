<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="color-scheme" content="light dark">
    <title>{{ $title ?? 'AgentKas - Sistem Kasir Agen Bank' }}</title>

    <!-- Theme & Compact Mode Early Initialization (Prevent FOUC) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }

            const savedCompact = localStorage.getItem('compact_mode');
            if (savedCompact === 'true') {
                document.documentElement.classList.add('compact-mode');
            }
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Play CDN - Ringan & Instan) -->
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
                        // Biru khas BRI (#00529C) sebagai warna utama
                        primary: {
                            50: '#eaf2fa',
                            100: '#d3e4f4',
                            200: '#a7c9e9',
                            300: '#6fa6d8',
                            400: '#3a82c4',
                            500: '#1268b3',
                            600: '#00529c',
                            700: '#004585',
                            800: '#00396d',
                            900: '#002d57',
                            950: '#001b36',
                        },
                        // Aksen oranye BRI (dipakai hemat)
                        accent: {
                            50: '#fff4ec',
                            100: '#ffe4d1',
                            400: '#f8924f',
                            500: '#f37021',
                            600: '#d95c12',
                        }
                    },
                    boxShadow: {
                        'soft': '0 2px 15px -3px rgb(0 0 0 / 0.05), 0 4px 6px -2px rgb(0 0 0 / 0.02)',
                        'mobile-nav': '0 -2px 12px rgb(0 0 0 / 0.06)',
                        'drawer': '4px 0 24px -2px rgb(0 0 0 / 0.25)',
                        '2xs': '0 1px 2px 0 rgb(0 0 0 / 0.04)',
                        'xs': '0 1px 3px 0 rgb(0 0 0 / 0.06)',
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
        html { font-size: 15px; scroll-behavior: smooth; }
        @media (min-width: 640px) { html { font-size: 16px; } }
        body { -webkit-tap-highlight-color: transparent; }
        input, select, textarea { font-size: 0.95rem; }
        button, a, input, select, textarea { touch-action: manipulation; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
.pb-safe { padding-bottom: max(env(safe-area-inset-bottom, 0px), 6px); }
.pt-safe { padding-top: env(safe-area-inset-top, 0px); }        [x-cloak] { display: none !important; }

        /* TEMA GELAP (DARK MODE) */
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

        /* MODE RINGKAS (Minimalist / Compact View - Menyembunyikan teks keterangan & penjelasan) */
        html.compact-mode .helper-text,
        html.compact-mode .sub-explanation,
        html.compact-mode [data-explanation],
        html.compact-mode .lead-description,
        html.compact-mode .compact-hide {
            display: none !important;
        }
        html.compact-mode .compact-p-reduce {
            padding-top: 0.35rem !important;
            padding-bottom: 0.35rem !important;
        }
        html.compact-mode .compact-gap {
            gap: 0.5rem !important;
        }
    </style>

    <!-- KOMPONEN UI AGENTKAS (dipakai di semua halaman agar seragam, compact & profesional) -->
    <style type="text/tailwindcss">
        @layer utilities {
            .no-scrollbar::-webkit-scrollbar { display: none; }
            .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
            .pb-safe { padding-bottom: max(env(safe-area-inset-bottom, 0px), 6px); }
            .pt-safe { padding-top: env(safe-area-inset-top, 0px); }
        }

        @layer components {
            /* Wadah halaman */
            .ak-page      { @apply max-w-3xl mx-auto space-y-3 pb-4; }
            .ak-page-wide { @apply max-w-6xl mx-auto space-y-3 pb-4; }

            /* Header halaman */
            .ak-head      { @apply flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-3.5; }
            .ak-head-icon { @apply w-9 h-9 shrink-0 rounded-xl bg-primary-50 text-primary-600 border border-primary-100 flex items-center justify-center; }
            .ak-back      { @apply w-9 h-9 shrink-0 rounded-xl bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 flex items-center justify-center transition; }
            .ak-title     { @apply text-base sm:text-lg font-black text-slate-900 tracking-tight leading-tight; }
            .ak-desc      { @apply text-[11px] sm:text-xs text-slate-500 font-medium mt-0.5; }
            .ak-section   { @apply text-[11px] font-black uppercase tracking-wider text-slate-500 px-1; }

            /* Kartu */
            .ak-card      { @apply bg-white rounded-2xl border border-slate-200/80 shadow-2xs; }
            .ak-card-body { @apply p-3.5 sm:p-4; }
            .ak-stat      { @apply bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-3; }
            .ak-stat-label{ @apply text-[10px] font-black uppercase tracking-wider text-slate-500; }
            .ak-stat-value{ @apply text-sm sm:text-base font-black text-slate-900 mt-0.5; }
            .ak-hero      { @apply relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 text-white p-4 shadow-md; }

            /* Tombol */
            .ak-btn           { @apply inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold transition active:scale-95 cursor-pointer whitespace-nowrap; }
            .ak-btn-primary   { @apply inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold transition active:scale-95 cursor-pointer whitespace-nowrap bg-primary-600 hover:bg-primary-700 text-white shadow-sm; }
            .ak-btn-secondary { @apply inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold transition active:scale-95 cursor-pointer whitespace-nowrap bg-white hover:bg-slate-50 text-slate-700 border border-slate-200; }
            .ak-btn-soft      { @apply inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold transition active:scale-95 cursor-pointer whitespace-nowrap bg-primary-50 hover:bg-primary-100 text-primary-700 border border-primary-100; }
            .ak-btn-danger    { @apply inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold transition active:scale-95 cursor-pointer whitespace-nowrap bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200; }
            .ak-btn-success   { @apply inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold transition active:scale-95 cursor-pointer whitespace-nowrap bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm; }
            .ak-btn-accent    { @apply inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold transition active:scale-95 cursor-pointer whitespace-nowrap bg-accent-500 hover:bg-accent-600 text-white shadow-sm; }
            .ak-icon-btn      { @apply w-8 h-8 shrink-0 rounded-lg flex items-center justify-center border transition active:scale-95 cursor-pointer; }

            /* Form */
            .ak-label     { @apply block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1; }
            .ak-input     { @apply w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-sm font-medium text-slate-800 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition; }
            .ak-hint      { @apply text-[11px] text-slate-400 mt-1; }
            .ak-fieldset  { @apply p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-3; }
            .ak-actions   { @apply flex items-center gap-2 pt-3 border-t border-slate-100; }

            /* Badge */
            .ak-badge         { @apply inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-bold border; }
            .ak-badge-primary { @apply inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-bold border bg-primary-50 text-primary-700 border-primary-100; }
            .ak-badge-success { @apply inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-bold border bg-emerald-50 text-emerald-700 border-emerald-200; }
            .ak-badge-danger  { @apply inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-bold border bg-rose-50 text-rose-700 border-rose-200; }
            .ak-badge-warning { @apply inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-bold border bg-amber-50 text-amber-800 border-amber-200; }
            .ak-badge-muted   { @apply inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-bold border bg-slate-100 text-slate-600 border-slate-200; }

            /* Alert */
            .ak-alert-success { @apply flex items-start gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2.5 text-xs font-semibold text-emerald-800; }
            .ak-alert-error   { @apply flex items-start gap-2 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2.5 text-xs font-semibold text-rose-800; }
            .ak-alert-danger  { @apply flex items-start gap-2 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2.5 text-xs font-semibold text-rose-800; }
            .ak-alert-warning { @apply flex items-start gap-2 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2.5 text-xs font-semibold text-amber-800; }
            .ak-alert-info    { @apply flex items-start gap-2 rounded-xl border border-primary-100 bg-primary-50 px-3 py-2.5 text-xs font-semibold text-primary-800; }

            /* Tabel & list */
            .ak-table-wrap{ @apply bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-x-auto no-scrollbar; }
            .ak-table     { @apply w-full text-left text-xs sm:text-sm; }
            .ak-table thead { @apply bg-slate-50 border-b border-slate-200; }
            .ak-table th  { @apply px-3 py-2.5 text-[10px] font-black uppercase tracking-wider text-slate-500; }
            .ak-table td  { @apply px-3 py-2.5 border-t border-slate-100 align-middle; }
            .ak-list-item { @apply flex items-center justify-between gap-3 p-3; }
            .ak-empty     { @apply bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-6 text-center text-xs text-slate-400 border-dashed; }
        }

        /* Dark mode untuk komponen */
        html.dark .ak-card, html.dark .ak-table-wrap, html.dark .ak-head, html.dark .ak-stat { background-color: #162032 !important; border-color: #243248 !important; }
        html.dark .ak-btn-secondary { background-color: #162032 !important; border-color: #243248 !important; color: #f1f5f9 !important; }
        html.dark .ak-btn-secondary:hover { background-color: #1e2d45 !important; }
        html.dark .ak-input, html.dark .ak-fieldset { background-color: #1a273e !important; border-color: #2e4161 !important; color: #f8fafc !important; }
        html.dark .ak-table thead { background-color: #0d1526 !important; border-color: #243248 !important; }
        html.dark .ak-table td { border-color: #243248 !important; }
        html.dark .ak-title, html.dark .ak-stat-value { color: #f8fafc !important; }
        html.dark .ak-label, html.dark .ak-section, html.dark .ak-stat-label { color: #94a3b8 !important; }
        html.dark .ak-back { background-color: #1a273e !important; color: #cbd5e1 !important; }
    </style>

    <!-- Global Helpers & Directives -->
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
            const isCompact = document.documentElement.classList.contains('compact-mode');
            const btns = document.querySelectorAll('.btn-compact-toggle');
            btns.forEach(btn => {
                const label = btn.querySelector('.compact-btn-label');
                const badge = btn.querySelector('.compact-btn-badge');
                if (isCompact) {
                    btn.classList.add('bg-amber-400', 'text-slate-950', 'border-amber-300');
                    btn.classList.remove('bg-white/10', 'text-blue-100', 'border-white/15');
                    if (label) label.textContent = 'Ringkas';
                    if (badge) {
                        badge.textContent = 'ON';
                        badge.className = 'compact-btn-badge px-1 py-0.2 rounded text-[9px] font-black bg-slate-950 text-amber-300';
                    }
                } else {
                    btn.classList.remove('bg-amber-400', 'text-slate-950', 'border-amber-300');
                    btn.classList.add('bg-white/10', 'text-blue-100', 'border-white/15');
                    if (label) label.textContent = 'Ringkas';
                    if (badge) {
                        badge.textContent = 'OFF';
                        badge.className = 'compact-btn-badge px-1 py-0.2 rounded text-[9px] font-bold bg-white/20 text-white';
                    }
                }
            });
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

        // Auto-clean & format numeric inputs
        function isPhoneOrAccountField(target) {
            if (!target) return false;
            if (target.type === 'tel' || target.type === 'password' || target.type === 'email') return true;
            const nameOrId = ((target.name || '') + ' ' + (target.id || '')).toLowerCase();
            return /(^|[^a-z])(telepon|phone|hp|whatsapp|wa|no_?rek|norek|rekening|account)([^a-z]|$)/.test(nameOrId);
        }

        function sanitizeNumberTarget(target) {
            if (!target || target.tagName !== 'INPUT' || target._isSanitizing) return;
            if (isPhoneOrAccountField(target)) return;

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

        document.addEventListener('input', e => sanitizeNumberTarget(e.target), true);
        document.addEventListener('change', e => sanitizeNumberTarget(e.target), true);
        document.addEventListener('blur', e => sanitizeNumberTarget(e.target), true);
        document.addEventListener('focus', e => {
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
<body class="bg-slate-100 text-slate-800 antialiased selection:bg-primary-500 selection:text-white flex flex-col min-h-screen"
      x-data="{ navOpen: false }">

    @php
        $userRole = auth()->user()?->role ?? '';
        $isAdminRole = in_array($userRole, ['admin', 'owner'], true);

        // Ambil daftar cabang yang berhak diakses
        $listWarungs = \App\Models\Kasir::query()
            ->when($userRole === 'kasir', function ($query) {
                $query->where(function ($query) {
                    $query->where('id_user', auth()->id())
                        ->orWhereHas('users', fn ($users) => $users->where('user.id', auth()->id()));
                });
            })
            ->when($isAdminRole, fn ($query) => $query->where('id_user', auth()->id()))
            ->when(!$isAdminRole && $userRole !== 'kasir', fn ($query) => $query->whereRaw('1 = 0'))
            ->orderBy('id')
            ->get(['id', 'nama_warung', 'bank_utama', 'id_user']);

        // Pastikan session cabang valid
        if ($listWarungs->isNotEmpty() && !$listWarungs->contains('id', session('id_warung'))) {
            session(['id_warung' => $listWarungs->first()->id]);
        }

        $cabangAktif = $listWarungs->firstWhere('id', session('id_warung'));
        $isTransaksiActive = request()->routeIs('transaksi.menu*') || request()->routeIs('transaksi.index*') || request()->routeIs('transaksi.setor*') || request()->routeIs('transaksi.tarik*') || request()->routeIs('transaksi.kas.*') || request()->routeIs('transaksi.penjualan-item.*');
    @endphp

    <!-- ==================== HEADER UTAMA ==================== -->
<header class="sticky top-0 z-40 bg-gradient-to-r from-primary-800 via-primary-700 to-primary-600 text-white shadow-lg pt-safe">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">

        <!-- Baris utama -->
        <div class="flex items-center justify-between h-[62px] gap-3">

            <!-- Kiri: hamburger + brand -->
            <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                <button type="button"
                        @click="navOpen = true"
                        class="h-10 w-10 shrink-0 rounded-xl bg-white/10 hover:bg-white/20 active:scale-95 flex items-center justify-center transition border border-white/15 cursor-pointer"
                        aria-label="Buka menu lengkap"
                        title="Buka menu navigasi & master data">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12"/>
                    </svg>
                </button>

                <div class="min-w-0 leading-tight">
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] font-extrabold tracking-[0.14em] text-primary-100 uppercase">AgentKas</span>
                        @if($cabangAktif)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-400/15 text-emerald-200 border border-emerald-300/25">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                {{ $cabangAktif->bank_utama ?? 'BRI' }}
                            </span>
                        @endif
                    </div>
                    <h1 class="mt-0.5 text-[15px] sm:text-base font-extrabold text-white truncate max-w-[140px] sm:max-w-xs">
                        {{ $cabangAktif?->nama_warung ?? 'Pilih Cabang' }}
                    </h1>
                </div>
            </div>

            <!-- Tengah: Navigasi Desktop Terpadu (Tampil di md: / layar komputer & tablet) -->
            <nav class="hidden md:flex items-center gap-1 overflow-x-auto no-scrollbar py-1">
                <!-- Beranda -->
                <a href="{{ route('dashboard') }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white shadow-xs' : 'text-primary-100 hover:text-white hover:bg-white/10' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v4.875h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                    <span>Beranda</span>
                </a>

                <!-- Transaksi -->
                <a href="{{ route('transaksi.menu') }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ ($isTransaksiActive && !request()->routeIs('transaksi.index') && !request()->routeIs('transaksi.penjualan-item.*')) ? 'bg-emerald-500/30 text-emerald-200 border border-emerald-400/40 shadow-xs' : 'text-primary-100 hover:text-white hover:bg-white/10' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                    <span>Transaksi</span>
                </a>

                <!-- Jual Barang -->
                <a href="{{ route('transaksi.penjualan-item.create') }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('transaksi.penjualan-item.*') ? 'bg-purple-500/30 text-purple-200 border border-purple-400/40 shadow-xs' : 'text-primary-100 hover:text-white hover:bg-white/10' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                    <span>Jual Barang</span>
                </a>

                <!-- Riwayat -->
                <a href="{{ route('transaksi.index') }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('transaksi.index') ? 'bg-white/20 text-white shadow-xs' : 'text-primary-100 hover:text-white hover:bg-white/10' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Riwayat</span>
                </a>

                <!-- Kas Harian -->
                <a href="{{ route('laporan-harian.index') }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('laporan-harian.*') ? 'bg-white/20 text-white shadow-xs' : 'text-primary-100 hover:text-white hover:bg-white/10' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Kas Fisik</span>
                </a>

                @if($isAdminRole)
                    <!-- Kas & Bank -->
                    <a href="{{ route('kas.index') }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('kas.*') ? 'bg-white/20 text-white shadow-xs' : 'text-primary-100 hover:text-white hover:bg-white/10' }}">
                        <span>Kas & Bank</span>
                    </a>

                    <!-- Monitoring Cabang -->
                    <a href="{{ route('owner.monitoring') }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-black transition flex items-center gap-1.5 {{ request()->routeIs('owner.monitoring*') ? 'bg-amber-400 text-slate-950 shadow-sm' : 'text-amber-200 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6"/></svg>
                        <span>Monitoring</span>
                    </a>

                    <!-- Laporan -->
                    <a href="{{ route('laporan.index') }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('laporan.index') ? 'bg-white/20 text-white shadow-xs' : 'text-primary-100 hover:text-white hover:bg-white/10' }}">
                        <span>Laporan</span>
                    </a>
                @endif
            </nav>

            <!-- Kanan: ringkas + tema + avatar -->
            <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                <!-- Tombol Ringkas Mode (Header Quick Button) -->
                <button type="button"
                        onclick="toggleCompactMode()"
                        class="btn-compact-toggle hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 active:scale-95 text-blue-100 border border-white/15 cursor-pointer text-xs font-bold transition"
                        title="Mode Ringkas: Sembunyikan teks keterangan agar tampilan lebih minimalis">
                    <svg class="w-3.5 h-3.5 text-amber-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12"/></svg>
                    <span class="compact-btn-label text-[11px] font-bold">Ringkas</span>
                    <span class="compact-btn-badge px-1 py-0.2 rounded text-[9px] font-bold bg-white/20 text-white">OFF</span>
                </button>

                <!-- Tombol Tema Gelap / Terang -->
                <button type="button"
                        onclick="toggleTheme()"
                        class="h-10 w-10 rounded-xl bg-white/10 hover:bg-white/20 active:scale-95 flex items-center justify-center transition border border-white/15 cursor-pointer"
                        aria-label="Ganti tema"
                        title="Ganti Tema Gelap / Terang">
                    <svg class="w-[18px] h-[18px] dark:hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/></svg>
                    <svg class="w-[18px] h-[18px] hidden dark:block text-amber-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/></svg>
                </button>

                <!-- Avatar User -->
                <button type="button"
                        @click="navOpen = true"
                        class="h-10 w-10 rounded-xl bg-white text-primary-700 font-black text-sm flex items-center justify-center shadow-sm active:scale-95 transition cursor-pointer"
                        aria-label="Menu Pengguna"
                        title="Buka menu & pengaturan">
                    {{ strtoupper(mb_substr(auth()->user()->nama ?? auth()->user()->username ?? 'U', 0, 1)) }}
                </button>
            </div>
        </div>

        <!-- Pill pilihan cabang -->
        @if($listWarungs->count() > 1)
            <div class="pb-2.5 pt-2 border-t border-white/10">
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
                    @foreach($listWarungs as $warung)
                        @php $isActive = session('id_warung') == $warung->id; @endphp
                        <form method="POST" action="{{ route('cabang.pilih', $warung->id) }}" class="shrink-0">
                            @csrf
                            <button type="submit"
                                    class="h-8 px-3.5 rounded-full text-xs transition-all inline-flex items-center gap-1.5 cursor-pointer active:scale-95
                                    {{ $isActive
                                        ? 'bg-white text-primary-800 font-bold shadow-sm'
                                        : 'bg-white/10 text-blue-50 hover:bg-white/20 border border-white/15 font-medium' }}">
                                @if($isActive)<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>@endif
                                {{ $warung->nama_warung }}
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</header>

    <!-- ==================== NAV SIDE DRAWER (MENU KIRI ATAS) ==================== -->
    <div x-show="navOpen"
         x-cloak
         class="fixed inset-0 z-50 overflow-hidden"
         role="dialog"
         aria-modal="true">

        <!-- Backdrop Overlay -->
        <div x-show="navOpen"
             x-transition:enter="transition-opacity ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="navOpen = false"
             class="fixed inset-0 bg-slate-950/65 backdrop-blur-xs transition-opacity"></div>

        <!-- Sliding Panel dari Kiri -->
        <div class="fixed inset-y-0 left-0 max-w-full flex pr-10 sm:pr-16">
            <div x-show="navOpen"
                 x-transition:enter="transform transition ease-out duration-200"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in duration-150"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full"
                 class="w-screen max-w-xs sm:max-w-sm bg-white dark:bg-slate-900 shadow-drawer flex flex-col h-full border-r border-slate-200 dark:border-slate-800">

                <!-- Drawer Header -->
                <div class="p-4 sm:p-5 bg-gradient-to-br from-primary-800 via-primary-700 to-primary-600 text-white shrink-0">
                    <div class="flex items-center justify-between pb-3 border-b border-white/10">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-white flex items-center justify-center font-black text-primary-700 text-sm shadow-md">
                                AK
                            </div>
                            <div>
                                <div class="text-xs font-black tracking-wider text-primary-200 uppercase">Navigasi Sistem</div>
                                <div class="text-sm font-black text-white">AgentKas</div>
                            </div>
                        </div>

                        <!-- Close Button -->
                        <button type="button"
                                @click="navOpen = false"
                                class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 active:scale-95 text-slate-300 hover:text-white flex items-center justify-center transition border border-white/10 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- User Info & Active Branch -->
                    <div class="mt-3 flex items-center justify-between">
                        <div class="min-w-0">
                            <div class="text-xs font-black text-white truncate">
                                {{ auth()->user()->nama ?? auth()->user()->username }}
                            </div>
                            <div class="text-[11px] text-slate-300 font-medium truncate">
                                {{ $cabangAktif?->nama_warung ?? 'Semua Cabang' }}
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider {{ $isAdminRole ? 'bg-amber-400 text-slate-950' : 'bg-primary-500 text-white' }}">
                            {{ strtoupper($userRole ?: 'USER') }}
                        </span>
                    </div>

                    <!-- Ringkas & Theme Quick Strip Inside Drawer -->
                    <div class="mt-3.5 pt-3 border-t border-white/10 grid grid-cols-2 gap-2">
                        <button type="button"
                                onclick="toggleCompactMode()"
                                class="btn-compact-toggle px-2.5 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 active:scale-95 text-blue-100 flex items-center justify-center gap-1.5 transition border border-white/15 cursor-pointer text-xs font-bold">
                            <svg class="w-3.5 h-3.5 text-amber-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12"/></svg>
                            <span class="compact-btn-label text-[11px] font-bold">Ringkas</span>
                            <span class="compact-btn-badge px-1 py-0.2 rounded text-[9px] font-bold bg-white/20 text-white">OFF</span>
                        </button>

                        <button type="button"
                                onclick="toggleTheme()"
                                class="px-2.5 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 active:scale-95 text-yellow-300 flex items-center justify-center gap-1.5 transition border border-white/15 cursor-pointer text-xs font-bold">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/></svg>
                            <span class="text-[11px] font-bold text-white">Tema</span>
                        </button>
                    </div>
                </div>

                <!-- Drawer Scrollable Menu List -->
                <div class="flex-1 overflow-y-auto p-3 sm:p-4 space-y-4 no-scrollbar">

                    <!-- SEKSI 1: MENU UTAMA -->
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 px-2 block mb-1">
                            Menu Utama
                        </span>
                        <div class="space-y-1">
                            <!-- Beranda -->
                            <a href="{{ route('dashboard') }}"
                               @click="navOpen = false"
                               class="group flex items-start gap-3 p-2.5 rounded-xl transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary-50 dark:bg-primary-950/40 text-primary-700 dark:text-primary-300 font-bold border border-primary-200/60 dark:border-primary-800/60' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                                <div class="w-8 h-8 rounded-lg bg-primary-100 dark:bg-primary-900/60 text-primary-600 dark:text-primary-400 flex items-center justify-center shrink-0 mt-0.5 group-hover:scale-105 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v4.875h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs sm:text-sm font-black">Beranda</div>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-normal leading-tight sub-explanation mt-0.5">
                                        Ringkasan saldo toko, mutasi shift kasir, dan transaksi terkini.
                                    </p>
                                </div>
                            </a>

                            <!-- Menu Transaksi -->
                            <a href="{{ route('transaksi.menu') }}"
                               @click="navOpen = false"
                               class="group flex items-start gap-3 p-2.5 rounded-xl transition-colors {{ ($isTransaksiActive && !request()->routeIs('transaksi.index') && !request()->routeIs('transaksi.penjualan-item.*')) ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 font-bold border border-emerald-200/60 dark:border-emerald-800/60' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 mt-0.5 group-hover:scale-105 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 6.75h15m-15 5.25h15m-15 5.25h15"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs sm:text-sm font-black">Menu Transaksi</div>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-normal leading-tight sub-explanation mt-0.5">
                                        Setor tunai, tarik tunai, transfer antar bank, dan top up e-wallet.
                                    </p>
                                </div>
                            </a>

                            <!-- Jual Barang -->
                            <a href="{{ route('transaksi.penjualan-item.create') }}"
                               @click="navOpen = false"
                               class="group flex items-start gap-3 p-2.5 rounded-xl transition-colors {{ request()->routeIs('transaksi.penjualan-item.*') ? 'bg-purple-50 dark:bg-purple-950/40 text-purple-800 dark:text-purple-300 font-bold border border-purple-200/60 dark:border-purple-800/60' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                                <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/60 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0 mt-0.5 group-hover:scale-105 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs sm:text-sm font-black">Jual Barang & Pulsa</div>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-normal leading-tight sub-explanation mt-0.5">
                                        Kasir penjualan pulsa, token listrik, materai, dan inventaris toko.
                                    </p>
                                </div>
                            </a>

                            <!-- Riwayat Transaksi -->
                            <a href="{{ route('transaksi.index') }}"
                               @click="navOpen = false"
                               class="group flex items-start gap-3 p-2.5 rounded-xl transition-colors {{ request()->routeIs('transaksi.index') ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-800 dark:text-blue-300 font-bold border border-blue-200/60 dark:border-blue-800/60' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 mt-0.5 group-hover:scale-105 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs sm:text-sm font-black">Riwayat Transaksi</div>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-normal leading-tight sub-explanation mt-0.5">
                                        Histori arus kas masuk & keluar, cetak struk, dan bukti transaksi.
                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- SEKSI 2: KAS & LAPORAN -->
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 px-2 block mb-1">
                            Kas & Pembukuan
                        </span>
                        <div class="space-y-1">
                            <!-- Laporan Kas Fisik Harian -->
                            <a href="{{ route('laporan-harian.index') }}"
                               @click="navOpen = false"
                               class="group flex items-start gap-3 p-2.5 rounded-xl transition-colors {{ request()->routeIs('laporan-harian.*') ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 font-bold border border-emerald-200/60 dark:border-emerald-800/60' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 mt-0.5 group-hover:scale-105 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs sm:text-sm font-black">Laporan Kas Fisik</div>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-normal leading-tight sub-explanation mt-0.5">
                                        Hitung uang tunai di laci kasir saat pergantian shift atau tutup hari.
                                    </p>
                                </div>
                            </a>

                            @if($isAdminRole)
                            <!-- Kas & Rekening Bank -->
                            <a href="{{ route('kas.index') }}"
                               @click="navOpen = false"
                               class="group flex items-start gap-3 p-2.5 rounded-xl transition-colors {{ request()->routeIs('kas.*') ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-800 dark:text-indigo-300 font-bold border border-indigo-200/60 dark:border-indigo-800/60' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                                <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 mt-0.5 group-hover:scale-105 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs sm:text-sm font-black">Kas & Rekening Bank</div>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-normal leading-tight sub-explanation mt-0.5">
                                        Monitoring modal kas tunai, saldo rekening operasional, & e-wallet.
                                    </p>
                                </div>
                            </a>

                            <!-- Transfer Antar Kas -->
                            <a href="{{ route('transfer-kas.index') }}"
                               @click="navOpen = false"
                               class="group flex items-start gap-3 p-2.5 rounded-xl transition-colors {{ request()->routeIs('transfer-kas.*') ? 'bg-cyan-50 dark:bg-cyan-950/40 text-cyan-800 dark:text-cyan-300 font-bold border border-cyan-200/60 dark:border-cyan-800/60' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                                <div class="w-8 h-8 rounded-lg bg-cyan-100 dark:bg-cyan-900/60 text-cyan-600 dark:text-cyan-400 flex items-center justify-center shrink-0 mt-0.5 group-hover:scale-105 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs sm:text-sm font-black">Transfer Antar Kas</div>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-normal leading-tight sub-explanation mt-0.5">
                                        Pencatatan mutasi perpindahan saldo tunai ke rekening bank toko.
                                    </p>
                                </div>
                            </a>

                            <!-- Laporan Periode -->
                            <a href="{{ route('laporan.index') }}"
                               @click="navOpen = false"
                               class="group flex items-start gap-3 p-2.5 rounded-xl transition-colors {{ request()->routeIs('laporan.index') ? 'bg-teal-50 dark:bg-teal-950/40 text-teal-800 dark:text-teal-300 font-bold border border-teal-200/60 dark:border-teal-800/60' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                                <div class="w-8 h-8 rounded-lg bg-teal-100 dark:bg-teal-900/60 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0 mt-0.5 group-hover:scale-105 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs sm:text-sm font-black">Laporan Periode & Laba</div>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-normal leading-tight sub-explanation mt-0.5">
                                        Rekapitulasi keuangan per rentang tanggal, laba kotor, & komisi.
                                    </p>
                                </div>
                            </a>

                            <!-- Pusat Monitoring Semua Cabang -->
                            <a href="{{ route('owner.monitoring') }}"
                               @click="navOpen = false"
                               class="group flex items-start gap-3 p-2.5 rounded-xl transition-colors {{ request()->routeIs('owner.monitoring*') ? 'bg-amber-400/20 text-amber-900 dark:text-amber-200 font-bold border border-amber-300 dark:border-amber-700/60' : 'text-slate-700 dark:text-slate-200 hover:bg-amber-50 dark:hover:bg-slate-800' }}">
                                <div class="w-8 h-8 rounded-lg bg-amber-400 text-slate-950 flex items-center justify-center shrink-0 mt-0.5 group-hover:scale-105 transition-transform font-black">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs sm:text-sm font-black text-amber-950 dark:text-amber-300">Pusat Monitoring Cabang</div>
                                    <p class="text-[11px] text-amber-800 dark:text-amber-400 font-normal leading-tight sub-explanation mt-0.5">
                                        Konsolidasi likuiditas, omset, dan laba seluruh cabang real-time.
                                    </p>
                                </div>
                            </a>
                            @endif
                        </div>
                    </div>

                    @if($isAdminRole)
                    <!-- SEKSI 3: PENGATURAN & MASTER DATA (KHUSUS OWNER / ADMIN) -->
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 px-2 block mb-1">
                            Pengaturan & Master Data
                        </span>
                        <div class="space-y-1">
                            <!-- Kelola Kasir -->
                            <a href="{{ route('manajemen-kasir.index') }}"
                               @click="navOpen = false"
                               class="group flex items-start gap-3 p-2.5 rounded-xl transition-colors {{ request()->routeIs('manajemen-kasir.*') ? 'bg-primary-50 dark:bg-primary-950/40 text-primary-800 dark:text-primary-300 font-bold border border-primary-200/60 dark:border-primary-800/60' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                                <div class="w-8 h-8 rounded-lg bg-primary-100 dark:bg-primary-900/60 text-primary-600 dark:text-primary-400 flex items-center justify-center shrink-0 mt-0.5 group-hover:scale-105 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs sm:text-sm font-black">Kelola Kasir</div>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-normal leading-tight sub-explanation mt-0.5">
                                        Akun login staf kasir, hak akses cabang, & status penugasan.
                                    </p>
                                </div>
                            </a>

                            <!-- Kelola Cabang -->
                            <a href="{{ route('cabang.index') }}"
                               @click="navOpen = false"
                               class="group flex items-start gap-3 p-2.5 rounded-xl transition-colors {{ request()->routeIs('cabang.*') ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 font-bold border border-amber-200/60 dark:border-amber-800/60' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 mt-0.5 group-hover:scale-105 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.25m16.5 0h2.25m-15 0v-7.5A2.25 2.25 0 016 11.25h3a2.25 2.25 0 012.25 2.25V21m-6 0h6"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs sm:text-sm font-black">Kelola Cabang</div>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-normal leading-tight sub-explanation mt-0.5">
                                        Daftar warung outlet agen, pengaturan bank utama, & alamat.
                                    </p>
                                </div>
                            </a>

                            <!-- Biaya Admin -->
                            <a href="{{ route('biaya-admin.index') }}"
                               @click="navOpen = false"
                               class="group flex items-start gap-3 p-2.5 rounded-xl transition-colors {{ request()->routeIs('biaya-admin.*') ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 font-bold border border-emerald-200/60 dark:border-emerald-800/60' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 mt-0.5 group-hover:scale-105 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m4-9H9.5a2.5 2.5 0 000 5h5a2.5 2.5 0 010 5H8"/><circle cx="12" cy="12" r="9"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs sm:text-sm font-black">Biaya Admin</div>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-normal leading-tight sub-explanation mt-0.5">
                                        Tarif biaya admin perbankan, keuntungan agen, & pembagian hasil.
                                    </p>
                                </div>
                            </a>

                            <!-- Kategori Transaksi -->
                            <a href="{{ route('kategori-transaksi.index') }}"
                               @click="navOpen = false"
                               class="group flex items-start gap-3 p-2.5 rounded-xl transition-colors {{ request()->routeIs('kategori-transaksi.*') ? 'bg-sky-50 dark:bg-sky-950/40 text-sky-800 dark:text-sky-300 font-bold border border-sky-200/60 dark:border-sky-800/60' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                                <div class="w-8 h-8 rounded-lg bg-sky-100 dark:bg-sky-900/60 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0 mt-0.5 group-hover:scale-105 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs sm:text-sm font-black">Kategori Transaksi</div>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-normal leading-tight sub-explanation mt-0.5">
                                        Daftar jenis layanan kasir, metode EDC / aplikasi, & mutasi akun.
                                    </p>
                                </div>
                            </a>

                            <!-- Katalog Barang Jualan -->
                            <a href="{{ route('item-jualan.index') }}"
                               @click="navOpen = false"
                               class="group flex items-start gap-3 p-2.5 rounded-xl transition-colors {{ request()->routeIs('item-jualan.*') ? 'bg-purple-50 dark:bg-purple-950/40 text-purple-800 dark:text-purple-300 font-bold border border-purple-200/60 dark:border-purple-800/60' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                                <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/60 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0 mt-0.5 group-hover:scale-105 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l2.4 12.2a2 2 0 002 1.6h8.8a2 2 0 002-1.6L22 8H6M10 21h.01M18 21h.01"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs sm:text-sm font-black">Barang & Jasa Dagangan</div>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-normal leading-tight sub-explanation mt-0.5">
                                        Katalog barang, harga modal, harga jual, dan stok persediaan.
                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>
                    @endif

                </div>

                <!-- Drawer Footer (Logout) -->
                <div class="p-3 sm:p-4 bg-slate-50 dark:bg-slate-950/80 border-t border-slate-200 dark:border-slate-800 shrink-0">
                    <button type="button"
                            onclick="event.preventDefault(); if(confirm('Apakah Anda yakin ingin keluar dari sistem?')) document.getElementById('logout-form').submit();"
                            class="w-full py-2.5 px-3 rounded-xl bg-rose-50 dark:bg-rose-950/30 hover:bg-rose-100 dark:hover:bg-rose-900/50 text-rose-700 dark:text-rose-300 font-bold text-xs sm:text-sm flex items-center justify-center gap-2 transition border border-rose-200 dark:border-rose-900/60 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                        <span>Keluar Akun (Logout)</span>
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- ==================== KONTEN UTAMA ==================== -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-5 pb-28 md:pb-12">
    @yield('content')
</main>

    <!-- ==================== BOTTOM NAVIGATION (KHUSUS MOBILE) ==================== -->
@php
    $bnItem = 'relative flex flex-col items-center justify-end gap-1 pt-2 pb-1 text-[11px] leading-none transition-colors active:scale-95';
    $bnOn   = 'text-primary-700 dark:text-primary-300 font-bold';
    $bnOff  = 'text-slate-500 dark:text-slate-400 font-medium';
    $pillOn  = 'bg-primary-100 dark:bg-primary-900/60';
    $pillOff = 'bg-transparent';
    $bnTransaksiOn = $isTransaksiActive && !request()->routeIs('transaksi.index');
@endphp

<nav aria-label="Menu Navigasi Bawah"
     class="md:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-t border-slate-200 dark:border-slate-800 shadow-mobile-nav pb-safe">
    <div class="mx-auto max-w-xl grid grid-cols-5 items-end px-2 h-[64px]">

        <!-- Beranda -->
        @php $on = request()->routeIs('dashboard'); @endphp
        <a href="{{ route('dashboard') }}" class="{{ $bnItem }} {{ $on ? $bnOn : $bnOff }}">
            <span class="h-7 w-14 rounded-full flex items-center justify-center transition-colors {{ $on ? $pillOn : $pillOff }}">
                <svg class="h-[22px] w-[22px]" fill="none" stroke="currentColor" stroke-width="{{ $on ? 2.2 : 1.8 }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v4.875h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
            </span>
            <span>Beranda</span>
        </a>

        <!-- Riwayat -->
        @php $on = request()->routeIs('transaksi.index'); @endphp
        <a href="{{ route('transaksi.index') }}" class="{{ $bnItem }} {{ $on ? $bnOn : $bnOff }}">
            <span class="h-7 w-14 rounded-full flex items-center justify-center transition-colors {{ $on ? $pillOn : $pillOff }}">
                <svg class="h-[22px] w-[22px]" fill="none" stroke="currentColor" stroke-width="{{ $on ? 2.2 : 1.8 }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </span>
            <span>Riwayat</span>
        </a>

        <!-- Transaksi (tombol tengah menonjol) -->
        <a href="{{ route('transaksi.menu') }}" class="relative flex flex-col items-center justify-end gap-1 pb-1 text-[11px] leading-none active:scale-95 transition {{ $bnTransaksiOn ? $bnOn : $bnOff }}">
            <span class="-mt-7 h-14 w-14 rounded-full flex items-center justify-center text-white bg-gradient-to-br from-primary-500 to-primary-700 shadow-lg shadow-primary-700/40 ring-4 ring-white dark:ring-slate-900 {{ $bnTransaksiOn ? 'scale-105' : '' }} transition-transform">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
            </span>
            <span>Transaksi</span>
        </a>

        <!-- Kas Harian -->
        @php $on = request()->routeIs('laporan-harian.*'); @endphp
        <a href="{{ route('laporan-harian.index') }}" class="{{ $bnItem }} {{ $on ? $bnOn : $bnOff }}">
            <span class="h-7 w-14 rounded-full flex items-center justify-center transition-colors {{ $on ? $pillOn : $pillOff }}">
                <svg class="h-[22px] w-[22px]" fill="none" stroke="currentColor" stroke-width="{{ $on ? 2.2 : 1.8 }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5h16.5a1.5 1.5 0 011.5 1.5v9.75a1.5 1.5 0 01-1.5 1.5H3.75a1.5 1.5 0 01-1.5-1.5V6a1.5 1.5 0 011.5-1.5z"/></svg>
            </span>
            <span>Kas Harian</span>
        </a>

        <!-- Menu (buka drawer) -->
        <button type="button" @click="navOpen = true" class="{{ $bnItem }} {{ $bnOff }} cursor-pointer">
            <span class="h-7 w-14 rounded-full flex items-center justify-center">
                <svg class="h-[22px] w-[22px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
            </span>
            <span>Menu</span>
        </button>
    </div>
</nav>

    <!-- Hidden Form Logout -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

</body>
</html>
