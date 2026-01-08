<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Auth') - {{ config('app.name') }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    @vite('resources/assets/frontend/css/styles.css')
    @vite('resources/assets/frontend/css/auth.css')

    @stack('styles')
</head>
<body class="min-h-screen bg-white relative">
    <div class="relative min-h-screen flex items-center justify-center p-8 z-10 py-12">
        <div class="w-full max-w-[450px] relative">
            <div class="rounded-3xl shadow-md p-10 md:p-8 sm:p-6 relative z-10 bg-white my-8">
                <div class="text-center mb-8">
                    <div class="flex items-center justify-center gap-3 mb-6">
                        <img src="{{ asset('images/logo/Logo-site-1050-x-300.webp') }}" alt="{{ config('app.name') }}" class="w-full h-[100px] object-contain">
                    </div>
                    @yield('header')
                </div>

                <div class="mt-8">
                    @include('components.toast-main')
                    @include('components.toast')

                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function togglePassword(inputId) {
            inputId = inputId || 'password';
            const passwordInput = document.getElementById(inputId);
            const toggleBtn = document.getElementById(inputId === 'password' ? 'password-toggle-btn' : (inputId === 'password_confirmation' ? 'password-confirmation-toggle-btn' : 'password-toggle-btn'));
            if (!toggleBtn) return;
            
            const showIcon = toggleBtn.querySelector('.password-icon-show');
            const hideIcon = toggleBtn.querySelector('.password-icon-hide');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                if (showIcon) showIcon.style.display = 'none';
                if (hideIcon) hideIcon.style.display = 'block';
            } else {
                passwordInput.type = 'password';
                if (showIcon) showIcon.style.display = 'block';
                if (hideIcon) hideIcon.style.display = 'none';
            }
        }
    </script>
    @stack('scripts')
</body>
</html>

