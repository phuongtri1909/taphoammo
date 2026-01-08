<!DOCTYPE html>
<html lang="vi">

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
        
        try {
            if (isset($seoSetting) && $seoSetting && method_exists($seoSetting, 'getTranslation')) {
                $seoTitle = $seoSetting->getTranslation('title', $currentLocale) ?: ($seoSetting->getTranslation('title', 'vi') ?: $seoTitle);
                $seoDescription = $seoSetting->getTranslation('description', $currentLocale) ?: ($seoSetting->getTranslation('description', 'vi') ?: $seoDescription);
                $seoKeywords = $seoSetting->getTranslation('keywords', $currentLocale) ?: ($seoSetting->getTranslation('keywords', 'vi') ?: $seoKeywords);
                if (isset($seoSetting->thumbnail_url)) {
                    $seoThumbnail = $seoSetting->thumbnail_url;
                }
            } elseif (isset($seoData) && $seoData) {
                $seoTitle = $seoData->title ?? $seoTitle;
                $seoDescription = $seoData->description ?? $seoDescription;
                $seoKeywords = $seoData->keywords ?? $seoKeywords;
                $seoThumbnail = $seoData->thumbnail ?? $seoThumbnail;
            }
        } catch (\Exception $e) {
            
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

    @yield('content')
    
    @include('components.contact_widget')
    @include('components.top_button')

    @include('client.layouts.partials.footer')

    @stack('scripts')
</body>

</html>
