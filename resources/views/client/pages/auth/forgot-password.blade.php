<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Quên mật khẩu - Nap PLUS</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    @vite('resources/assets/frontend/css/styles.css')
    @vite('resources/assets/frontend/css/auth.css')

</head>
<body class="login-page-body">
    <div class="login-page-container">
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <div class="logo-wrapper">
                        <img src="{{ asset('images/logo/Logo-site-1050-x-300.webp') }}" alt="{{ config('app.name') }}" class="img-fluid logo-site">
                    </div>
                    <h2 class="login-title">Quên mật khẩu</h2>
                    <p class="login-subtitle">Nhập email để nhận link đặt lại mật khẩu</p>
                </div>

                <div class="login-body">
                    @include('components.toast-main')
                    @include('components.toast')

                    <form method="POST" action="{{ route('forgot-password.post') }}" class="login-form">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control login-input @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Nhập email của bạn" required autofocus>
                            @error('email')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-login">
                                Gửi link đặt lại mật khẩu
                            </button>
                        </div>
                    </form>

                    <div class="login-footer text-center mt-4">
                        <p class="register-text">
                            <a href="{{ route('sign-in') }}" class="register-link">Quay lại đăng nhập</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>



