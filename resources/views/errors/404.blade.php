<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>404 - Page Not Found</title>
    <meta name="description" content="404 - Page Not Found">
    <link rel="icon" href="{{ $faviconPath }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ $faviconPath }}" type="image/x-icon">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            height: 100vh;
            overflow: hidden;
        }

        .error-container {
            background-image: url('{{ asset('assets/images/dev/bg-error-page.webp') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            width: 100%;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            flex-direction: column;
        }

        .title-error {
            background: linear-gradient(180deg, rgba(57, 75, 155, 0.45) 19.79%, rgba(215, 211, 219, 0.20) 194.53%);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: clamp(120px, 25vw, 300px);
            font-style: normal;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            margin: 0;
            animation: fadeInUp 1.2s ease-out forwards, float 3s ease-in-out infinite 1.5s;
            transform: translateY(50px);
            opacity: 0;
        }

        .button-back-to-home {
            color: #000;
            border-radius: 20px;
            padding: 12px 24px;
            text-decoration: none;
            background: #fff;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            animation: fadeInUp 1.2s ease-out 0.5s forwards, pulse 2s ease-in-out infinite 2s;
            transform: translateY(30px);
            opacity: 0;
            display: inline-block;
        }

        .button-back-to-home:hover {
            background: #000;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }
            100% {
                background-position: 200% 0;
            }
        }

        /* Thêm hiệu ứng shimmer cho text 404 */
        .title-error::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            background-size: 200% 100%;
            animation: shimmer 3s ease-in-out infinite 2s;
            border-radius: inherit;
        }

        /* Responsive cho mobile */
        @media (max-width: 768px) {
            .title-error {
                font-size: clamp(80px, 20vw, 200px);
            }
        }

        @media (max-width: 480px) {
            .title-error {
                font-size: clamp(60px, 15vw, 150px);
            }
        }
    </style>
</head>

<body>
    <div class="error-container">
        <h1 class="title-error">404</h1>
        <a href="{{ route('home') }}" class="button-back-to-home">{{ __('Back to home') }}</a>
    </div>
</body>

</html>
