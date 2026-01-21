<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="author" content="{{ config('app.name') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->full() }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:locale" content="vi_VN">

    {!! SEO::generate() !!}

    @stack('custom_schema')

    <link rel="icon" href="{{ $faviconPath }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ $faviconPath }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ $faviconPath }}">

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="canonical" href="{{ url()->current() }}">

    <meta name="google-site-verification" content="" />
    @verbatim
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Organization",
            "url": "{{ url('/') }}",
            "logo": "{{ asset('/images/logo/Logo-site-1050-x-300.webp') }}"
        }
        </script>
    @endverbatim

    @stack('meta')

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">

    {{-- styles --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    @vite('resources/assets/frontend/css/styles.css')

    @stack('styles')

    {{-- end styles --}}
</head>

<body>
    @include('client.layouts.partials.header')

    @include('components.sweetalert')
    @include('components.toast-main')
    @include('components.toast')

    @include('components.auction-banners')

    @yield('content')
    
    @include('components.contact_widget')
    @include('components.top_button')
    @include('components.terms-popup')

    @include('client.layouts.partials.footer')

    @stack('scripts')
</body>

</html>
