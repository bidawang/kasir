<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - BRILINK</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }
        .auth-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .app-card {
            max-width: 420px;
            width: 100%;
            border-radius: 12px;
            border: 1px solid rgba(0,0,0,.08);
            background: #fff;
            padding: 2rem;
            box-shadow: 0 4px 10px rgba(0,0,0,.05);
        }
        .app-card h4 {
            font-weight: 600;
        }
        .form-label {
            font-size: .875rem;
            font-weight: 500;
        }
        .toggle-password {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container auth-container">
        <div class="app-card">
            <!-- Header -->
            <div class="text-center mb-4">
                <h4 class="fw-bold">Daftar</h4>
                <p class="text-secondary small">Buat akun baru untuk melanjutkan</p>
            </div>

            <!-- Form Register -->
            <form method="POST" action="#">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="nama" name="nama" required autofocus>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Alamat Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <div class="mb-3 position-relative">
                    <label for="password" class="form-label">Kata Sandi</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" required>
                        <span class="input-group-text toggle-password" data-target="password">
                            <i class="bi bi-eye"></i>
                        </span>
                    </div>
                </div>

                <div class="mb-3 position-relative">
                    <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        <span class="input-group-text toggle-password" data-target="password_confirmation">
                            <i class="bi bi-eye"></i>
                        </span>
                    </div>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary">Daftar</button>
                </div>
            </form>

            <!-- Link login -->
            <div class="text-center small">
                Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Toggle Password -->
    <script>
        document.querySelectorAll('.toggle-password').forEach(el => {
            el.addEventListener('click', function () {
                const target = document.getElementById(this.dataset.target);
                const icon = this.querySelector('i');
                if (target.type === "password") {
                    target.type = "text";
                    icon.classList.replace("bi-eye", "bi-eye-slash");
                } else {
                    target.type = "password";
                    icon.classList.replace("bi-eye-slash", "bi-eye");
                }
            });
        });
    </script>
</body>
</html>
