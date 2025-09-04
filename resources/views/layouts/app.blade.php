<!doctype html>
<html lang="id" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'BRILINK')</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
        }

        .app-card {
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, .08);
            background: var(--bs-body-bg);
            transition: .2s;
        }

        .app-card:hover {
            box-shadow: 0 6px 16px rgba(0, 0, 0, .08);
        }

        .bottom-nav {
            position: sticky;
            bottom: 0;
            z-index: 1030;
            background: var(--bs-body-bg);
            border-top: 1px solid rgba(0, 0, 0, .08);
        }

        /* CSS untuk menu aktif */
        .bottom-nav .active {
            color: var(--bs-primary) !important;
            font-weight: 500;
        }

        .navbar-desktop .active {
            font-weight: 500;
            color: var(--bs-body-emphasis) !important;
        }
    </style>

    @stack('styles')
</head>

<body>

    {{-- Navbar (Desktop) --}}
    <nav class="navbar navbar-light bg-body border-bottom sticky-top d-none d-lg-block navbar-desktop">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">
                <i class="bi bi-cash-stack me-1"></i> BRILINK
            </span>
            <div class="d-flex gap-3">
                <a href="{{ route('dashboard') }}" class="btn btn-sm text-decoration-none text-secondary @if(request()->routeIs('dashboard')) active @endif">
                    <i class="bi bi-house me-1"></i> Beranda
                </a>
                <a href="{{ route('transaksi.index') }}" class="btn btn-sm text-decoration-none text-secondary @if(request()->routeIs('transaksi.*')) active @endif">
                    <i class="bi bi-list-ul me-1"></i> Riwayat
                </a>
                <a href="{{ route('laporan.index') }}" class="btn btn-sm text-decoration-none text-secondary @if(request()->routeIs('laporan.index')) active @endif">
                    <i class="bi bi-bar-chart me-1"></i> Laporan
                </a>
                <a href="{{ route('transaksi.create') }}" class="btn btn-sm btn-success">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Transaksi
                </a>
            </div>
        </div>
    </nav>

    {{-- Navbar (Mobile) --}}
    <nav class="navbar navbar-light bg-body border-bottom sticky-top d-lg-none">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">
                <i class="bi bi-cash-stack me-1"></i> BRILINK
            </span>
            <a href="{{ route('transaksi.create') }}" class="btn btn-sm btn-success">
                <i class="bi bi-plus-lg"></i> Transaksi
            </a>
        </div>
    </nav>

    {{-- Konten utama --}}
    <main class="container my-3">
        @yield('content')
    </main>

    {{-- Bottom nav (mobile) --}}
    <nav class="bottom-nav d-lg-none">
        <div class="container-fluid">
            <div class="d-flex justify-content-around py-2">
                <a href="{{ route('dashboard') }}" class="text-center text-decoration-none text-body-emphasis @if(request()->routeIs('dashboard')) active @endif">
                    <i class="bi bi-house fs-5 d-block"></i>
                    <small>Beranda</small>
                </a>
                <a href="{{ route('transaksi.index') }}" class="text-center text-decoration-none text-body-emphasis @if(request()->routeIs('transaksi.index')) active @endif">
                    <i class="bi bi-list-ul fs-5 d-block"></i>
                    <small>Riwayat</small>
                </a>
                <a href="{{ route('laporan.index') }}" class="text-center text-decoration-none text-body-emphasis @if(request()->routeIs('laporan.index')) active @endif">
                    <i class="bi bi-bar-chart fs-5 d-block"></i>
                    <small>Laporan</small>
                </a>
            </div>
        </div>
    </nav>

    {{-- Footer --}}
    <footer class="border-top mt-4">
        <div class="container py-3 text-center small text-secondary">
            © {{ now()->year }} BRILINK
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>