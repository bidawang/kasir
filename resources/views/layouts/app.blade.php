<!doctype html>
<html lang="id" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>@yield('title', 'BRILINK')</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    {{-- Alpine.js (Global) --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root {
            --app-primary: #00529c; /* Warna Khas BRI */
            --app-bg: #f8f9fa;
        }

        body {
            background-color: var(--app-bg);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        /* Card Style Modern */
        .app-card {
            border-radius: 16px;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            background: #fff;
        }

        /* Bottom Navigation Mobile */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(0, 0, 0, 0.08);
            padding-bottom: env(safe-area-inset-bottom); /* Support iPhone notch */
        }

        .bottom-nav .nav-link {
            color: #6c757d;
            font-size: 0.7rem;
            padding: 8px 0;
            transition: 0.2s;
        }

        .bottom-nav .nav-link i {
            font-size: 1.3rem;
            margin-bottom: 2px;
            display: block;
        }

        .bottom-nav .active {
            color: var(--app-primary) !important;
            font-weight: 600;
        }

        /* Spacing Konten agar tidak tertutup nav bawah */
        main.container-main {
            padding-bottom: 85px; 
            min-height: 100vh;
        }

        /* Desktop Navbar Enhancements */
        .navbar-desktop .active {
            border-bottom: 2px solid var(--app-primary);
            color: var(--app-primary) !important;
        }

        /* Utility */
        .bg-primary-soft { background-color: rgba(0, 82, 156, 0.1); }
        .text-primary-app { color: var(--app-primary); }
    </style>

    @stack('styles')
</head>

<body>

    {{-- Navbar (Desktop) --}}
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top d-none d-lg-block navbar-desktop">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary-app" href="{{ route('dashboard') }}">
                <i class="bi bi-bank2 me-2"></i>BRILINK
            </a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link px-3 @if(request()->routeIs('dashboard')) active @endif">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('transaksi.index') }}" class="nav-link px-3 @if(request()->routeIs('transaksi.index')) active @endif">Riwayat</a>
                    </li>
                    @if(Auth::user()->role === 'admin')
                    <li class="nav-item">
                        <a href="{{ route('kas.index') }}" class="nav-link px-3 @if(request()->routeIs('kas.*')) active @endif">Kelola Kas</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('laporan.index') }}" class="nav-link px-3 @if(request()->routeIs('laporan.*')) active @endif">Laporan</a>
                    </li>
                    @endif
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('transaksi.create') }}" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-plus-lg me-1"></i> Transaksi Baru
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-light rounded-circle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li><h6 class="dropdown-header">{{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})</h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    {{-- Header (Mobile) --}}
    <nav class="navbar navbar-light bg-white border-bottom sticky-top d-lg-none py-2">
        <div class="container-fluid">
            <span class="navbar-brand fw-bold text-primary-app">
                <i class="bi bi-bank2 me-1"></i> BRILINK
            </span>
            <a href="{{ route('transaksi.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                <i class="bi bi-plus-lg"></i> Baru
            </a>
        </div>
    </nav>

    {{-- Konten Utama --}}
    <main class="container-main container py-3">
        @yield('content')
    </main>

    {{-- Bottom Nav (Mobile Only) --}}
    <nav class="bottom-nav d-lg-none">
        <div class="d-flex justify-content-around align-items-center">
            <a href="{{ route('dashboard') }}" class="nav-link text-center flex-fill @if(request()->routeIs('dashboard')) active @endif">
                <i class="bi bi-house-door"></i>
                <span>Beranda</span>
            </a>
            
            <a href="{{ route('transaksi.index') }}" class="nav-link text-center flex-fill @if(request()->routeIs('transaksi.index')) active @endif">
                <i class="bi bi-clock-history"></i>
                <span>Riwayat</span>
            </a>

            @if(Auth::user()->role === 'admin')
            <a href="{{ route('kas.index') }}" class="nav-link text-center flex-fill @if(request()->routeIs('kas.*')) active @endif">
                <i class="bi bi-wallet2"></i>
                <span>Kas</span>
            </a>
            <a href="{{ route('laporan.index') }}" class="nav-link text-center flex-fill @if(request()->routeIs('laporan.*')) active @endif">
                <i class="bi bi-bar-chart"></i>
                <span>Laporan</span>
            </a>
            @endif

            <a href="#" class="nav-link text-center flex-fill text-danger" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i>
                <span>Keluar</span>
            </a>
        </div>
    </nav>

    {{-- Hidden Logout Form --}}
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    {{-- Footer (Desktop Only) --}}
    <footer class="bg-white border-top d-none d-lg-block">
        <div class="container py-4 text-center text-secondary small">
            &copy; {{ now()->year }} <span class="fw-bold">BRILINK System</span>. Semua Hak Dilindungi.
        </div>
    </footer>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>

</html>