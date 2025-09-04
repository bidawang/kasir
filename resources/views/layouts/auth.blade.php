<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .auth-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .app-card {
            max-width: 450px;
            width: 100%;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="container auth-container">
        @yield('content')
    </div>
</body>
</html>
