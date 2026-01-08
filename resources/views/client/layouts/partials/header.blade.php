<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    @php
        $currentLocale = app()->getLocale();
        $seoTitle = 'Home - ' . config('app.name');
        $seoDescription = config('app.name');
        $seoKeywords = config('app.name') . ',thu vien';
        $seoThumbnail = asset('images/dev/Thumbnail.png');
        
        if (isset($seoSetting) && $seoSetting) {
            $seoTitle = $seoSetting->getTranslation('title', $currentLocale) ?: $seoSetting->getTranslation('title', 'vi');
            $seoDescription = $seoSetting->getTranslation('description', $currentLocale) ?: $seoSetting->getTranslation('description', 'vi');
            $seoKeywords = $seoSetting->getTranslation('keywords', $currentLocale) ?: $seoSetting->getTranslation('keywords', 'vi');
            $seoThumbnail = $seoSetting->thumbnail_url;
        } elseif (isset($seoData) && $seoData) {
            $seoTitle = $seoData->title;
            $seoDescription = $seoData->description;
            $seoKeywords = $seoData->keywords;
            $seoThumbnail = $seoData->thumbnail;
        }
    @endphp

    <title>@if($seoTitle){{ $seoTitle }}@elseif(@hasSection('title'))@yield('title')@else Home - {{ config('app.name') }} @endif</title>
    <meta name="description" content="@if($seoDescription){{ $seoDescription }}@elseif(@hasSection('description'))@yield('description')@else {{ config('app.name') }} @endif">
    <meta name="keywords" content="@if($seoKeywords){{ $seoKeywords }}@elseif(@hasSection('keywords'))@yield('keywords')@else {{ config('app.name') }},park @endif">
    <meta name="author" content="{{ config('app.name') }}">
    <meta name="robots" content="noindex, nofollow">
    <meta property="og:type" content="website">
    <meta property="og:title" content="@if($seoTitle){{ $seoTitle }}@elseif(@hasSection('title'))@yield('title')@else Home - {{ config('app.name') }} @endif">
    <meta property="og:description" content="@if($seoDescription){{ $seoDescription }}@elseif(@hasSection('description'))@yield('description')@else {{ config('app.name') }} @endif">
    <meta property="og:url" content="{{ url()->full() }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:locale" content="vi_VN">
    <meta property="og:image" content="{{ $seoThumbnail }}">
    <meta property="og:image:secure_url" content="{{ $seoThumbnail }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="@if($seoTitle){{ $seoTitle }}@elseif(@hasSection('title'))@yield('title')@else Home - {{ config('app.name') }} @endif">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@if($seoTitle){{ $seoTitle }}@elseif(@hasSection('title'))@yield('title')@else Home - {{ config('app.name') }} @endif">
    <meta name="twitter:description" content="@if($seoDescription){{ $seoDescription }}@elseif(@hasSection('description'))@yield('description')@else {{ config('app.name') }} @endif">
    <meta name="twitter:image" content="{{ $seoThumbnail }}">
    <meta name="twitter:image:alt" content="@if($seoTitle){{ $seoTitle }}@elseif(@hasSection('title'))@yield('title')@else Home - {{ config('app.name') }} @endif">
    <link rel="icon" href="{{ $faviconPath }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ $faviconPath }}" type="image/x-icon">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="canonical" href="{{ url()->current() }}">

    <meta name="google-site-verification" content="" />
    @verbatim
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Organization",
            "url": "{{ url('/') }}",
            "logo": "{{ asset('/images/dev/Thumbnail.png') }}"
        }
        </script>
    @endverbatim

    @stack('meta')

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->

    {{-- styles --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    @vite('resources/assets/frontend/css/styles.css')

    @stack('styles')

    {{-- end styles --}}
</head>

<body>
    <header class="header-main" id="header">
        <div class="container mt-4">
            <div class="header-custom">
                <!-- Logo -->
                <div class="d-flex align-items-center">
                    <img src="{{ $logoPath }}" alt="Logo" style="margin-right: 20px;" height="30px">
                    <div class="header-nav d-none d-xl-flex align-items-end">
                        <a href="{{ route('home') }}"
                            class="text-sm fw-medium {{ Route::currentRouteNamed('home') ? 'active' : '' }}">{{ __('home') }}
                        </a>
                    </div>
                </div>

                <div class="d-xl-none">
                    <button class="btn border rounded-circle bg-white" id="mobileMenuToggle">
                        <img src="{{ asset('/images/svg/menu.svg') }}" alt="">
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Side Menu Overlay -->
    <div class="mobile-side-menu-overlay" id="mobileMenuOverlay"></div>

    <!-- Mobile Side Menu -->
    <div class="mobile-side-menu" id="mobileSideMenu">
        <div class="mobile-menu-header">
            <img src="{{ $logoPath }}" alt="Logo" height="40px">
            <button class="mobile-menu-close" id="mobileMenuClose">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <ul class="mobile-nav-list">
            <li><a href="{{ route('home') }}"
                    class="text-md fw-medium {{ Route::currentRouteNamed('home') ? 'active' : '' }}">{{ __('home') }}</a>
            </li>
        </ul>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const mobileSideMenu = document.getElementById('mobileSideMenu');
            const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
            const mobileMenuClose = document.getElementById('mobileMenuClose');

            // Open mobile menu
            mobileMenuToggle.addEventListener('click', function() {
                mobileSideMenu.classList.add('active');
                mobileMenuOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            });

            // Close mobile menu
            function closeMobileMenu() {
                mobileSideMenu.classList.remove('active');
                mobileMenuOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }

            mobileMenuClose.addEventListener('click', closeMobileMenu);
            mobileMenuOverlay.addEventListener('click', closeMobileMenu);

            // Close menu when clicking on navigation links
            const mobileNavLinks = document.querySelectorAll('.mobile-nav-list a');
            mobileNavLinks.forEach(link => {
                link.addEventListener('click', closeMobileMenu);
            });

            // Close menu on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeMobileMenu();
                }
            });
        });
    </script>
